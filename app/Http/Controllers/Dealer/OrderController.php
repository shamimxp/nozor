<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\DealerOrder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DealerOrder::with('items')->where('dealer_id', auth('dealer')->id())->latest();

            if ($request->order_number) {
                $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
            }
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
            }
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $orders = $query->get();
            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('order_info', function($r) {
                    return '<strong>Order Num:</strong> ' . $r->order_number . '<br>' .
                           '<strong>Date:</strong> ' . Carbon::parse($r->order_date)->format('d M, Y');
                })
                ->addColumn('totals', function($r) {
                    return 'Total: ৳' . number_format($r->grand_total, 2) . '<br>' .
                            '<span class="text-success font-weight-bold">Paid: ৳' . number_format($r->paid, 2) . '</span><br>' .
                           '<small class="' . ($r->due > 0 ? 'text-danger font-weight-bold' : 'text-success') . '">Due: ৳' . number_format($r->due, 2) . '</small>';
                })
                ->addColumn('status_badge', function ($r) {
                    $badge = 'badge-light-info';
                    if ($r->status == 'pending') $badge = 'badge-light-warning';
                    if ($r->status == 'delivered') $badge = 'badge-light-success';
                    if ($r->status == 'cancelled') $badge = 'badge-light-danger';
                    return '<span class="badge badge-pill '.$badge.'">'.ucfirst($r->status).'</span>';
                })
                ->addColumn('action', function ($r) {
                    $viewBtn = '<a href="' . route('dealer.orders.show', $r->id) . '" class="btn btn-info btn-sm mr-50" title="View"><i data-feather="eye"></i></a>';
                    $pdfBtn = '<a href="' . route('dealer.orders.pdf', $r->id) . '" class="btn btn-primary btn-sm" title="Download PDF"><i data-feather="download"></i></a>';
                    return '<div class="d-flex align-items-center">' . $viewBtn . $pdfBtn . '</div>';
                })
                ->rawColumns(['order_info', 'totals', 'status_badge', 'action'])
                ->make(true);
        }
        return view('dealer.orders.index');
    }

    public function show($id)
    {
        $order = DealerOrder::with(['items.product', 'dealer'])->where('dealer_id', auth('dealer')->id())->findOrFail($id);
        return view('dealer.orders.show', compact('order'));
    }

    public function downloadPdf($id)
    {
        $order = DealerOrder::with(['items.product', 'dealer'])->where('dealer_id', auth('dealer')->id())->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.dealer-order.pdf', compact('order'));
        return $pdf->download('order_' . $order->order_number . '.pdf');
    }
}
