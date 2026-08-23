<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['opd', 'roles']);

        if ($s = $request->get('q')) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")->orWhere('nip', 'like', "%{$s}%"));
        }
        if ($request->filled('role')) {
            $query->role($request->role);
        }
        if ($request->filled('opd_id')) $query->where('opd_id', $request->opd_id);

        $items = $query->latest()->paginate(10)->appends($request->query());
        $roles = Role::orderBy('name')->get();
        $opds = Opd::where('is_active', true)->orderBy('nama')->get();

        return view('users.index', compact('items', 'roles', 'opds'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $opds = Opd::where('is_active', true)->orderBy('nama')->get();
        return view('users.form', ['item' => new User(), 'roles' => $roles, 'opds' => $opds]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'opd_id' => 'nullable|exists:opd,id',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $role = $data['role'];
        unset($data['role']);

        $user = User::create($data);
        $user->assignRole($role);

        activity()->performedOn($user)->log('Menambah user baru: ' . $user->name);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $opds = Opd::where('is_active', true)->orderBy('nama')->get();
        return view('users.form', ['item' => $user, 'roles' => $roles, 'opds' => $opds]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'opd_id' => 'nullable|exists:opd,id',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $role = $data['role'];
        unset($data['role']);
        if (empty($data['password'])) unset($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        $user->update($data);
        $user->syncRoles([$role]);

        activity()->performedOn($user)->log('Memperbarui user: ' . $user->name);

        return redirect()->route('users.index')->with('success', 'User diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        $user->delete();
        return back()->with('success', 'User dihapus.');
    }

    public function resetPassword(User $user)
    {
        $password = 'Password123!';
        $user->update(['password' => $password]);
        activity()->performedOn($user)->log('Reset password user');
        return back()->with('success', "Password direset ke: {$password}");
    }

    public function loginActivities(User $user)
    {
        $items = $user->loginActivities()->latest()->paginate(10);
        return view('users.login-activities', compact('items', 'user'));
    }
}
