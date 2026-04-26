<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use App\Models\PosOrder;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;


class ReportController extends Controller
{
    /**
     * Custom Order Sales Report
     */
    public function customOrderReport(Request $request)
    {
        if ($request->ajax()) {
            $query = CustomOrder::with('customer')->latest();

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
            }

            if ($request->order_number) {
                $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_info', function($r) {
                    return $r->customer ? '<strong>'.$r->customer->name.'</strong><br><small>'.$r->customer->phone.'</small>' : '-';
                })
                ->addColumn('financials', function($r) {
                    return 'Total: ৳'.number_format($r->grand_total, 2).'<br>'.
                           'Paid: ৳'.number_format($r->paid, 2).'<br>'.
                           'Due: ৳'.number_format($r->due, 2);
                })
                ->addColumn('status', function($r) {
                    $class = [
                        'pending' => 'warning',
                        'order_confirm' => 'info',
                        'delivered' => 'success',
                        'cancelled' => 'danger'
                    ][$r->status] ?? 'primary';
                    return '<span class="badge badge-light-'.$class.' text-uppercase">'.str_replace('_', ' ', $r->status).'</span>';
                })
                ->addColumn('date', fn($r) => $r->order_date->format('d M, Y'))
                ->rawColumns(['customer_info', 'financials', 'status'])
                ->make(true);
        }
        return view('admin.report.custom_order_report');
    }

    /**
     * POS Order Sales Report
     */
    public function posOrderReport(Request $request)
    {
        if ($request->ajax()) {
            $query = PosOrder::with('customer')->latest();

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
            }

            if ($request->order_number) {
                $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
            }

            if ($request->status) {
                $query->where('order_status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_info', function($r) {
                    return $r->customer ? '<strong>'.$r->customer->name.'</strong><br><small>'.$r->customer->phone.'</small>' : 'Walk-in';
                })
                ->addColumn('financials', function($r) {
                    return 'Total: ৳'.number_format($r->total_amount, 2).'<br>'.
                           'Paid: ৳'.number_format($r->paid_amount, 2).'<br>'.
                           'Due: ৳'.number_format($r->due_amount, 2);
                })
                ->addColumn('order_status', function($r) {
                    $class = [
                        'completed' => 'success',
                        'cancelled' => 'danger'
                    ][$r->order_status] ?? 'primary';
                    return '<span class="badge badge-light-'.$class.' text-uppercase">'.$r->order_status.'</span>';
                })
                ->addColumn('date', fn($r) => date('d M, Y', strtotime($r->order_date)))
                ->rawColumns(['customer_info', 'financials', 'order_status'])
                ->make(true);
        }
        return view('admin.report.pos_order_report');
    }

    /**
     * Custom Order Profit/Loss Report
     */
    public function customProfitLossReport(Request $request)
    {
        if ($request->ajax()) {
            $query = CustomOrder::with(['customer', 'purchases'])->latest();

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
            }

            $query->where('status', 'delivered');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('revenue', fn($r) => $r->grand_total)
                ->addColumn('cost', function($r) {
                    return $r->purchases->sum('grand_total');
                })
                ->addColumn('profit', function($r) {
                    $cost = $r->purchases->sum('grand_total');
                    $profit = $r->grand_total - $cost;
                    $class = $profit >= 0 ? 'text-success' : 'text-danger';
                    return '<span class="'.$class.' font-weight-bold">৳'.number_format($profit, 2).'</span>';
                })
                ->addColumn('date', fn($r) => $r->order_date->format('d M, Y'))
                ->rawColumns(['profit'])
                ->make(true);
        }
        return view('admin.report.custom_profit_loss');
    }

    /**
     * Export Custom Order Profit/Loss to Excel
     */
    public function exportCustomProfitLossExcel(Request $request)
    {
        $fileName = 'custom_profit_loss_' . date('Y-m-d') . '.csv';
        $query = CustomOrder::with(['customer', 'purchases'])->latest();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        $query->where('status', 'delivered');
        $orders = $query->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Order ID', 'Date', 'Revenue', 'Cost', 'Profit/Loss');

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($orders as $order) {
                $cost = $order->purchases->sum('grand_total');
                fputcsv($file, array(
                    $order->order_number,
                    $order->order_date->format('Y-m-d'),
                    $order->grand_total,
                    $cost,
                    $order->grand_total - $cost
                ));
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Custom Order Profit/Loss to PDF
     */
    public function exportCustomProfitLossPdf(Request $request)
    {
        $query = CustomOrder::with(['customer', 'purchases'])->latest();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        $query->where('status', 'delivered');
        $orders = $query->get();
        $pdf = Pdf::loadView('admin.report.pdf.custom_profit_loss_pdf', compact('orders'));
        return $pdf->download('custom_profit_loss_' . date('Y-m-d') . '.pdf');
    }

    /**
     * POS Order Profit/Loss Report
     */
    public function posProfitLossReport(Request $request)
    {
        if ($request->ajax()) {
            $query = PosOrder::with(['items.product'])->latest();

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
            }

            $query->where('order_status', 'completed');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('revenue', fn($r) => $r->total_amount)
                ->addColumn('cost', function($r) {
                    $total_cost = 0;
                    foreach($r->items as $item) {
                        $total_cost += ($item->product->cost_price ?? 0) * $item->quantity;
                    }
                    return $total_cost;
                })
                ->addColumn('profit', function($r) {
                    $total_cost = 0;
                    foreach($r->items as $item) {
                        $total_cost += ($item->product->cost_price ?? 0) * $item->quantity;
                    }
                    $profit = $r->total_amount - $total_cost;
                    $class = $profit >= 0 ? 'text-success' : 'text-danger';
                    return '<span class="'.$class.' font-weight-bold">৳'.number_format($profit, 2).'</span>';
                })
                ->addColumn('date', fn($r) => date('d M, Y', strtotime($r->order_date)))
                ->rawColumns(['profit'])
                ->make(true);
        }
        return view('admin.report.pos_profit_loss');
    }

    /**
     * Export POS Order Profit/Loss to Excel
     */
    public function exportPosProfitLossExcel(Request $request)
    {
        $fileName = 'pos_profit_loss_' . date('Y-m-d') . '.csv';
        $query = PosOrder::with(['items.product'])->latest();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        $query->where('order_status', 'completed');
        $orders = $query->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Order ID', 'Date', 'Revenue', 'Cost', 'Profit/Loss');

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($orders as $order) {
                $total_cost = 0;
                foreach($order->items as $item) {
                    $total_cost += ($item->product->cost_price ?? 0) * $item->quantity;
                }
                fputcsv($file, array(
                    $order->order_number,
                    date('Y-m-d', strtotime($order->order_date)),
                    $order->total_amount,
                    $total_cost,
                    $order->total_amount - $total_cost
                ));
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export POS Order Profit/Loss to PDF
     */
    public function exportPosProfitLossPdf(Request $request)
    {
        $query = PosOrder::with(['items.product'])->latest();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        $query->where('order_status', 'completed');
        $orders = $query->get();
        $pdf = Pdf::loadView('admin.report.pdf.pos_profit_loss_pdf', compact('orders'));
        return $pdf->download('pos_profit_loss_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Custom Order Sales to CSV
     */
    public function exportCustomSalesExcel(Request $request)
    {
        $fileName = 'custom_sales_' . date('Y-m-d') . '.csv';
        $query = CustomOrder::with('customer')->latest();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        if ($request->order_number) {
            $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $orders = $query->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Order ID', 'Date', 'Customer', 'Grand Total', 'Paid', 'Due', 'Status');

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, array(
                    $order->order_number,
                    $order->order_date->format('Y-m-d'),
                    $order->customer->name ?? 'N/A',
                    $order->grand_total,
                    $order->paid,
                    $order->due,
                    $order->status
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Custom Order Sales to PDF
     */
    public function exportCustomSalesPdf(Request $request)
    {
        $query = CustomOrder::with('customer')->latest();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        if ($request->order_number) {
            $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $orders = $query->get();

        $pdf = Pdf::loadView('admin.report.pdf.custom_order_sales_pdf', compact('orders'));
        return $pdf->download('custom_sales_report_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export POS Sales to CSV
     */
    public function exportPosSalesExcel(Request $request)
    {
        $fileName = 'pos_sales_' . date('Y-m-d') . '.csv';
        $query = PosOrder::with('customer')->latest();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        if ($request->order_number) {
            $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
        }
        if ($request->status) {
            $query->where('order_status', $request->status);
        }
        $orders = $query->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Order ID', 'Date', 'Customer', 'Total Amount', 'Paid', 'Due', 'Status');

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, array(
                    $order->order_number,
                    date('Y-m-d', strtotime($order->order_date)),
                    $order->customer->name ?? 'Walk-in',
                    $order->total_amount,
                    $order->paid_amount,
                    $order->due_amount,
                    $order->order_status
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export POS Sales to PDF
     */
    public function exportPosSalesPdf(Request $request)
    {
        $query = PosOrder::with('customer')->latest();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        if ($request->order_number) {
            $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
        }
        if ($request->status) {
            $query->where('order_status', $request->status);
        }
        $orders = $query->get();

        $pdf = Pdf::loadView('admin.report.pdf.pos_order_sales_pdf', compact('orders'));
        return $pdf->download('pos_sales_report_' . date('Y-m-d') . '.pdf');
    }
}
