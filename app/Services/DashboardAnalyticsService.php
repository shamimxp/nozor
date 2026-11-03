<?php

namespace App\Services;

use App\Models\DealerOrder;
use App\Models\PosOrder;
use App\Models\WebOrder;
use App\Models\CustomOrder;
use App\Models\Purchase;
use App\Models\InventoryPurchase;
use App\Models\WorkerPayment;
use App\Models\Manufacture;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Dealer;
use App\Models\ReturnOrder;
use App\Models\Collection;
use App\Models\DealerOrderItem;
use App\Models\PosOrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAnalyticsService
{
    /**
     * Get primary executive KPI metrics.
     */
    public function getKpiMetrics(): array
    {
        $today = Carbon::today()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();

        // 1. Gross Revenue by Channel (Lifetime)
        $dealerRev = (float) DealerOrder::sum('grand_total');
        $posRev    = (float) PosOrder::sum('payable_amount');
        $customRev = (float) CustomOrder::sum('grand_total');
        $webRev    = (float) WebOrder::sum('total');
        $totalGrossRevenue = $dealerRev + $posRev + $customRev + $webRev;

        // Today's Revenue
        $todayDealer = (float) DealerOrder::whereDate('created_at', $today)->sum('grand_total');
        $todayPos    = (float) PosOrder::whereDate('created_at', $today)->sum('payable_amount');
        $todayCustom = (float) CustomOrder::whereDate('created_at', $today)->sum('grand_total');
        $todayWeb    = (float) WebOrder::whereDate('created_at', $today)->sum('total');
        $todayRevenue = $todayDealer + $todayPos + $todayCustom + $todayWeb;

        // This Month's Revenue
        $monthDealer = (float) DealerOrder::whereDate('created_at', '>=', $startOfMonth)->sum('grand_total');
        $monthPos    = (float) PosOrder::whereDate('created_at', '>=', $startOfMonth)->sum('payable_amount');
        $monthCustom = (float) CustomOrder::whereDate('created_at', '>=', $startOfMonth)->sum('grand_total');
        $monthWeb    = (float) WebOrder::whereDate('created_at', '>=', $startOfMonth)->sum('total');
        $thisMonthRevenue = $monthDealer + $monthPos + $monthCustom + $monthWeb;

        // 2. Realized Cash Collected
        $dealerPaid = (float) DealerOrder::sum('paid');
        $posPaid    = (float) PosOrder::sum('paid_amount');
        $customPaid = (float) CustomOrder::sum('paid');
        $webPaid    = (float) WebOrder::where('status', 'delivered')->sum('total');
        $totalCashCollected = $dealerPaid + $posPaid + $customPaid + $webPaid;

        $totalDealerWallets = (float) Dealer::sum('wallet_balance');

        // 3. Receivables / Outstanding Dues
        $dealerDue = (float) DealerOrder::where('status', '!=', 'cancelled')->sum('due');
        $posDue    = (float) PosOrder::sum('due_amount');
        $customDue = (float) CustomOrder::sum('due');
        $webDue    = (float) WebOrder::whereNotIn('status', ['delivered', 'canceled'])->sum('total');
        $totalReceivableDue = $dealerDue + $posDue + $customDue + $webDue;

        // 4. Order Counts
        $dealerOrdersCount = DealerOrder::count();
        $posOrdersCount    = PosOrder::count();
        $webOrdersCount    = WebOrder::count();
        $customOrdersCount = CustomOrder::count();
        $totalOrdersCount  = $dealerOrdersCount + $posOrdersCount + $webOrdersCount + $customOrdersCount;

        $todayOrdersCount = DealerOrder::whereDate('created_at', $today)->count()
            + PosOrder::whereDate('created_at', $today)->count()
            + WebOrder::whereDate('created_at', $today)->count()
            + CustomOrder::whereDate('created_at', $today)->count();

        return [
            'totalGrossRevenue'  => $totalGrossRevenue,
            'todayRevenue'       => $todayRevenue,
            'thisMonthRevenue'   => $thisMonthRevenue,
            'totalCashCollected' => $totalCashCollected,
            'totalDealerWallets' => $totalDealerWallets,
            'totalReceivableDue' => $totalReceivableDue,
            'totalOrdersCount'   => $totalOrdersCount,
            'todayOrdersCount'   => $todayOrdersCount,
            'channelRevenues'    => [
                'dealer' => $dealerRev,
                'pos'    => $posRev,
                'custom' => $customRev,
                'web'    => $webRev,
            ],
            'channelDues' => [
                'dealer' => $dealerDue,
                'pos'    => $posDue,
                'custom' => $customDue,
                'web'    => $webDue,
            ],
            'channelOrders' => [
                'dealer' => $dealerOrdersCount,
                'pos'    => $posOrdersCount,
                'custom' => $customOrdersCount,
                'web'    => $webOrdersCount,
            ],
        ];
    }

    /**
     * Get 12-Month Channel-wise Revenue Trends (Optimized with GROUP BY).
     */
    public function getMonthlyRevenueTrends(int $year): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Group by Month in single queries
        $dealerByMonth = DealerOrder::selectRaw('MONTH(created_at) as m, SUM(grand_total) as val')
            ->whereYear('created_at', $year)
            ->groupBy('m')
            ->pluck('val', 'm')
            ->toArray();

        $posByMonth = PosOrder::selectRaw('MONTH(created_at) as m, SUM(payable_amount) as val')
            ->whereYear('created_at', $year)
            ->groupBy('m')
            ->pluck('val', 'm')
            ->toArray();

        $customByMonth = CustomOrder::selectRaw('MONTH(created_at) as m, SUM(grand_total) as val')
            ->whereYear('created_at', $year)
            ->groupBy('m')
            ->pluck('val', 'm')
            ->toArray();

        $webByMonth = WebOrder::selectRaw('MONTH(created_at) as m, SUM(total) as val')
            ->whereYear('created_at', $year)
            ->groupBy('m')
            ->pluck('val', 'm')
            ->toArray();

        $dealerSeries = [];
        $posSeries    = [];
        $customSeries = [];
        $webSeries    = [];
        $totalSeries  = [];

        for ($i = 1; $i <= 12; $i++) {
            $d = (float) ($dealerByMonth[$i] ?? 0);
            $p = (float) ($posByMonth[$i] ?? 0);
            $c = (float) ($customByMonth[$i] ?? 0);
            $w = (float) ($webByMonth[$i] ?? 0);

            $dealerSeries[] = round($d, 2);
            $posSeries[]    = round($p, 2);
            $customSeries[] = round($c, 2);
            $webSeries[]    = round($w, 2);
            $totalSeries[]  = round($d + $p + $c + $w, 2);
        }

        return [
            'months'       => $months,
            'dealerSeries' => $dealerSeries,
            'posSeries'    => $posSeries,
            'customSeries' => $customSeries,
            'webSeries'    => $webSeries,
            'totalSeries'  => $totalSeries,
        ];
    }

    /**
     * Get Financial Health, Payables vs Receivables, and Cash Outflows.
     */
    public function getFinancialHealth(): array
    {
        $purchaseDue     = (float) Purchase::sum('due_amount');
        $invPurchaseDue  = (float) InventoryPurchase::sum('due_amount');
        $workerDue       = (float) WorkerPayment::where('status', '!=', 'paid')->sum('due_amount');
        $totalPayables   = $purchaseDue + $invPurchaseDue + $workerDue;

        // Lifetime Outflows / Expenses
        $totalPurchasesPaid    = (float) Purchase::sum('paid_amount');
        $totalInvPurchasesPaid = (float) InventoryPurchase::sum('paid_amount');
        $totalWorkerPayouts    = (float) WorkerPayment::sum('paid_amount');
        $totalRefunds          = (float) ReturnOrder::where('status', 'approved')->sum('refund_amount');
        $totalOutflow          = $totalPurchasesPaid + $totalInvPurchasesPaid + $totalWorkerPayouts + $totalRefunds;

        return [
            'purchaseDue'    => $purchaseDue,
            'invPurchaseDue' => $invPurchaseDue,
            'workerDue'      => $workerDue,
            'totalPayables'  => $totalPayables,
            'totalOutflow'   => $totalOutflow,
            'totalRefunds'   => $totalRefunds,
            'breakdown'      => [
                'Vendor Purchases Due'     => $purchaseDue,
                'Raw Material Due'         => $invPurchaseDue,
                'Worker Manufacturing Due' => $workerDue,
            ]
        ];
    }

    /**
     * Get Operations, Production & Inventory Health.
     */
    public function getOperationsAndInventory(): array
    {
        $totalProducts    = Product::count();
        $activeProducts   = Product::where('status', 1)->count();
        $outOfStockCount  = Product::where('stock', '<=', 0)->count();
        $lowStockCount    = Product::where('stock', '>', 0)->where('stock', '<=', 5)->count();

        // Production Jobs
        $totalManufactureJobs = Manufacture::count();
        $inProgressJobs       = Manufacture::where('status', 1)->count();
        $completedJobs        = Manufacture::where('status', 2)->count();
        $totalProducedQty     = (int) Manufacture::sum('received_qty');

        // Return Orders
        $totalReturnOrders    = ReturnOrder::count();
        $pendingReturnsCount  = ReturnOrder::where('status', 'pending')->count();
        $approvedReturnsCount = ReturnOrder::where('status', 'approved')->count();

        return [
            'totalProducts'        => $totalProducts,
            'activeProducts'       => $activeProducts,
            'outOfStockCount'      => $outOfStockCount,
            'lowStockCount'        => $lowStockCount,
            'totalManufactureJobs' => $totalManufactureJobs,
            'inProgressJobs'       => $inProgressJobs,
            'completedJobs'        => $completedJobs,
            'totalProducedQty'     => $totalProducedQty,
            'totalReturnOrders'    => $totalReturnOrders,
            'pendingReturnsCount'  => $pendingReturnsCount,
            'approvedReturnsCount' => $approvedReturnsCount,
        ];
    }

    /**
     * Get Recent Omnichannel Orders across Dealer, POS, Web, and Custom.
     */
    public function getRecentOmnichannelOrders(int $limit = 8): array
    {
        $recent = collect();

        // 1. Dealer Orders
        DealerOrder::with('dealer')->latest('id')->take($limit)->get()->each(function ($order) use ($recent) {
            $recent->push([
                'id'           => $order->id,
                'channel'      => 'Dealer',
                'channel_badge'=> 'primary',
                'order_no'     => $order->order_number,
                'party_name'   => $order->dealer->shop_name ?? ($order->dealer->name ?? 'Dealer'),
                'amount'       => (float) $order->grand_total,
                'paid'         => (float) $order->paid,
                'due'          => (float) $order->due,
                'status'       => $order->status ?? 'pending',
                'created_at'   => $order->created_at,
                'view_url'     => route('admin.dealer-order.show', $order->id),
            ]);
        });

        // 2. POS Orders
        PosOrder::with('customer')->latest('id')->take($limit)->get()->each(function ($order) use ($recent) {
            $recent->push([
                'id'           => $order->id,
                'channel'      => 'POS',
                'channel_badge'=> 'success',
                'order_no'     => $order->order_number,
                'party_name'   => $order->customer->name ?? 'Walk-in Customer',
                'amount'       => (float) $order->payable_amount,
                'paid'         => (float) $order->paid_amount,
                'due'          => (float) $order->due_amount,
                'status'       => $order->order_status ?? 'completed',
                'created_at'   => $order->created_at,
                'view_url'     => route('admin.pos-order.show', $order->id),
            ]);
        });

        // 3. Web Orders
        WebOrder::with('address')->latest('id')->take($limit)->get()->each(function ($order) use ($recent) {
            $recent->push([
                'id'           => $order->id,
                'channel'      => 'Web',
                'channel_badge'=> 'info',
                'order_no'     => '#' . $order->invoice_no,
                'party_name'   => $order->address->name ?? 'Web Shopper',
                'amount'       => (float) $order->total,
                'paid'         => $order->status === 'delivered' ? (float) $order->total : 0,
                'due'          => $order->status !== 'delivered' ? (float) $order->total : 0,
                'status'       => $order->status ?? 'pending',
                'created_at'   => $order->created_at,
                'view_url'     => route('admin.web-order.show', $order->id),
            ]);
        });

        // 4. Custom Orders
        CustomOrder::with('customer')->latest('id')->take($limit)->get()->each(function ($order) use ($recent) {
            $recent->push([
                'id'           => $order->id,
                'channel'      => 'Custom',
                'channel_badge'=> 'warning',
                'order_no'     => $order->order_number,
                'party_name'   => $order->customer->name ?? 'Custom Client',
                'amount'       => (float) $order->grand_total,
                'paid'         => (float) $order->paid,
                'due'          => (float) $order->due,
                'status'       => $order->status ?? 'pending',
                'created_at'   => $order->created_at,
                'view_url'     => route('admin.custom-order.show', $order->id),
            ]);
        });

        return $recent->sortByDesc('created_at')->take($limit)->values()->all();
    }

    /**
     * Get Top Selling Products.
     */
    public function getTopProducts(int $limit = 5): array
    {
        $topSold = DealerOrderItem::selectRaw('product_id, SUM(qty) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take($limit)
            ->pluck('total_qty', 'product_id');

        if ($topSold->isEmpty()) {
            return [];
        }

        $products = Product::with('category')->whereIn('id', $topSold->keys())->get();

        return $products->map(function ($product) use ($topSold) {
            return [
                'id'          => $product->id,
                'name'        => $product->name,
                'category'    => $product->category->name ?? 'Hardware & Tools',
                'image'       => $product->featured_image ? asset(config('imagepath.product') . '/' . $product->featured_image) : asset('images/no-image.png'),
                'price'       => (float) $product->selling_price,
                'stock'       => (int) $product->stock,
                'sold_qty'    => (int) ($topSold[$product->id] ?? 0),
                'total_sales' => (float) ($product->selling_price * ($topSold[$product->id] ?? 0)),
            ];
        })->sortByDesc('sold_qty')->values()->all();
    }

    /**
     * Get Out-of-Stock and Critical Inventory Items for instant restock action.
     */
    public function getCriticalInventory(int $limit = 6): array
    {
        return Product::with('category')
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->take($limit)
            ->get()
            ->map(function ($p) {
                return [
                    'id'       => $p->id,
                    'name'     => $p->name,
                    'category' => $p->category->name ?? 'General',
                    'stock'    => (int) $p->stock,
                    'price'    => (float) $p->selling_price,
                    'status'   => $p->stock <= 0 ? 'Out of Stock' : 'Low Stock (' . $p->stock . ' left)',
                ];
            })->all();
    }
}
