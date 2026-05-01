    public function vendorHistory(Request $request)
    {
        if ($request->ajax()) {
            $vendors = Vendor::all();
            $data = [];
            foreach ($vendors as $vendor) {
                // For inventory purchase:
                $total_purchased = InventoryPurchase::where('vendor_id', $vendor->id)->sum('total_amount');
                $total_paid = InventoryPurchasePayment::where('vendor_id', $vendor->id)->sum('amount');
                $total_due = $total_purchased - $total_paid;

                $data[] = [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'company' => $vendor->company_name,
                    'phone' => $vendor->phone,
                    'total_purchased' => number_format($total_purchased, 2),
                    'total_paid' => number_format($total_paid, 2),
                    'total_due' => number_format($total_due, 2),
                ];
            }
            return DataTables::of($data)->addIndexColumn()->make(true);
        }
        return view('admin.inventory_purchase.vendor_history');
    }

    public function vendorHistoryPdf(Request $request)
    {
        $vendors = Vendor::all();
        $data = [];
        foreach ($vendors as $vendor) {
            $total_purchased = InventoryPurchase::where('vendor_id', $vendor->id)->sum('total_amount');
            $total_paid = InventoryPurchasePayment::where('vendor_id', $vendor->id)->sum('amount');
            $total_due = $total_purchased - $total_paid;

            $data[] = [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'company' => $vendor->company_name,
                'phone' => $vendor->phone,
                'total_purchased' => $total_purchased,
                'total_paid' => $total_paid,
                'total_due' => $total_due,
            ];
        }

        $pdf = Pdf::loadView('admin.inventory_purchase.vendor_history_pdf', compact('data'))
            ->setPaper('a4', 'portrait');
        return $pdf->download('vendor_inventory_due_list_' . date('Y-m-d') . '.pdf');
    }
