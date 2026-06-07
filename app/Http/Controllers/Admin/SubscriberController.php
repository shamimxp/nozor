<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Subscriber::latest()->get();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $deleteUrl = route('admin.subscribers.destroy', $row->id);
                    $btn = '<form action="'.$deleteUrl.'" method="POST" class="d-inline-block">
                                '.csrf_field().'
                                '.method_field("DELETE").'
                                <button type="submit" class="btn btn-sm btn-brand rounded font-sm mt-15" onclick="return confirm(\'Are you sure you want to delete this subscriber?\');">Delete</button>
                            </form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('admin.subscribers.index');
    }

    public function destroy($id)
    {
        Subscriber::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Subscriber deleted successfully.');
    }
}
