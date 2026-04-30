<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryPurchase;
use App\Models\InventoryPurchaseItem;
use App\Models\InventoryPurchasePayment;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class InventoryPurchaseController extends Controller
{
    private function calculatePurchaseTotals(array $products): array
    {
        $totalAmount = 0;

        foreach ($products as $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['price'] ?? 0);
            $totalAmount += $quantity * $price;
        }

        return [
            'total_amount' => round($totalAmount, 2),
        ];
    }

    private function validatePaymentAmount(Request $request): array
    {
        return $this->buildPaymentSummary($request->products ?? [], (float) ($request->paid_amount ?? 0));
    }

    private function buildPaymentSummary(array $products, float $paidAmount): array
    {
        $totals = $this->calculatePurchaseTotals($products);
        $paidAmount = round($paidAmount, 2);

        if ($paidAmount > $totals['total_amount']) {
            throw ValidationException::withMessages([
                'paid_amount' => 'Paid amount cannot be greater than Grand Total.',
            ]);
        }

        return [
            'total_amount' => $totals['total_amount'],
            'paid_amount' => $paidAmount,
            'due_amount' => round($totals['total_amount'] - $paidAmount, 2),
        ];
    }

    private function ensureStatusColumnSupportsWorkflow(): void
    {
        DB::statement("UPDATE inventory_purchases SET status = 'confirm' WHERE status = 'confirmed'");
        DB::statement("ALTER TABLE inventory_purchases MODIFY status ENUM('pending', 'confirm', 'received') NOT NULL DEFAULT 'pending'");
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = InventoryPurchase::with('vendor')->latest();

            if ($request->filled('purchase_number')) {
                $query->where('purchase_number', 'like', '%' . $request->purchase_number . '%');
            }

            if ($request->filled('vendor_id')) {
                $query->where('vendor_id', $request->vendor_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('purchase_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('purchase_date', '<=', $request->date_to);
            }

            $summary = [
                'total_amount' => number_format((float) (clone $query)->reorder()->sum('total_amount'), 2),
                'paid_amount' => number_format((float) (clone $query)->reorder()->sum('paid_amount'), 2),
                'due_amount' => number_format((float) (clone $query)->reorder()->sum('due_amount'), 2),
            ];

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('purchase_date', fn($r) => $r->purchase_date ? $r->purchase_date->format('d M, Y') : '-')
                ->addColumn('vendor_name', function ($r) {
                    $phone = $r->vendor->phone ?? '-';
                    return ($r->vendor->name ?? '-') . '<br><small>' . $phone . '</small>';
                })
                ->addColumn('paid_amount', fn($r) => number_format($r->paid_amount ?? 0, 2))
                ->addColumn('due_amount', fn($r) => number_format($r->due_amount ?? 0, 2))
                ->addColumn('status_badge', function($r) {
                    $class = [
                        'pending' => 'warning',
                        'confirm' => 'info',
                        'received' => 'success',
                    ][$r->status] ?? 'secondary';
                    return '<span class="badge badge-light-'.$class.' text-uppercase">'.$r->status.'</span>';
                })
                ->addColumn('action', function($r) {
                    $btn = '<div class="d-flex flex-wrap">';
                    $btn .= '<a href="'.route('admin.inventory-purchase.show', $r->id).'" class="btn btn-info btn-sm mr-1 mb-25"><i data-feather="eye"></i> Details</a>';
                    if ($r->due_amount > 0) {
                        $btn .= '<button type="button" class="btn btn-success btn-sm mr-1 mb-25 payPurchaseBtn"
                            data-id="'.$r->id.'"
                            data-purchase-number="'.$r->purchase_number.'"
                            data-vendor-name="'.e($r->vendor->name ?? '-').'"
                            data-due="'.$r->due_amount.'"><i data-feather="dollar-sign"></i> Pay</button>';
                    }
                    $btn .= '<button type="button" class="btn btn-outline-info btn-sm mr-1 mb-25 paymentHistoryBtn"
                        data-id="'.$r->id.'"
                        data-purchase-number="'.$r->purchase_number.'"><i data-feather="clock"></i> History</button>';
                    if ($r->status == 'pending') {
                        $btn .= '<a href="'.route('admin.inventory-purchase.edit', $r->id).'" class="btn btn-primary btn-sm mr-1 mb-25"><i data-feather="edit"></i></a>';
                    }
                    if(in_array($r->status, ['confirm'])) {
                        $btn .= '<a href="'.route('admin.inventory-purchase.receive', $r->id).'" class="btn btn-success btn-sm mr-1 mb-25"><i data-feather="download"></i> Receive</a>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['vendor_name', 'status_badge', 'action'])
                ->with('summary', $summary)
                ->make(true);
        }

        $vendors = Vendor::orderBy('name')->get();
        return view('admin.inventory_purchase.index', compact('vendors'));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('name')->get();
        $products = Product::where('status', 1)->get();
        $purchase_number = InventoryPurchase::generatePurchaseNumber();
        return view('admin.inventory_purchase.create', compact('vendors', 'products', 'purchase_number'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required',
            'purchase_date' => 'required|date',
            'products' => 'required|array',
            'products.*.product_id' => 'required',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        $payment = $this->validatePaymentAmount($request);

        try {
            DB::beginTransaction();

            $purchase = InventoryPurchase::create([
                'purchase_number' => InventoryPurchase::generatePurchaseNumber(),
                'vendor_id' => $request->vendor_id,
                'purchase_date' => $request->purchase_date,
                'status' => 'pending',
                'total_amount' => $payment['total_amount'],
                'paid_amount' => $payment['paid_amount'],
                'due_amount' => $payment['due_amount'],
                'note' => $request->note,
                'created_by' => auth()->id()
            ]);

            if ($payment['paid_amount'] > 0) {
                InventoryPurchasePayment::create([
                    'inventory_purchase_id' => $purchase->id,
                    'vendor_id' => $purchase->vendor_id,
                    'amount' => $payment['paid_amount'],
                    'payment_date' => now()->toDateString(),
                    'payment_method' => $request->payment_method ?? 'Cash',
                    'note' => 'Initial payment for Purchase #' . $purchase->purchase_number,
                    'created_by' => auth()->id(),
                ]);
            }

            foreach ($request->products as $item) {
                InventoryPurchaseItem::create([
                    'inventory_purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['price'],
                ]);
            }

            DB::commit();
            return response()->json(['success' => 'Purchase order created successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $purchase = InventoryPurchase::with('items.product')->findOrFail($id);
        $vendors = Vendor::orderBy('name')->get();
        $products = Product::where('status', 1)->get();
        return view('admin.inventory_purchase.edit', compact('purchase', 'vendors', 'products'));
    }

    public function update(Request $request, $id)
    {
        $purchase = InventoryPurchase::findOrFail($id);
        if($purchase->status === 'received'){
            return response()->json(['error' => 'Received purchase cannot be edited.'], 422);
        }

        $request->validate([
            'vendor_id' => 'required',
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,confirm,received',
            'products' => 'required|array',
            'products.*.product_id' => 'required',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        $payment = $this->buildPaymentSummary($request->products ?? [], (float) $purchase->paid_amount);

        try {
            $this->ensureStatusColumnSupportsWorkflow();
            DB::beginTransaction();

            $purchase->update([
                'vendor_id' => $request->vendor_id,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $payment['total_amount'],
                'paid_amount' => $payment['paid_amount'],
                'due_amount' => $payment['due_amount'],
                'note' => $request->note,
                'status' => $request->status,
            ]);

            $purchase->items()->delete();
            foreach ($request->products as $item) {
                InventoryPurchaseItem::create([
                    'inventory_purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['price'],
                ]);
            }

            DB::commit();
            return response()->json(['success' => 'Purchase order updated successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function receive($id)
    {
        $purchase = InventoryPurchase::with('items.product')->findOrFail($id);
        if($purchase->status === 'received'){
            return redirect()->route('admin.inventory-purchase.index')->with('error', 'Already received.');
        }
        return view('admin.inventory_purchase.receive', compact('purchase'));
    }

    public function receiveStore(Request $request, $id)
    {
        $purchase = InventoryPurchase::with('items')->findOrFail($id);

        try {
            $this->ensureStatusColumnSupportsWorkflow();
            DB::beginTransaction();

            foreach ($request->items as $itemId => $data) {
                $item = InventoryPurchaseItem::findOrFail($itemId);
                $receiveQty = $data['receive_qty'];

                $item->update(['received_quantity' => $receiveQty]);

                // Increase Stock
                $product = Product::findOrFail($item->product_id);
                $product->increment('stock', $receiveQty);

                // Update cost price if needed?
                // Usually we keep track of average cost or just latest cost.
                $product->update(['cost_price' => $item->purchase_price]);
            }

            $purchase->update(['status' => 'received']);

            DB::commit();
            return response()->json(['success' => 'Inventory received and stock updated.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $purchase = InventoryPurchase::with(['vendor', 'creator', 'items.product', 'payments'])->findOrFail($id);
        return view('admin.inventory_purchase.show', compact('purchase'));
    }

    public function addPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:100',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $purchase = InventoryPurchase::lockForUpdate()->findOrFail($id);
            $amount = round((float) $request->amount, 2);
            $dueAmount = round((float) $purchase->due_amount, 2);

            if ($amount > $dueAmount) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount cannot exceed due amount.',
                ], 422);
            }

            InventoryPurchasePayment::create([
                'inventory_purchase_id' => $purchase->id,
                'vendor_id' => $purchase->vendor_id,
                'amount' => $amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method ?? 'Cash',
                'note' => $request->note,
                'created_by' => auth()->id(),
            ]);

            $purchase->update([
                'paid_amount' => round((float) $purchase->paid_amount + $amount, 2),
                'due_amount' => round($dueAmount - $amount, 2),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment added successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function paymentHistory($id)
    {
        $purchase = InventoryPurchase::with(['payments' => fn($query) => $query->latest(), 'vendor'])->findOrFail($id);

        return response()->json([
            'purchase_number' => $purchase->purchase_number,
            'vendor_name' => $purchase->vendor->name ?? '-',
            'total_amount' => number_format($purchase->total_amount, 2),
            'paid_amount' => number_format($purchase->paid_amount, 2),
            'due_amount' => number_format($purchase->due_amount, 2),
            'payments' => $purchase->payments->map(fn($payment) => [
                'date' => $payment->payment_date ? $payment->payment_date->format('d M, Y') : '-',
                'amount' => number_format($payment->amount, 2),
                'method' => $payment->payment_method,
                'note' => $payment->note ?: '-',
            ]),
        ]);
    }
}
