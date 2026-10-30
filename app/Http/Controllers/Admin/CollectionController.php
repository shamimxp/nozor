<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Customer;
use App\Models\Dealer;
use App\Models\DealerOrder;
use App\Models\PosOrder;
use App\Models\WebOrder;
use App\Models\CustomOrder;
use App\Services\DealerWalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Collection::with(['creator'])->latest('id');

            if ($request->filled('collection_type')) {
                $query->where('collection_type', $request->collection_type);
            }

            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('payment_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('payment_date', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('collection_no', function ($r) {
                    return '<span class="font-weight-bolder text-primary">#' . e($r->collection_no) . '</span>';
                })
                ->addColumn('party_info', function ($r) {
                    if ($r->collection_type === 'dealer') {
                        $dealer = Dealer::find($r->party_id);
                        if ($dealer) {
                            return '<div><strong class="text-dark">' . e($dealer->shop_name) . '</strong></div>' .
                                   '<small class="text-muted">' . e($dealer->name) . ' (' . e($dealer->phone) . ')</small>';
                        }
                    } elseif (in_array($r->collection_type, ['pos_order', 'custom_order', 'customer'])) {
                        $customer = Customer::find($r->party_id);
                        if ($customer) {
                            return '<div><strong class="text-dark">' . e($customer->name) . '</strong></div>' .
                                   '<small class="text-muted">' . e($customer->phone) . '</small>';
                        }
                    } elseif ($r->collection_type === 'web_order') {
                        $order = WebOrder::find($r->source_id);
                        if ($order) {
                            return '<div><strong class="text-dark">Web Order #' . e($order->invoice_no) . '</strong></div>';
                        }
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('type_badge', function ($r) {
                    $badge = 'badge-light-secondary';
                    $text = ucfirst(str_replace('_', ' ', $r->collection_type));
                    if ($r->collection_type === 'dealer') {
                        $badge = 'badge-light-primary';
                        $text = 'Dealer Wallet';
                    } elseif ($r->collection_type === 'pos_order') {
                        $badge = 'badge-light-info';
                        $text = 'POS Order Due';
                    } elseif ($r->collection_type === 'web_order') {
                        $badge = 'badge-light-warning';
                        $text = 'Web Order';
                    } elseif ($r->collection_type === 'custom_order') {
                        $badge = 'badge-light-success';
                        $text = 'Custom Order';
                    }
                    return '<span class="badge badge-pill ' . $badge . ' font-weight-bold">' . $text . '</span>';
                })
                ->addColumn('amount', function ($r) {
                    return '<strong class="text-success font-weight-bolder">৳ ' . number_format($r->amount, 2) . '</strong>';
                })
                ->addColumn('method_badge', function ($r) {
                    $badge = 'badge-light-secondary';
                    $method = $r->payment_method;
                    if ($method === 'Cash') {
                        $badge = 'badge-light-success';
                    } elseif ($method === 'Bank') {
                        $badge = 'badge-light-primary';
                    } elseif (in_array($method, ['bKash', 'Nagad', 'Rocket'])) {
                        $badge = 'badge-light-danger';
                    }

                    $info = '<span class="badge badge-pill ' . $badge . ' font-weight-bold">' . e($method) . '</span>';
                    if (!empty($r->transaction_id)) {
                        $info .= '<br><small class="text-muted font-weight-bold">Trx: ' . e($r->transaction_id) . '</small>';
                    }
                    if (!empty($r->bank_name)) {
                        $info .= '<br><small class="text-muted">' . e($r->bank_name) . '</small>';
                    }
                    return $info;
                })
                ->addColumn('date', fn($r) => $r->payment_date ? $r->payment_date->format('d M, Y') : '-')
                ->addColumn('creator', fn($r) => $r->creator->name ?? 'Admin')
                ->addColumn('action', function ($r) {
                    return '<button type="button" class="btn btn-sm btn-outline-info viewCollectionBtn"
                        data-id="' . $r->id . '"
                        data-no="' . e($r->collection_no) . '"
                        data-amount="৳ ' . number_format($r->amount, 2) . '"
                        data-method="' . e($r->payment_method) . '"
                        data-date="' . ($r->payment_date ? $r->payment_date->format('d M, Y') : '-') . '"
                        data-note="' . e($r->note ?? '-') . '"
                        data-trx="' . e($r->transaction_id ?? '-') . '"
                        data-bank="' . e($r->bank_name ?? '-') . '"
                        data-acc="' . e($r->account_no ?? '-') . '"
                        title="View Details">
                        <i data-feather="eye"></i>
                    </button>';
                })
                ->rawColumns(['collection_no', 'party_info', 'type_badge', 'amount', 'method_badge', 'action'])
                ->make(true);
        }

        // Metrics for top cards
        $totalCollected = (float) Collection::sum('amount');
        $dealerCollected = (float) Collection::where('collection_type', 'dealer')->sum('amount');
        $posCollected = (float) Collection::where('collection_type', 'pos_order')->sum('amount');
        $webCollected = (float) Collection::where('collection_type', 'web_order')->sum('amount');

        return view('admin.collections.index', compact(
            'totalCollected',
            'dealerCollected',
            'posCollected',
            'webCollected'
        ));
    }

    public function create()
    {
        $dealers = Dealer::where('status', 1)->orderBy('name')->get();
        $posOrders = PosOrder::where('due_amount', '>', 0)->with('customer')->latest()->take(100)->get();
        $webOrders = WebOrder::latest()->take(100)->get();
        $customers = Customer::orderBy('name')->get();

        return view('admin.collections.create', compact('dealers', 'posOrders', 'webOrders', 'customers'));
    }

    public function store(Request $request)
    {
        // Handle legacy payload from due_list modal
        if ($request->has('payable_type') && in_array($request->payable_type, ['customOrder', 'POSOrder'])) {
            return $this->handleLegacyStore($request);
        }

        $request->validate([
            'collection_type' => 'required|in:dealer,pos_order,web_order,custom_order',
            'amount'          => 'required|numeric|min:0.01',
            'payment_method'  => 'required|in:Cash,Bank,bKash,Nagad,Rocket',
            'payment_date'    => 'required|date',
            'transaction_id'  => 'nullable|string|max:191',
            'bank_name'       => 'nullable|string|max:191',
            'account_no'      => 'nullable|string|max:191',
            'note'            => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $adminId = auth('admin')->id() ?? auth()->id();

            // 1. DEALER WALLET DEPOSIT
            if ($request->collection_type === 'dealer') {
                $request->validate(['dealer_id' => 'required|exists:dealers,id']);

                $walletService = app(DealerWalletService::class);
                $result = $walletService->deposit(
                    (int) $request->dealer_id,
                    (float) $request->amount,
                    $request->payment_method,
                    [
                        'payment_date'   => $request->payment_date,
                        'transaction_id' => $request->transaction_id,
                        'bank_name'      => $request->bank_name,
                        'account_no'     => $request->account_no,
                        'note'           => $request->note,
                        'created_by'     => $adminId,
                    ]
                );

                DB::commit();

                $settledCount = count($result['settled_orders'] ?? []);
                $msg = '৳ ' . number_format($request->amount, 2) . ' credited to dealer wallet.';
                if ($settledCount > 0) {
                    $msg .= ' Automatically settled ' . $settledCount . ' delivered order(s) via FIFO!';
                }

                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }

            // 2. POS ORDER DUE PAYMENT
            if ($request->collection_type === 'pos_order') {
                $request->validate(['pos_order_id' => 'required|exists:pos_orders,id']);
                $order = PosOrder::lockForUpdate()->findOrFail($request->pos_order_id);

                if ($request->amount > ($order->due_amount + 0.01)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Amount exceeds POS order due balance (৳ ' . number_format($order->due_amount, 2) . ').'
                    ], 422);
                }

                $collection = Collection::create([
                    'collection_no'   => Collection::generateCollectionNo(),
                    'collection_type' => 'pos_order',
                    'party_type'      => Customer::class,
                    'party_id'        => $order->customer_id,
                    'source_type'     => PosOrder::class,
                    'source_id'       => $order->id,
                    'amount'          => $request->amount,
                    'payment_method'  => $request->payment_method,
                    'transaction_id'  => $request->transaction_id,
                    'bank_name'       => $request->bank_name,
                    'account_no'      => $request->account_no,
                    'payment_date'    => $request->payment_date,
                    'note'            => $request->note ?? "Payment for POS order #{$order->order_number}",
                    'created_by'      => $adminId,
                ]);

                $order->paid_amount += $request->amount;
                $order->due_amount  -= $request->amount;
                if ($order->due_amount <= 0.001) {
                    $order->due_amount = 0.00;
                    $order->payment_status = 'paid';
                } else {
                    $order->payment_status = 'partial';
                }
                $order->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Payment of ৳ ' . number_format($request->amount, 2) . ' recorded for POS Order #' . $order->order_number . ' successfully!'
                ]);
            }

            // 3. WEB ORDER PAYMENT
            if ($request->collection_type === 'web_order') {
                $request->validate(['web_order_id' => 'required|exists:web_orders,id']);
                $order = WebOrder::lockForUpdate()->findOrFail($request->web_order_id);

                $collection = Collection::create([
                    'collection_no'   => Collection::generateCollectionNo(),
                    'collection_type' => 'web_order',
                    'party_type'      => null,
                    'party_id'        => $order->user_id,
                    'source_type'     => WebOrder::class,
                    'source_id'       => $order->id,
                    'amount'          => $request->amount,
                    'payment_method'  => $request->payment_method,
                    'transaction_id'  => $request->transaction_id,
                    'bank_name'       => $request->bank_name,
                    'account_no'      => $request->account_no,
                    'payment_date'    => $request->payment_date,
                    'note'            => $request->note ?? "Payment for Web order #{$order->invoice_no}",
                    'created_by'      => $adminId,
                ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Payment of ৳ ' . number_format($request->amount, 2) . ' recorded for Web Order #' . $order->invoice_no . ' successfully!'
                ]);
            }

            // 4. CUSTOM ORDER DUE PAYMENT (Legacy customer order)
            if ($request->collection_type === 'custom_order') {
                $request->validate(['custom_order_id' => 'required|exists:custom_orders,id']);
                $order = CustomOrder::lockForUpdate()->findOrFail($request->custom_order_id);

                if ($request->amount > ($order->due + 0.01)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Amount exceeds Custom order due balance (৳ ' . number_format($order->due, 2) . ').'
                    ], 422);
                }

                $collection = Collection::create([
                    'collection_no'   => Collection::generateCollectionNo(),
                    'collection_type' => 'custom_order',
                    'party_type'      => Customer::class,
                    'party_id'        => $order->customer_id,
                    'source_type'     => CustomOrder::class,
                    'source_id'       => $order->id,
                    'amount'          => $request->amount,
                    'payment_method'  => $request->payment_method,
                    'transaction_id'  => $request->transaction_id,
                    'bank_name'       => $request->bank_name,
                    'account_no'      => $request->account_no,
                    'payment_date'    => $request->payment_date,
                    'note'            => $request->note ?? "Payment for Custom order #{$order->order_number}",
                    'created_by'      => $adminId,
                ]);

                $order->paid += $request->amount;
                $order->due  -= $request->amount;
                if ($order->due <= 0.001) {
                    $order->due = 0.00;
                }
                $order->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Payment of ৳ ' . number_format($request->amount, 2) . ' recorded for Custom Order #' . $order->order_number . ' successfully!'
                ]);
            }

            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Invalid collection type.'], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Backward-compatible handler for existing modals on POS/Custom Order due lists
     */
    protected function handleLegacyStore(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required',
            'amount'         => 'required|numeric|min:0.01',
            'payment_date'   => 'required|date',
            'payment_method' => 'required',
            'payable_id'     => 'required',
            'payable_type'   => 'required|in:customOrder,POSOrder',
        ]);

        try {
            DB::beginTransaction();
            $adminId = auth('admin')->id() ?? auth()->id();

            if ($request->payable_type === 'customOrder') {
                $order = CustomOrder::lockForUpdate()->findOrFail($request->payable_id);
                if ($request->amount > ($order->due + 0.01)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Amount exceeds due balance (৳ ' . number_format($order->due, 2) . ')'
                    ], 422);
                }

                $order->increment('paid', $request->amount);
                $order->decrement('due', $request->amount);

                Collection::create([
                    'collection_no'   => Collection::generateCollectionNo(),
                    'collection_type' => 'custom_order',
                    'party_type'      => Customer::class,
                    'party_id'        => $request->customer_id,
                    'source_type'     => CustomOrder::class,
                    'source_id'       => $order->id,
                    'amount'          => $request->amount,
                    'payment_method'  => $request->payment_method,
                    'payment_date'    => $request->payment_date,
                    'note'            => $request->note,
                    'created_by'      => $adminId,
                ]);

            } elseif ($request->payable_type === 'POSOrder') {
                $order = PosOrder::lockForUpdate()->findOrFail($request->payable_id);
                if ($request->amount > ($order->due_amount + 0.01)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Amount exceeds due balance (৳ ' . number_format($order->due_amount, 2) . ')'
                    ], 422);
                }

                $order->increment('paid_amount', $request->amount);
                $order->decrement('due_amount', $request->amount);

                $fresh = $order->fresh();
                $order->update([
                    'payment_status' => $fresh->due_amount <= 0.001 ? 'paid' : 'partial'
                ]);

                Collection::create([
                    'collection_no'   => Collection::generateCollectionNo(),
                    'collection_type' => 'pos_order',
                    'party_type'      => Customer::class,
                    'party_id'        => $request->customer_id,
                    'source_type'     => PosOrder::class,
                    'source_id'       => $order->id,
                    'amount'          => $request->amount,
                    'payment_method'  => $request->payment_method,
                    'payment_date'    => $request->payment_date,
                    'note'            => $request->note,
                    'created_by'      => $adminId,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payment recorded successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function customerHistory($id)
    {
        $customer = Customer::findOrFail($id);
        $collections = Collection::where('party_id', $id)
            ->where('party_type', Customer::class)
            ->with(['creator'])
            ->latest('id')
            ->get();
        return view('admin.collections.customer_history', compact('customer', 'collections'));
    }

    public function dealerHistory($id)
    {
        $dealer = Dealer::findOrFail($id);
        $collections = Collection::where('party_id', $id)
            ->where('party_type', Dealer::class)
            ->with(['creator'])
            ->latest('id')
            ->get();
        return view('admin.collections.dealer_history', compact('dealer', 'collections'));
    }
}
