<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Admin\Concerns\AuthorizesAdminModule;
use App\Http\Controllers\Admin\Concerns\HandlesTrash;
use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Services\Deletion\SoftDeleteService;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    use HandlesTrash, AuthorizesAdminModule;

    protected function trashModuleKey(): string
    {
        return 'subscriber';
    }

    public function __construct()
    {
        $this->authorizeAdminModule('subscriber');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Subscriber::latest()->get();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return perm_html('subscriber.delete', '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger deleteSubscriber">Delete</a>');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.subscribers.index');
    }

    public function destroy($id, SoftDeleteService $softDeleteService)
    {
        return $this->softDeleteRecord($id, $softDeleteService);
    }
}
