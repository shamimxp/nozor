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
            $query = StockAdjustment::with('product')->latest();

            if ($request->filled('date_from')) {
                $query->whereDate('adjustment_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('adjustment_date', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('product_name', fn($r) => $r->product->name ?? '-')
                ->addColumn('type_badge', function($r) {
                    if ($r->type == 'addition') {
                        return '<span class="badge badge-light-success">Addition (Surplus)</span>';
                    }
                    $class = $r->type == 'normal' ? 'info' : 'danger';
                    $text = $r->type == 'normal' ? 'Normal (Exchange)' : 'Abnormal (Damage)';
                    return '<span class="badge badge-light-'.$class.'">'.$text.'</span>';
                })
                ->addColumn('receive_status', function($r) {
                    if ($r->type !== 'normal') {
                        return '<span class="badge badge-light-secondary">Not Applicable</span>';
                    }

                    if ($r->is_received) {
                        return '<span class="badge badge-light-success">Received</span><br><small>' .
                            optional($r->received_at)->format('d M, Y h:i A') .
                            '</small>';
                    }

                    return '<span class="badge badge-light-warning">Pending</span>';
                })
                ->addColumn('date', fn($r) => $r->adjustment_date->format('d M, Y'))
                ->addColumn('action', function($r) {
                    if ($r->type === 'normal' && !$r->is_received) {
                        return '<button type="button" class="btn btn-success btn-sm receiveAdjustmentBtn"
                            data-id="'.$r->id.'"
                            data-product="'.e($r->product->name ?? '-').'"
                            data-quantity="'.$r->quantity.'">
                            <i data-feather="download"></i> Receive
                        </button>';
                    }

                    return '-';
                })
                ->rawColumns(['type_badge', 'receive_status', 'action'])
                ->make(true);
        }
        return view('admin.stock_adjustment.index');
    }

    public function create()
    {
        $products = Product::where('status', 1)->get();
        return view('admin.stock_adjustment.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'type' => 'required|in:addition,normal,abnormal',
            'adjustment_date' => 'required|date',
        ]);

        $stockService = app(\App\Services\StockService::class);

        try {
            DB::beginTransaction();

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
                    true
                );
            }

            DB::commit();
            return response()->json(['success' => 'Stock adjusted successfully.']);
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
}
