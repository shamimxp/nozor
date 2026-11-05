<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DealerOrder;
use App\Models\DraftOrder;
use App\Models\OrderActivityLog;
use App\Models\OrderRequest;
use App\Models\PosOrder;
use App\Models\WebOrder;
use App\Services\OrderActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function __construct()
    {
        // History is readable if the admin can show any related order module.
        $this->middleware(function ($request, $next) {
            $allowed = [
                'web_order.show', 'pos_order.show', 'dealer_order.show',
                'draft_order.show', 'order_request.show',
            ];
            foreach ($allowed as $permission) {
                if (\Illuminate\Support\Facades\Gate::check($permission)) {
                    return $next($request);
                }
            }
            abort(403);
        });
    }

    /**
     * Map slug/alias to model class & order_type
     */
    protected array $typeMap = [
        'pos'           => ['class' => PosOrder::class,     'type' => 'pos_order',     'label' => 'POS Order'],
        'pos_order'     => ['class' => PosOrder::class,     'type' => 'pos_order',     'label' => 'POS Order'],
        'web'           => ['class' => WebOrder::class,     'type' => 'web_order',     'label' => 'Web Order'],
        'web_order'     => ['class' => WebOrder::class,     'type' => 'web_order',     'label' => 'Web Order'],
        'dealer'        => ['class' => DealerOrder::class,  'type' => 'dealer_order',  'label' => 'Dealer Order'],
        'dealer_order'  => ['class' => DealerOrder::class,  'type' => 'dealer_order',  'label' => 'Dealer Order'],
        'request'       => ['class' => OrderRequest::class, 'type' => 'order_request', 'label' => 'Dealer Order Request'],
        'order_request' => ['class' => OrderRequest::class, 'type' => 'order_request', 'label' => 'Dealer Order Request'],
        'draft'         => ['class' => DraftOrder::class,   'type' => 'draft_order',   'label' => 'Draft Order'],
        'draft_order'   => ['class' => DraftOrder::class,   'type' => 'draft_order',   'label' => 'Draft Order'],
    ];

    /**
     * Fetch chronological activity history for a specific order
     */
    public function getHistory(string $type, int $id): JsonResponse
    {
        $normalizedType = strtolower(str_replace('-', '_', $type));
        $config = $this->typeMap[$normalizedType] ?? null;

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order type specified.'
            ], 400);
        }

        $modelClass = $config['class'];
        $orderType = $config['type'];
        $typeLabel = $config['label'];

        $order = $modelClass::find($id);

        // Authorization check if dealer is requesting
        if (Auth::guard('dealer')->check()) {
            $dealerId = Auth::guard('dealer')->id();
            if ($order && isset($order->dealer_id) && $order->dealer_id != $dealerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this order history.'
                ], 403);
            }
        }

        $orderNumber = $order ? OrderActivityService::resolveOrderNumber($order) : ('#' . $id);
        $currentStatus = $order ? ($order->order_status ?? $order->status ?? 'N/A') : 'N/A';

        // Retrieve activities ordered chronologically (newest first for timeline)
        $activities = OrderActivityLog::where('order_type', $orderType)
            ->where('order_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $html = view('admin.partials.order_history_timeline', [
            'order'         => $order,
            'orderNumber'   => $orderNumber,
            'orderType'     => $orderType,
            'typeLabel'     => $typeLabel,
            'currentStatus' => $currentStatus,
            'activities'    => $activities,
        ])->render();

        return response()->json([
            'success'        => true,
            'order_id'       => $id,
            'order_number'   => $orderNumber,
            'order_type'     => $orderType,
            'type_label'     => $typeLabel,
            'current_status' => $currentStatus,
            'count'          => $activities->count(),
            'html'           => $html,
        ]);
    }
}
