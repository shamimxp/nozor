<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Admin\Concerns\AuthorizesAdminModule;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Dealer;
use App\Models\ReturnOrder;
use App\Models\User;
use App\Services\ReturnService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReturnController extends Controller
{
    use AuthorizesAdminModule;

    protected ReturnService $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->authorizeAdminModule('return_order');
        $this->returnService = $returnService;
    }

    /**
     * Display listing of returns with KPI cards and DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ReturnOrder::with(['items'])->select('returns.*');

            // Filter by channel
            if ($request->filled('channel') && $request->channel !== 'all') {
                $query->where('channel', $request->channel);
            }

            // Filter by status
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by refund type
            if ($request->filled('refund_type') && $request->refund_type !== 'all') {
                $query->where('refund_type', $request->refund_type);
            }

            // Filter by date
            if ($request->filled('from_date')) {
                $query->whereDate('return_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('return_date', '<=', $request->to_date);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('return_identity', function ($row) {
                    $url = route('admin.returns.show', $row->id);
                    return '
                        <div>
                            <a href="' . $url . '" class="font-weight-bolder text-dark font-small-3 text-decoration-none">
                                ' . e($row->return_no) . '
                            </a>
                            <div class="mt-25">' . $row->channel_badge . '</div>
                        </div>';
                })
                ->addColumn('formatted_date', function ($row) {
                    return '<span class="text-dark font-small-3 font-weight-bold">' . date('M d, Y', strtotime($row->return_date)) . '</span>';
                })
                ->addColumn('customer_info', function ($row) {
                    $phone = $row->customer_phone ? '<small class="text-muted d-block font-small-2"><i data-feather="phone" style="width: 10px; height: 10px; margin-right: 2px;"></i> ' . e($row->customer_phone) . '</small>' : '';
                    return '
                        <div>
                            <span class="font-weight-bold text-dark font-small-3 d-block">' . e($row->customer_name ?: 'General Party') . '</span>
                            ' . $phone . '
                        </div>';
                })
                ->addColumn('invoices_list', function ($row) {
                    $invs = $row->items->pluck('order_number')->unique()->values();
                    if ($invs->isEmpty()) {
                        return '<span class="text-muted font-small-2">-</span>';
                    }
                    $badges = '';
                    foreach ($invs as $inv) {
                        $badges .= '<span class="badge badge-light-primary me-50 font-small-1 font-weight-bold" style="margin-right: 3px; margin-bottom: 2px;">' . e($inv) . '</span>';
                    }
                    return '<div class="d-flex flex-wrap">' . $badges . '</div>';
                })
                ->addColumn('total_qty_formatted', function ($row) {
                    return '<span class="badge badge-pill badge-light-secondary font-weight-bold font-small-2">' . (float) $row->total_qty . ' items</span>';
                })
                ->addColumn('refund_amount_formatted', function ($row) {
                    return '<span class="font-weight-bolder text-dark font-small-3">৳' . number_format($row->refund_amount, 2) . '</span>';
                })
                ->addColumn('refund_type_badge', function ($row) {
                    return $row->refund_type_badge;
                })
                ->addColumn('status_badge', function ($row) {
                    return $row->status_badge;
                })
                ->addColumn('note_preview', function ($row) {
                    if (empty($row->return_note)) {
                        return '<span class="text-muted font-small-2 font-italic">-</span>';
                    }
                    $short = mb_strimwidth($row->return_note, 0, 35, '...');
                    return '<span class="text-muted font-small-2" title="' . e($row->return_note) . '">' . e($short) . '</span>';
                })
                ->addColumn('actions', function ($row) {
                    $showUrl = route('admin.returns.show', $row->id);
                    $btn = '<div class="d-inline-flex" style="gap: 5px;">';
                    $btn .= '<a href="' . $showUrl . '" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center" style="height: 30px; padding: 4px 10px; font-weight: 600; font-size: 12px; border-radius: 6px;" title="View Details">
                                <i data-feather="eye" style="width: 12px; height: 12px; margin-right: 4px;"></i> View
                             </a>';
                    if ($row->status === 'pending') {
                        $btn .= '<button type="button" class="btn btn-sm btn-success btn-quick-approve d-inline-flex align-items-center" data-id="' . $row->id . '" data-no="' . e($row->return_no) . '" data-amount="' . number_format($row->refund_amount, 2) . '" data-type="' . $row->refund_type . '" style="height: 30px; padding: 4px 8px; font-weight: 600; font-size: 12px; border-radius: 6px; background-color: #28c76f; border-color: #28c76f;" title="Approve Return">
                                    <i data-feather="check" style="width: 12px; height: 12px;"></i>
                                 </button>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['return_identity', 'formatted_date', 'customer_info', 'invoices_list', 'total_qty_formatted', 'refund_amount_formatted', 'refund_type_badge', 'status_badge', 'note_preview', 'actions'])
                ->make(true);
        }

        // Summary KPI Metrics
        $totalReturnsCount = ReturnOrder::count();
        $totalRefundSum = (float) ReturnOrder::where('status', 'approved')->sum('refund_amount');
        $pendingCount = ReturnOrder::where('status', 'pending')->count();
        $pendingAmount = (float) ReturnOrder::where('status', 'pending')->sum('refund_amount');
        $walletCreditSum = (float) ReturnOrder::where('status', 'approved')->where('refund_type', 'wallet')->sum('refund_amount');

        return view('admin.returns.index', compact(
            'totalReturnsCount',
            'totalRefundSum',
            'pendingCount',
            'pendingAmount',
            'walletCreditSum'
        ));
    }

    /**
     * Show return creation form
     */
    public function create()
    {
        $dealers = Dealer::where('status', 1)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $webUsers = User::orderBy('name')->get();

        return view('admin.returns.create', compact('dealers', 'customers', 'webUsers'));
    }

    /**
     * Store new return request
     */
    public function store(Request $request)
    {
        // Filter out unselected items or items with zero return quantity
        $rawItems = $request->input('items', []);
        $selectedItems = [];
        if (is_array($rawItems)) {
            foreach ($rawItems as $key => $item) {
                if (!empty($item['selected']) && (float) ($item['return_qty'] ?? 0) > 0) {
                    $selectedItems[$key] = $item;
                }
            }
        }

        if (empty($selectedItems)) {
            return back()->withInput()->with('error', 'Please select at least one product with a return quantity greater than 0.');
        }

        $request->merge(['items' => $selectedItems]);

        $request->validate([
            'channel'          => 'required|in:dealer,pos,web',
            'party_id'         => 'required|integer',
            'return_date'      => 'required|date',
            'refund_type'      => 'required|in:cash,wallet',
            'deduction_amount' => 'nullable|numeric|min:0',
            'return_note'      => 'nullable|string|max:1000',
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.return_qty' => 'required|numeric|min:0.01',
        ]);

        try {
            $returnOrder = $this->returnService->createReturn($request->all(), auth()->id());

            return redirect()->route('admin.returns.show', $returnOrder->id)
                ->with('success', "Return Request #{$returnOrder->return_no} created successfully in Pending status.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error creating return: ' . $e->getMessage());
        }
    }

    /**
     * Display detailed return slip and approval actions
     */
    public function show($id)
    {
        $return = ReturnOrder::with([
            'items.product',
            'walletTransaction',
            'approver',
            'rejector',
            'creator',
            'party'
        ])->findOrFail($id);

        return view('admin.returns.show', compact('return'));
    }

    /**
     * Approve a pending return
     */
    public function approve(Request $request, $id)
    {
        $return = ReturnOrder::findOrFail($id);

        try {
            $this->returnService->approveReturn($return, $request->input('admin_review_note'), auth()->id());

            $msg = "Return #{$return->return_no} has been approved successfully.";
            if ($return->refund_type === 'wallet') {
                $msg .= " ৳" . number_format($return->refund_amount, 2) . " has been credited to the dealer's wallet.";
            }

            return redirect()->route('admin.returns.show', $return->id)->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    /**
     * Reject a pending return
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);

        $return = ReturnOrder::findOrFail($id);

        try {
            $this->returnService->rejectReturn($return, $request->input('reject_reason'), auth()->id());

            return redirect()->route('admin.returns.show', $return->id)
                ->with('warning', "Return #{$return->return_no} has been rejected.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get delivered invoices for selected party & channel
     */
    public function getInvoices(Request $request): JsonResponse
    {
        $channel = $request->input('channel', 'dealer');
        $partyId = (int) $request->input('party_id', 0);

        if (!$partyId) {
            return response()->json(['invoices' => []]);
        }

        $invoices = $this->returnService->getInvoicesByParty($channel, $partyId);

        return response()->json([
            'status'   => true,
            'invoices' => $invoices,
        ]);
    }

    /**
     * AJAX: Get products across selected invoices
     */
    public function getInvoiceProducts(Request $request): JsonResponse
    {
        $channel = $request->input('channel', 'dealer');
        $orderIds = (array) $request->input('order_ids', []);

        $products = $this->returnService->getInvoiceProducts($channel, $orderIds);

        return response()->json([
            'status'   => true,
            'products' => $products,
        ]);
    }
}
