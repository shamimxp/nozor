<?php

namespace App\Exports;

use App\Models\Manufacture;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManufactureExport
{
    public function export(Request $request)
    {
        $response = new StreamedResponse(function() use ($request) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM to ensure proper rendering in Excel if needed, or omit. I'll omit it for pure CSV.
            // Actually they asked to revert back to exactly what I had before the HTML attempt.

            // Headers
            fputcsv($handle, [
                'Invoice No', 'Product', 'Dealer Name', 'Dealer Phone', 'Worker', 
                'Quantity', 'Body Total', 'Finishing Total', 'Grand Total', 
                'Status', 'Date Created'
            ]);

            $query = Manufacture::with(['product', 'dealer', 'worker']);

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('created_at', '>=', $request->from_date)
                      ->whereDate('created_at', '<=', $request->to_date);
            }
            if ($request->filled('worker_id')) {
                $query->where('worker_id', $request->worker_id);
            }
            if ($request->filled('dealer_id')) {
                $query->where('dealer_id', $request->dealer_id);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('part_type')) {
                if ($request->part_type == 'body') {
                    $query->where('body_total', '>', 0);
                } elseif ($request->part_type == 'finishing') {
                    $query->where('finishing_total', '>', 0);
                }
            }

            $query->orderBy('id', 'desc')->chunk(500, function($manufactures) use ($handle) {
                foreach ($manufactures as $row) {
                    $status = 'Pending';
                    if ($row->status == 2) $status = 'Completed';
                    elseif ($row->status == 1 || $row->is_confirm == 1) $status = 'Confirmed';

                    fputcsv($handle, [
                        $row->invoice_no,
                        $row->product->name ?? 'N/A',
                        $row->dealer_name ?? ($row->dealer->name ?? 'N/A'),
                        $row->dealer_phone ?? ($row->dealer->phone ?? 'N/A'),
                        $row->worker->name ?? 'N/A',
                        $row->manufacture_qty,
                        number_format($row->body_total, 2, '.', ''), // Removed commas for strict CSV
                        number_format($row->finishing_total, 2, '.', ''),
                        number_format($row->grand_total, 2, '.', ''),
                        $status,
                        $row->created_at ? $row->created_at->format('d M Y h:i A') : ''
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="manufacture_export_'.date('Ymd_His').'.csv"');

        return $response;
    }
}
