<?php
namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('warehouse:id,name')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            }))
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->has('is_active') && $request->is_active !== '', fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);

        return view('superadmin.users.index', compact('users', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        return view('superadmin.users.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:150',
            'email'        => 'required|email|max:150|unique:users,email',
            'phone'        => 'nullable|string|max:20',
            'role'         => 'required|in:super_admin,admin,user',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'password'     => ['required', Password::min(8)->letters()->numbers()],
            'is_active'    => 'nullable|boolean',
        ]);

        User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'role'         => $request->role,
            'warehouse_id' => $request->warehouse_id,
            'password'     => Hash::make($request->password),
            'is_active'    => $request->boolean('is_active', true),
        ]);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->load('warehouse:id,name');

        $activityCount = $user->activityLogs()->count();
        $lastActivity  = $user->activityLogs()->latest()->first();

        return view('superadmin.users.show', compact('user', 'activityCount', 'lastActivity'));
    }

    public function edit(User $user)
    {
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        return view('superadmin.users.edit', compact('user', 'warehouses'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'         => 'required|string|max:150',
            'email'        => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'phone'        => 'nullable|string|max:20',
            'role'         => 'required|in:super_admin,admin,user',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'is_active'    => 'nullable|boolean',
        ]);

        $user->update([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'role'         => $request->role,
            'warehouse_id' => $request->warehouse_id,
            'is_active'    => $request->boolean('is_active', $user->is_active),
        ]);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', $user->is_active ? 'User diaktifkan.' : 'User dinonaktifkan.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => ['required', Password::min(8)->letters()->numbers()],
        ]);

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Password user berhasil direset.');
    }
}


