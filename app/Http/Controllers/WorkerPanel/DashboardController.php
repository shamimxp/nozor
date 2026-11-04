<?php

namespace App\Http\Controllers\WorkerPanel;

use App\Http\Controllers\Controller;
use App\Models\Manufacture;
use App\Models\WorkerPayment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $worker = Auth::guard('worker')->user();
        $workerId = $worker->id;

        $totalOrders = Manufacture::where('worker_id', $workerId)->count();
        $pendingOrders = Manufacture::where('worker_id', $workerId)->whereNull('collected_by')->count();
        $collectedOrders = Manufacture::where('worker_id', $workerId)->whereNotNull('collected_by')->count();

        $totalBilled = (float) WorkerPayment::where('worker_id', $workerId)->sum('total_amount');
        $totalPaid = (float) WorkerPayment::where('worker_id', $workerId)->sum('paid_amount');
        $totalDue = (float) WorkerPayment::where('worker_id', $workerId)->sum('due_amount');

        $months = [];
        $orderCounts = [];
        $paidAmounts = [];
        $currentYear = (int) date('Y');

        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
            $orderCounts[] = Manufacture::where('worker_id', $workerId)
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', $currentYear)
                ->count();
            $paidAmounts[] = (float) WorkerPayment::where('worker_id', $workerId)
                ->whereMonth('date', $i)
                ->whereYear('date', $currentYear)
                ->sum('paid_amount');
        }

        $recentOrders = Manufacture::with(['product', 'items.product'])
            ->where('worker_id', $workerId)
            ->latest('id')
            ->limit(5)
            ->get();

        return view('mypanel.dashboard', compact(
            'worker',
            'totalOrders',
            'pendingOrders',
            'collectedOrders',
            'totalBilled',
            'totalPaid',
            'totalDue',
            'months',
            'orderCounts',
            'paidAmounts',
            'recentOrders'
        ));
    }
}
