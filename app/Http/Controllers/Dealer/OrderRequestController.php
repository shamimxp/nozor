<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\OrderRequest;
use App\Services\ProductPriceCalculator;
use Carbon\Carbon;

class OrderRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = OrderRequest::with('items')->where('dealer_id', auth('dealer')->id())->latest();
            
            return datatables()->of($query)
                ->addColumn('total_items', function ($row) {
                    return $row->items()->sum('requested_qty');
                })
                 ->addColumn('request_date', function ($row) {
                   return Carbon::parse($row->created_at)->format('d M Y h:i A');
                })
                ->addColumn('status', function ($row) {
                    $badge = $row->status == 'pending' ? 'badge-warning' : ($row->status == 'completed' ? 'badge-success' : 'badge-info');
                    return '<span class="badge badge-pill '.$badge.'">'.ucfirst(str_replace('_', ' ', $row->status)).'</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('dealer.order-requests.show', $row->id).'" class="btn btn-sm btn-info" title="View"><i data-feather="eye"></i></a> ';
                    if($row->status == 'pending') {
                        $btn .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteRequest('.$row->id.')" title="Delete"><i data-feather="trash-2"></i></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('dealer.order-requests.index');
    }

    public function show($id, ProductPriceCalculator $calculator)
    {
        $orderRequest = OrderRequest::with(['items.product'])->where('dealer_id', auth('dealer')->id())->findOrFail($id);
        $dealerId = auth('dealer')->id();
        
        $totalAmount = 0;
        foreach($orderRequest->items as $item) {
            $product = $item->product;
            if ($product->is_manufacturer) {
                try {
                    $calc = $calculator->calculate($product->id, $dealerId);
                    $price = $calc['dealer_price'];
                } catch (\Exception $e) {
                    $price = $product->discount_price > 0 ? $product->discount_price : $product->selling_price;
                }
            } else {
                $price = $product->discount_price > 0 ? $product->discount_price : $product->selling_price;
            }
            $item->unit_price = $price;
            $item->total_price = $price * $item->requested_qty;
            $totalAmount += $item->total_price;
        }

        return view('dealer.order-requests.show', compact('orderRequest', 'totalAmount'));
    }

    public function destroy($id)
    {
        $orderRequest = OrderRequest::where('dealer_id', auth('dealer')->id())->where('status', 'pending')->findOrFail($id);
        $orderRequest->delete();
        return response()->json(['status' => 'success', 'message' => 'Order request deleted successfully.']);
    }
}
