<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesTrash;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\Deletion\SoftDeleteService;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    use HandlesTrash;

    protected function trashModuleKey(): string
    {
        return 'contact_message';
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ContactMessage::latest()->get();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger deleteContactMessage">Delete</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.contact_messages.index');
    }

    public function destroy($id, SoftDeleteService $softDeleteService)
    {
        return $this->softDeleteRecord($id, $softDeleteService);
    }
}
