<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $dealerId = Auth::guard('dealer')->id();
        $dealer = Auth::guard('dealer')->user();

        $totalConfirmedOrders = \App\Models\DealerOrder::where('dealer_id', $dealerId)->where('status', 'confirm')->count();
        $totalRequestedOrders = \App\Models\OrderRequest::where('dealer_id', $dealerId)->count();
        $totalProducts = \App\Models\Product::count();
        $totalOrderDelivered = \App\Models\DealerOrder::where('dealer_id', $dealerId)->where('status', 'delivered')->count();

        $months = [];
        $confirmedOrderAmount = [];
        $requestedOrderQty = [];
        $orderQuantity = [];
        $deliveredQuantity = [];

        $currentYear = date('Y');

        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
            
            $confirmedOrderAmount[] = \App\Models\DealerOrder::where('dealer_id', $dealerId)
                ->where('status', 'confirm')
                ->whereMonth('order_date', $i)
                ->whereYear('order_date', $currentYear)
                ->sum('grand_total');

            $requestedOrderQty[] = \App\Models\OrderRequestItem::whereHas('orderRequest', function($q) use($dealerId, $i, $currentYear) {
                $q->where('dealer_id', $dealerId)
                  ->whereMonth('created_at', $i)
                  ->whereYear('created_at', $currentYear);
            })->sum('requested_qty');

            $orderQuantity[] = \App\Models\DealerOrderItem::whereHas('dealerOrder', function($q) use($dealerId, $i, $currentYear) {
                $q->where('dealer_id', $dealerId)
                  ->whereMonth('order_date', $i)
                  ->whereYear('order_date', $currentYear);
            })->sum('qty');

            $deliveredQuantity[] = \App\Models\DealerOrderItem::whereHas('dealerOrder', function($q) use($dealerId, $i, $currentYear) {
                $q->where('dealer_id', $dealerId)
                  ->where('status', 'delivered')
                  ->whereMonth('order_date', $i)
                  ->whereYear('order_date', $currentYear);
            })->sum('qty');
        }

        return view('dealer.dashboard', compact(
            'dealer', 
            'totalConfirmedOrders', 
            'totalRequestedOrders', 
            'totalProducts', 
            'totalOrderDelivered',
            'months',
            'confirmedOrderAmount',
            'requestedOrderQty',
            'orderQuantity',
            'deliveredQuantity'
        ));
    }
}
