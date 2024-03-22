<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Role;
use Hash;
use App\Actions\Fortify\PasswordValidationRules;

class ManageUserController extends Controller
{
    use PasswordValidationRules;
    
    public function __construct()
    {
        $this->middleware('can:Manage User - Read', ['only' => ['index', 'show']]);
        $this->middleware('can:Manage User - Create', ['only' => ['create', 'store']]);
        $this->middleware('can:Manage User - Edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:Manage User - Delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $user = User::with('roles')
        ->when($search, function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        })
        ->orderByDesc('id')
        ->paginate(5)
        ->withQueryString();

        return Inertia::render('Admin/ManageUser/Index', [
            'users' => $user,
            'can' => [
                'create' => Auth::user()->can('Manage User - Create'),
                'edit' => Auth::user()->can('Manage User - Edit'),
                'delete' => Auth::user()->can('Manage User - Delete'),
            ]
        ]);
    }


    public function create()
    {
        $roles = Role::get();
        return Inertia::render('Admin/ManageUser/Create', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => $this->passwordRules(),
            'role' => 'required',
        ]);

        $role = Role::where('name', $request->input('role'))->first();
        $data = [
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password)
        ];
        $user = User::create($data);
        $user->assignRole($request->role['name']);
        $request->session()->flash('message', 'User has been created successfully!');

        return redirect()->route('user.index');
    }

    public function show(string $id)
    {
        
    }

    public function edit(string $id)
    {
        $role = Role::get()->map(function ($name) {
            return $name->name;
        });

        $user = User::with('roles')->whereId($id)->first();
        return Inertia::render('Admin/ManageUser/Edit', [
            'users' => $user,
            'roles' => $role
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => $this->passwordRules(),
            'role' => 'required',
        ]);
       
        $data = [
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password)
        ];

        $user = User::findOrFail($id);
        $user->update($data);
        $user->syncRoles($request->role);

        $request->session()->flash('message', 'User has been updated successfully!');
        return redirect()->route('user.index');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        
        return redirect()->back();
    }
}
