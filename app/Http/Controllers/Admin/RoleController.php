<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role.list')->only('index');
        $this->middleware('can:role.create')->only(['create', 'store']);
        $this->middleware('can:role.edit')->only(['edit', 'update']);
        $this->middleware('can:role.delete')->only(['destroy']);
    }

    public function index()
    {
        if (\request()->ajax()) {
            $roles = Role::where('guard_name', 'admin')
                ->where('name', '!=', config('admin_permissions.super_admin_role', 'Super Admin'))
                ->orderBy('id', 'desc')
                ->get();
            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('action', function ($role) {
                        return view('admin.access_control.role.action_button', compact('role'));
                })
                ->rawColumns(['action'])
                ->tojson();
        }
        return view('admin.access_control.role.index');
    }

    public function create()
    {
        $permissions = $this->groupedAdminPermissions();
        return view('admin.access_control.role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:24',
                'min:2',
                Rule::unique('roles')->where('guard_name', 'admin')
            ],
            'permissions' => 'required|array',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'admin']);
        $permissions = $request->input('permissions');
        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
            Toastr::success('Role created successfully!', 'Success');
            return redirect()->route('roles.index');
        }

        return back()->withInput();
    }

//    public function show($id)
//    {
//        //
//    }

    public function edit($id)
    {
        try {
            $role = Role::findById(decrypt($id));
            $permissions = $this->groupedAdminPermissions();
            return view('admin.access_control.role.edit', compact('role', 'permissions'));
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    /**
     * Admin permissions grouped by module, actions in a stable UX order.
     */
    protected function groupedAdminPermissions()
    {
        $actionOrder = array_flip(config('admin_permissions.actions', [
            'list', 'create', 'edit', 'show', 'delete', 'force-delete', 'deleted-list', 'restore',
        ]));

        return Permission::where('guard_name', 'admin')
            ->get()
            ->sortBy(function (Permission $permission) use ($actionOrder) {
                $action = explode('.', $permission->name, 2)[1] ?? $permission->name;
                return $actionOrder[$action] ?? 999;
            })
            ->groupBy('module_name')
            ->sortKeys();
    }

    public function update(Request $request, $id)
    {
//        return $request->all();
        $decrypt = decrypt($id);
        try {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:24',
                    'min:2',
                    Rule::unique('roles')->ignore($decrypt)->where('guard_name', 'admin')
                ],
                'permissions' => 'required|array',
            ]);

            $role = Role::findById($decrypt);
            $permissions = $request->input('permissions');
            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
                $role->name = $request->name;
                $role->update();
                Toastr::success('Update successfully!','Success');
                return back();
            }
        } catch (DecryptException $e) {
            abort(404);
        }
    }

//    public function destroy($id)
//    {
//        //
//    }
}
