<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Solo usuarios del local (con algún rol asignado), excluye clientes
        $query = User::role(['admin', 'vendedor', 'deposito'])->with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $user)
    {
        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $user->load('roles');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($user->id !== auth()->id()) {
            $user->syncRoles([$request->role]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        // No permitir que el admin se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'No podés eliminar tu propio usuario.');
        }

        // No permitir eliminar el último admin
        if ($user->hasRole('admin') && User::role('admin')->count() === 1) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'No se puede eliminar el último administrador del sistema.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}