<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Admin\Concerns\AuthorizesAdminModule;
use App\Http\Controllers\Admin\Concerns\HandlesTrash;
use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Services\Deletion\SoftDeleteService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WishlistController extends Controller
{
    use HandlesTrash, AuthorizesAdminModule;

    protected function trashModuleKey(): string
    {
        return 'wishlist';
    }

    public function __construct()
    {
        $this->authorizeAdminModule('wishlist');
    }

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
                    return perm_html('wishlist.delete', '<button class="btn btn-sm btn-danger delete-wishlist" data-id="' . $row->id . '">
                                <i data-feather="trash"></i> Delete
                            </button>');
                })
                ->rawColumns(['user_info', 'action'])
                ->make(true);
        }

        return view('admin.wishlist.index');
    }

    public function destroy($id, SoftDeleteService $softDeleteService)
    {
        return $this->softDeleteRecord($id, $softDeleteService);
    }
}
