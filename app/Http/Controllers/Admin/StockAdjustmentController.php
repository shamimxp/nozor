<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = StockAdjustment::with(['product', 'creator'])->latest();

            if ($request->filled('date_from')) {
                $query->whereDate('adjustment_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('adjustment_date', '<=', $request->date_to);
            }

            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('receive_status')) {
                if ($request->receive_status === 'received') {
                    $query->where('type', 'normal')->where('is_received', true);
                } elseif ($request->receive_status === 'pending') {
                    $query->where('type', 'normal')->where('is_received', false);
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('product_name', function($r) {
                    $name = e($r->product->name ?? '-');
                    $badge = '';
                    if (!empty($r->product->is_manufacturer)) {
                        $badge = ' <span class="badge badge-light-primary" style="font-size: 10px;">Manufacture</span>';
                    }
                    return '<span class="font-weight-bold">' . $name . '</span>' . $badge;
                })
                ->addColumn('quantity_formatted', function($r) {
                    if ($r->type === 'addition') {
                        return '<span class="text-success font-weight-bold">+' . number_format($r->quantity, 2) . '</span>';
                    }
                    return '<span class="text-danger font-weight-bold">-' . number_format($r->quantity, 2) . '</span>';
                })
                ->addColumn('type_badge', function($r) {
                    if ($r->type == 'addition') {
                        return '<span class="badge badge-light-success font-weight-bold"><i data-feather="plus-circle" class="mr-25"></i> Addition (Surplus)</span>';
                    } elseif ($r->type == 'normal') {
                        return '<span class="badge badge-light-info font-weight-bold"><i data-feather="repeat" class="mr-25"></i> Normal (Exchange)</span>';
                    }
                    return '<span class="badge badge-light-danger font-weight-bold"><i data-feather="alert-triangle" class="mr-25"></i> Abnormal (Damage)</span>';
                })
                ->addColumn('receive_status', function($r) {
                    if ($r->type !== 'normal') {
                        return '<span class="badge badge-light-secondary font-weight-normal">Not Applicable</span>';
                    }

                    if ($r->is_received) {
                        return '<span class="badge badge-light-success font-weight-bold"><i data-feather="check" class="mr-25"></i> Received</span>' .
                            ($r->received_at ? '<br><small class="text-muted">' . $r->received_at->format('d M, Y h:i A') . '</small>' : '');
                    }

                    return '<span class="badge badge-light-warning font-weight-bold"><i data-feather="clock" class="mr-25"></i> Pending</span>';
                })
                ->addColumn('date', fn($r) => $r->adjustment_date ? $r->adjustment_date->format('d M, Y') : '-')
                ->addColumn('creator_name', fn($r) => $r->creator->name ?? 'Admin')
                ->addColumn('action', function($r) {
                    $html = '<div class="btn-group btn-group-sm" role="group">';
                    if ($r->type === 'normal' && !$r->is_received) {
                        $html .= '<button type="button" class="btn btn-success btn-sm receiveAdjustmentBtn mr-50"
                            data-id="'.$r->id.'"
                            data-product="'.e($r->product->name ?? '-').'"
                            data-quantity="'.$r->quantity.'" title="Receive Exchange Stock">
                            <i data-feather="download"></i> Receive
                        </button>';
                    }
                    $html .= '<button type="button" class="btn btn-outline-danger btn-sm deleteAdjustmentBtn"
                        data-id="'.$r->id.'"
                        data-product="'.e($r->product->name ?? '-').'"
                        data-type="'.$r->type.'"
                        data-quantity="'.$r->quantity.'" title="Delete Adjustment">
                        <i data-feather="trash-2"></i>
                    </button>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['product_name', 'quantity_formatted', 'type_badge', 'receive_status', 'action'])
                ->make(true);
        }

        $products = Product::where('status', 1)->orderBy('name')->get();
        $totalAdjustments = StockAdjustment::count();
        $totalAdditions = StockAdjustment::where('type', 'addition')->count();
        $pendingReceives = StockAdjustment::where('type', 'normal')->where('is_received', false)->count();
        $totalDeductions = StockAdjustment::whereIn('type', ['normal', 'abnormal'])->count();

        return view('admin.stock_adjustment.index', compact(
            'products',
            'totalAdjustments',
            'totalAdditions',
            'pendingReceives',
            'totalDeductions'
        ));
    }

    public function create()
    {
        $products = Product::where('status', 1)
            ->select('id', 'name', 'stock', 'is_manufacturer')
            ->orderBy('name')
            ->get();
        return view('admin.stock_adjustment.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'type' => 'required|in:addition,normal,abnormal',
            'adjustment_date' => 'required|date',
            'reason' => 'nullable|string|max:1000',
        ]);

        $stockService = app(\App\Services\StockService::class);

        try {
            DB::beginTransaction();

            $product = Product::lockForUpdate()->findOrFail($request->product_id);

            // For deduction types, ensure current stock is sufficient
            if ($request->type !== 'addition' && (float) $product->stock < (float) $request->quantity) {
                DB::rollback();
                return response()->json([
                    'error' => "Cannot deduct {$request->quantity} units. Available stock for '{$product->name}' is only " . number_format($product->stock, 2) . "."
                ], 422);
            }

            $adjustment = StockAdjustment::create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'type' => $request->type,
                'adjustment_date' => $request->adjustment_date,
                'reason' => $request->reason,
                'created_by' => auth()->id()
            ]);

            if ($request->type === 'addition') {
                // Positive adjustment: increase stock
                $stockService->recordIn(
                    $request->product_id,
                    (float) $request->quantity,
                    'stock_adjustment',
                    $adjustment->id,
                    'ADJ-' . $adjustment->id,
                    $request->reason ?? 'Positive stock adjustment (Surplus / Found / Return)'
                );
            } else {
                // Negative adjustment: decrease stock
                $stockService->recordOut(
                    $request->product_id,
                    (float) $request->quantity,
                    'stock_adjustment',
                    $adjustment->id,
                    'ADJ-' . $adjustment->id,
                    $request->reason ?? ($request->type === 'normal' ? 'Normal exchange deduction' : 'Damage/loss deduction'),
                    null,
                    false
                );
            }

            DB::commit();
            return response()->json([
                'success' => 'Stock adjusted successfully. Inventory and ledger updated.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function receive($id)
    {
        $stockService = app(\App\Services\StockService::class);

        try {
            DB::beginTransaction();

            $adjustment = StockAdjustment::with('product')->lockForUpdate()->findOrFail($id);

            if ($adjustment->type !== 'normal') {
                DB::rollback();
                return response()->json(['error' => 'Only Normal (Exchange) adjustments can be received.'], 422);
            }

            if ($adjustment->is_received) {
                DB::rollback();
                return response()->json(['error' => 'This adjustment has already been received.'], 422);
            }

            $stockService->recordIn(
                $adjustment->product_id,
                (float) $adjustment->quantity,
                'stock_adjustment',
                $adjustment->id,
                'ADJ-REC-' . $adjustment->id,
                'Exchange item received back into inventory'
            );

            $adjustment->update([
                'is_received' => true,
                'received_at' => now(),
                'received_by' => auth()->id(),
            ]);

            DB::commit();
            return response()->json(['success' => 'Exchange stock received and product quantity updated.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        $stockService = app(\App\Services\StockService::class);

        try {
            DB::beginTransaction();

            $adjustment = StockAdjustment::with('product')->lockForUpdate()->findOrFail($id);
            $product = Product::lockForUpdate()->find($adjustment->product_id);

            // Revert stock impact
            if ($adjustment->type === 'addition') {
                // Positive adjustment is being cancelled: deduct stock back
                if ($product && (float) $product->stock < (float) $adjustment->quantity) {
                    DB::rollback();
                    return response()->json([
                        'error' => "Cannot delete adjustment: available stock ({$product->stock}) is less than the adjustment quantity ({$adjustment->quantity})."
                    ], 422);
                }

                $stockService->recordOut(
                    $adjustment->product_id,
                    (float) $adjustment->quantity,
                    'stock_adjustment_reversal',
                    $adjustment->id,
                    'ADJ-REV-' . $adjustment->id,
                    'Reversal of addition adjustment #' . $adjustment->id,
                    null,
                    false
                );
            } elseif ($adjustment->type === 'abnormal') {
                // Damage deduction is being cancelled: restore stock back
                $stockService->recordIn(
                    $adjustment->product_id,
                    (float) $adjustment->quantity,
                    'stock_adjustment_reversal',
                    $adjustment->id,
                    'ADJ-REV-' . $adjustment->id,
                    'Reversal of damage deduction #' . $adjustment->id
                );
            } elseif ($adjustment->type === 'normal') {
                // Exchange adjustment
                if (!$adjustment->is_received) {
                    // Was deducted and not yet received back: restore deducted stock
                    $stockService->recordIn(
                        $adjustment->product_id,
                        (float) $adjustment->quantity,
                        'stock_adjustment_reversal',
                        $adjustment->id,
                        'ADJ-REV-' . $adjustment->id,
                        'Reversal of pending exchange deduction #' . $adjustment->id
                    );
                }
            }

            $adjustment->delete();

            DB::commit();
            return response()->json(['success' => 'Stock adjustment deleted and stock reversed successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
