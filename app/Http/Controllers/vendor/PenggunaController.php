<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Facades\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PenggunaController extends Controller
{
    /**
     * Display a listing of users associated with the current vendor.
     */
    public function index(Request $request)
    {
        $vendorId = Tenant::getVendorId();
        $vendor = Vendor::findOrFail($vendorId);

        $query = $vendor->users();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('pengguna.index', compact('users', 'vendor'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('pengguna.create');
    }

    /**
     * Store a newly created user and attach to the current vendor.
     */
    public function store(Request $request)
    {
        $vendorId = Tenant::getVendorId();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'usertype' => 'vendor',
        ]);

        // Attach the user to the current vendor
        $user->vendorUser()->attach($vendorId);

        return redirect()->route('vendor.users.index')
            ->with('success', "Pengguna \"{$user->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $vendorId = Tenant::getVendorId();
        $vendor = Vendor::findOrFail($vendorId);

        // Ensure the user belongs to this vendor
        $user = $vendor->users()->findOrFail($id);

        return view('pengguna.show', compact('user', 'vendor'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $vendorId = Tenant::getVendorId();
        $vendor = Vendor::findOrFail($vendorId);

        // Ensure the user belongs to this vendor
        $vendor->users()->findOrFail($user->id);

        return view('pengguna.edit', compact('user', 'vendor'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $vendorId = Tenant::getVendorId();
        $vendor = Vendor::findOrFail($vendorId);

        // Ensure the user belongs to this vendor
        $vendor->users()->findOrFail($user->id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('vendor.users.show', $user->id)
            ->with('success', "Pengguna \"{$user->name}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified user from the current vendor.
     * If user only belongs to this vendor, delete the user entirely.
     */
    public function destroy(User $user)
    {
        $vendorId = Tenant::getVendorId();
        $vendor = Vendor::findOrFail($vendorId);

        // Ensure the user belongs to this vendor
        $vendor->users()->findOrFail($user->id);

        $userName = $user->name;

        // Check if user belongs to other vendors
        if ($user->vendorUser()->count() > 1) {
            // Just detach from this vendor
            $user->vendorUser()->detach($vendorId);
            return redirect()->route('vendor.users.index')
                ->with('success', "Pengguna \"{$userName}\" berhasil dilepas dari vendor ini.");
        } else {
            // User only belongs to this vendor, delete entirely
            $user->delete();
            return redirect()->route('vendor.users.index')
                ->with('success', "Pengguna \"{$userName}\" berhasil dihapus.");
        }
    }
}
