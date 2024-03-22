<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Permission;

class ManageRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:Manage Role - Read', ['only' => ['index', 'show']]);
        $this->middleware('can:Manage Role - Create', ['only' => ['create', 'store']]);
        $this->middleware('can:Manage Role - Edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:Manage Role - Delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $roles = Role::when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('Admin/ManageRole/Index', [
            'search' => $search,
            'roles' => $roles,
            'can' => [
                'create' => Auth::user()->can('Manage Role - Create'),
                'edit' => Auth::user()->can('Manage Role - Edit'),
                'delete' => Auth::user()->can('Manage Role - Delete'),
            ]
        ]);
    }

    public function create(Request $request)
    {
        $search = $request->search;
        $permissionList = Permission::when($search, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->get();

        $defaultPermissions = $permissionList->groupBy(function ($item) {
            return preg_replace('/\s*\-\s*(Read|Create|Edit|Delete)\s*/i', '', $item->name);
        });

        return Inertia::render('Admin/ManageRole/Create', [
            'search' => $search,
            'defaultPermissions' => $defaultPermissions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);
        $permissions = $request->input('permissions');
        $selectedPermissions = array_keys(array_filter($permissions));

        $role2 = Role::create(['name' => $request->input('name'), 'description' => $request->input('description'), 'guard_name' => 'web',]);
        foreach ($selectedPermissions as $permission) {
            $role2->givePermissionTo($permission);
        }

        $request->session()->flash('message', 'Role has been created successfully!');

        return redirect()->route('role.index');
    }

    public function edit(Request $request, $id)
    {
        $search = $request->search;
        $roleModel = Role::with('permissions')->find($id);
        $permissionList = Permission::when($search, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->get();

        $rolePermissions = $roleModel->permissions;

        $groupedPermissions = $rolePermissions->groupBy(function ($permission) {
            return preg_replace('/\s*\-\s*(Read|Create|Edit|Delete)\s*/i', '', $permission->name);
        });

        $defaultPermissions = $permissionList->groupBy(function ($item) {
            return preg_replace('/\s*\-\s*(Read|Create|Edit|Delete)\s*/i', '', $item->name);
        });

        return Inertia::render('Admin/ManageRole/Edit', [
            'search' => $search,
            'data' => $roleModel,
            'availablePermissions' => $groupedPermissions,
            'defaultPermissions' => $defaultPermissions
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        $role = Role::findOrFail($id);

        $role->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);
        $updatedPermissions = [];
        foreach ($request->input('permissions') as $arrayName => $permissions) {
            
            foreach ($permissions as $permission) {
                $updatedPermissions[] = $permission;
            }
        }
        $role->syncPermissions($updatedPermissions);

        $request->session()->flash('message', 'Role has been updated successfully!');

        return redirect()->route('role.index');
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if ($role) {
            $role->delete();
    
            $role->permissions()->detach();
        }

        return redirect()->route('role.index');
    }
}
