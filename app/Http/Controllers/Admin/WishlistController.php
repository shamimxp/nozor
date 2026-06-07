<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Wishlist::with(['user', 'product'])->select('wishlists.*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_info', function($row){
                    return $row->user ? $row->user->name . '<br><small>' . $row->user->phone . '</small>' : 'N/A';
                })
                ->addColumn('product_info', function($row){
                    return $row->product ? $row->product->name : 'N/A';
                })
                ->addColumn('date', function($row){
                    return $row->created_at->format('d M, Y h:i A');
                })
                ->addColumn('action', function($row){
                    return '<button class="btn btn-sm btn-danger delete-wishlist" data-id="' . $row->id . '">
                                <i data-feather="trash"></i> Delete
                            </button>';
                })
                ->rawColumns(['user_info', 'action'])
                ->make(true);
        }

        return view('admin.wishlist.index');
    }

    public function destroy($id)
    {
        $wishlist = Wishlist::find($id);
        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['success' => true, 'message' => 'Wishlist record deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Wishlist record not found.'], 404);
    }
}
