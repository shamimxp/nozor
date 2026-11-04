<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesTrash;
use App\Http\Controllers\Controller;
use App\Models\DraftOrder;
use App\Models\DraftOrderItem;
use App\Models\OrderRequest;
use App\Models\OrderRequestItem;
use App\Services\Deletion\SoftDeleteService;
use App\Services\OrderRequestService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DraftOrderController extends Controller
{
    use HandlesTrash;

    protected function trashModuleKey(): string
    {
        return 'draft_order';
    }

    protected $orderRequestService;

    public function __construct(OrderRequestService $orderRequestService)
    {
        $this->orderRequestService = $orderRequestService;
    }

    /**
     * Display a listing of draft orders.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DraftOrder::with(['dealer', 'orderRequest', 'items.product', 'dealerOrder'])->latest();

            if ($request->filled('draft_number')) {
                $query->where('draft_number', 'LIKE', '%' . $request->draft_number . '%');
            }
            if ($request->filled('dealer_info')) {
                $query->whereHas('dealer', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->dealer_info . '%')
                      ->orWhere('shop_name', 'LIKE', '%' . $request->dealer_info . '%')
                      ->orWhere('phone', 'LIKE', '%' . $request->dealer_info . '%');
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('draft_info', function ($r) {
                    $num = '<span class="font-weight-bolder text-primary">' . e($r->draft_number) . '</span>';
                    $date = '<div class="small text-muted">' . $r->created_at->format('d M, Y h:i A') . '</div>';
                    $ref = '';
                    if ($r->orderRequest) {
                        $refUrl = route('admin.order-requests.show', $r->orderRequest->id);
                        $ref = '<div class="small mt-25"><span class="text-muted">From:</span> <a href="' . $refUrl . '" class="badge badge-light-info">' . e($r->orderRequest->request_number ?? ('#' . $r->orderRequest->id)) . '</a></div>';
                    }
                    return $num . $date . $ref;
                })
                ->addColumn('dealer_info', function ($r) {
                    $shop = $r->dealer->shop_name ?? 'N/A';
                    $name = $r->dealer->name ?? 'N/A';
                    $phone = $r->dealer->phone ?? 'N/A';
                    return '<strong>' . e($shop) . '</strong><br>' .
                           '<span class="text-dark">' . e($name) . '</span><br>' .
                           '<small class="text-muted"><i data-feather="phone" style="width:11px;height:11px;"></i> ' . e($phone) . '</small>';
                })
                ->addColumn('total_items', function ($r) {
                    $totalQty = $r->items->sum('qty');
                    return '<span class="badge badge-light-secondary font-weight-bold">' . $r->items->count() . ' Products (' . $totalQty . ' pcs)</span>';
                })
                ->addColumn('totals', function ($r) {
                    return '<span class="font-weight-bolder text-primary">৳' . number_format($r->grand_total, 2) . '</span>';
                })
                ->addColumn('status_badge', function ($r) {
                    if ($r->status === 'converted') {
                        $orderNum = $r->dealerOrder ? $r->dealerOrder->order_number : 'Order';
                        $orderLink = $r->dealer_order_id ? '<br><a href="' . route('admin.dealer-order.show', $r->dealer_order_id) . '" class="small text-success font-weight-bold">' . e($orderNum) . '</a>' : '';
                        return '<span class="badge badge-light-success">Converted</span>' . $orderLink;
                    } elseif ($r->status === 'cancelled') {
                        return '<span class="badge badge-light-danger">Cancelled</span>';
                    } else {
                        return '<span class="badge badge-light-warning">Draft</span>';
                    }
                })
                ->addColumn('action', function ($r) {
                    $btn = '<div class="d-flex align-items-center" style="gap:4px;">';
                    // Show
                    $btn .= '<a href="' . route('admin.draft-orders.show', $r->id) . '" class="btn btn-info btn-sm p-50" title="View Details"><i data-feather="eye" style="width:14px;height:14px;"></i></a>';
                    // View History
                    $btn .= '<button type="button" class="btn btn-outline-info btn-sm p-50 view-order-history" data-order-id="' . $r->id . '" data-order-type="draft_order" data-order-number="' . e($r->draft_number) . '" title="View History"><i data-feather="clock" style="width:14px;height:14px;"></i></button>';
                    // Download PDF
                    $btn .= '<a href="' . route('admin.draft-orders.export-pdf', $r->id) . '" class="btn btn-secondary btn-sm p-50" title="Download PDF"><i data-feather="download" style="width:14px;height:14px;"></i></a>';
                    // Print
                    $btn .= '<a href="' . route('admin.draft-orders.print', $r->id) . '" target="_blank" class="btn btn-light btn-sm p-50" title="Print"><i data-feather="printer" style="width:14px;height:14px;"></i></a>';

                    if ($r->status === 'draft') {
                        // Edit
                        $btn .= '<a href="' . route('admin.draft-orders.edit', $r->id) . '" class="btn btn-primary btn-sm p-50" title="Edit Draft"><i data-feather="edit" style="width:14px;height:14px;"></i></a>';
                        // Convert to Order
                        $btn .= '<button type="button" class="btn btn-success btn-sm p-50 btnConvertOrder" data-id="' . $r->id . '" data-num="' . e($r->draft_number) . '" title="Convert to Dealer Order"><i data-feather="check-circle" style="width:14px;height:14px;"></i></button>';
                    }

                    // Delete
                    $btn .= '<button type="button" class="btn btn-danger btn-sm p-50 btnDeleteDraft" data-id="' . $r->id . '" data-num="' . e($r->draft_number) . '" title="Delete Draft"><i data-feather="trash" style="width:14px;height:14px;"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['draft_info', 'dealer_info', 'total_items', 'totals', 'status_badge', 'action'])
                ->make(true);
        }

        return view('admin.draft_order.index');
    }

    /**
     * Display the specified draft order details.
     */
    public function show($id)
    {
        $draftOrder = DraftOrder::with(['dealer', 'orderRequest', 'items.product.recipe', 'dealerOrder'])->findOrFail($id);
        return view('admin.draft_order.show', compact('draftOrder'));
    }

    /**
     * Show the form for editing the specified draft order.
     */
    public function edit($id)
    {
        $draftOrder = DraftOrder::with(['dealer', 'orderRequest', 'items.product.recipe'])->findOrFail($id);

        if ($draftOrder->status === 'converted') {
            return redirect()->route('admin.draft-orders.show', $draftOrder->id)
                ->with('error', 'Converted draft orders cannot be edited.');
        }

        return view('admin.draft_order.edit', compact('draftOrder'));
    }

    /**
     * Update the specified draft order.
     */
    public function update(Request $request, $id)
    {
        $draftOrder = DraftOrder::with(['items.product', 'orderRequest'])->findOrFail($id);

        if ($draftOrder->status === 'converted') {
            return redirect()->route('admin.draft-orders.show', $draftOrder->id)
                ->with('error', 'Converted draft orders cannot be edited.');
        }

        $request->validate([
            'items'             => 'required|array',
            'items.*.id'        => 'required|exists:draft_order_items,id',
            'items.*.qty'       => 'required|integer|min:1',
            'items.*.note'      => 'nullable|string',
            'discount'          => 'nullable|numeric|min:0',
            'carrying_charge'   => 'nullable|numeric|min:0',
            'note'              => 'nullable|string',
            'admin_notes'       => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($draftOrder, $request) {
                $subTotal = 0;

                foreach ($request->items as $itemInput) {
                    $draftItem = $draftOrder->items->firstWhere('id', $itemInput['id']);
                    if (!$draftItem) continue;

                    $newQty = (int) $itemInput['qty'];
                    $oldQty = (int) $draftItem->qty;
                    $diff   = $newQty - $oldQty;

                    if ($diff < 0) {
                        // User reduced quantity -> restore (-diff) back to original OrderRequest
                        $restoreQty = abs($diff);
                        $this->orderRequestService->restoreDraftItemToOrderRequest(
                            $draftOrder->order_request_id,
                            $draftItem->product_id,
                            $restoreQty
                        );
                    } elseif ($diff > 0 && $draftOrder->order_request_id) {
                        // User increased quantity -> check if available in OrderRequest
                        $reqItem = OrderRequestItem::where('order_request_id', $draftOrder->order_request_id)
                            ->where('product_id', $draftItem->product_id)
                            ->first();

                        $available = $reqItem ? ($reqItem->requested_qty - $reqItem->confirmed_qty) : 0;
                        if ($diff > $available) {
                            throw new \Exception("Cannot increase quantity by {$diff} for product ID {$draftItem->product_id}. Only {$available} available in original Order Request.");
                        }

                        $reqItem->requested_qty -= $diff;
                        if ($reqItem->requested_qty <= 0) {
                            $reqItem->delete();
                        } else {
                            $reqItem->save();
                        }
                    }

                    $draftItem->qty = $newQty;
                    $draftItem->total = $draftItem->unit_price * $newQty;
                    $draftItem->note = $itemInput['note'] ?? null;
                    $draftItem->save();

                    $subTotal += $draftItem->total;
                }

                $discount = (float) ($request->discount ?? 0);
                $carrying = (float) ($request->carrying_charge ?? 0);
                $grandTotal = max(0, ($subTotal - $discount) + $carrying);

                $draftOrder->update([
                    'sub_total'       => $subTotal,
                    'discount'        => $discount,
                    'carrying_charge' => $carrying,
                    'grand_total'     => $grandTotal,
                    'note'            => $request->note,
                    'admin_notes'     => $request->admin_notes,
                ]);
            });

            return redirect()->route('admin.draft-orders.show', $draftOrder->id)
                ->with('success', 'Draft order updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Delete an individual product from a draft order and restore to OrderRequest.
     */
    public function deleteItem($draftId, $itemId)
    {
        try {
            DB::transaction(function () use ($draftId, $itemId) {
                $draftOrder = DraftOrder::with('items')->findOrFail($draftId);

                if ($draftOrder->status === 'converted') {
                    throw new \Exception("Cannot remove items from a converted draft order.");
                }

                $draftItem = $draftOrder->items->firstWhere('id', $itemId);
                if (!$draftItem) {
                    throw new \Exception("Item not found in this draft order.");
                }

                // Restore quantity to the original Order Request
                if ($draftOrder->order_request_id) {
                    $this->orderRequestService->restoreDraftItemToOrderRequest(
                        $draftOrder->order_request_id,
                        $draftItem->product_id,
                        $draftItem->qty
                    );
                }

                // Delete the draft item
                $draftItem->delete();

                // Recalculate draft order totals
                $remainingItems = DraftOrderItem::where('draft_order_id', $draftOrder->id)->get();
                if ($remainingItems->isEmpty()) {
                    $draftOrder->delete();
                } else {
                    $newSubTotal = $remainingItems->sum('total');
                    $grandTotal = max(0, ($newSubTotal - $draftOrder->discount) + $draftOrder->carrying_charge);
                    $draftOrder->update([
                        'sub_total'   => $newSubTotal,
                        'grand_total' => $grandTotal,
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Product removed from draft and restored to original Order Request successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Soft-delete the draft order and restore products to the original Order Request.
     */
    public function destroy($id, SoftDeleteService $softDeleteService)
    {
        try {
            $result = null;

            DB::transaction(function () use ($id, $softDeleteService, &$result) {
                $draftOrder = DraftOrder::with('items')->findOrFail($id);

                if ($draftOrder->status === 'converted') {
                    throw new \Exception('Converted draft orders cannot be deleted.');
                }

                if ($draftOrder->order_request_id) {
                    foreach ($draftOrder->items as $item) {
                        $this->orderRequestService->restoreDraftItemToOrderRequest(
                            $draftOrder->order_request_id,
                            $item->product_id,
                            $item->qty
                        );
                    }
                }

                $result = $softDeleteService->delete($draftOrder, $this->trashModuleKey());
                if (! $result['success']) {
                    throw new \Exception($result['message']);
                }
            });

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Draft order moved to trash and products restored to Order Request.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Export the draft order to PDF.
     */
    public function exportPdf($id)
    {
        $draftOrder = DraftOrder::with(['dealer', 'orderRequest', 'items.product', 'creator'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.draft_order.pdf', compact('draftOrder'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Draft-Order-' . $draftOrder->draft_number . '.pdf');
    }

    /**
     * Print preview page for the draft order.
     */
    public function print($id)
    {
        $draftOrder = DraftOrder::with(['dealer', 'orderRequest', 'items.product', 'creator'])->findOrFail($id);
        return view('admin.draft_order.print', compact('draftOrder'));
    }

    /**
     * Convert the draft order directly to a confirmed Dealer Order.
     */
    public function convertToOrder($id)
    {
        try {
            $order = $this->orderRequestService->convertDraftToDealerOrder($id);

            if (request()->ajax()) {
                return response()->json([
                    'success'      => true,
                    'message'      => 'Draft Order converted to Dealer Order successfully!',
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                    'redirect_url' => route('admin.dealer-order.show', $order->id),
                ]);
            }

            return redirect()->route('admin.dealer-order.show', $order->id)
                ->with('success', 'Draft Order converted to Dealer Order successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
