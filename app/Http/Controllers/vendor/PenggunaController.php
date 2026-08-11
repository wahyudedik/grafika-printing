<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Http\Concerns\HasVendorContext;
use App\Http\Responses\FlashMessage;
use App\Models\User;
use App\Models\Vendor;
use App\Facades\Tenant;
use App\Http\Requests\StorePenggunaRequest;
use App\Http\Requests\UpdatePenggunaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Services\AuditLogService;



class PenggunaController extends Controller
{
    use HasVendorContext;


    /**
     * Display a listing of users associated with the current vendor.
     */
    public function index(Request $request)
    {
        $vendor = $this->requireVendor();
        $vendorId = $vendor->id;

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
        $this->requireVendor();

        return view('pengguna.create');
    }

    /**
     * Store a newly created user and attach to the current vendor.
     */
    public function store(StorePenggunaRequest $request)
    {
        $vendorId = $this->requireVendor()->id;

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'usertype' => 'vendor',
        ]);

        // Attach the user to the current vendor
        $user->vendorUser()->attach($vendorId);

        AuditLogService::logCreated($user, 'Pengguna baru ditambahkan: ' . $user->name);

        return FlashMessage::success(redirect()->route('vendor.users.index'), "Pengguna \"{$user->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $vendor = $this->requireVendor();

        // Ensure the user belongs to this vendor
        $user = $vendor->users()->findOrFail($id);

        return view('pengguna.show', compact('user', 'vendor'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $vendor = $this->requireVendor();

        // Ensure the user belongs to this vendor
        $vendor->users()->findOrFail($user->id);

        return view('pengguna.edit', compact('user', 'vendor'));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdatePenggunaRequest $request, User $user)
    {
        $vendor = $this->requireVendor();

        // Ensure the user belongs to this vendor
        $vendor->users()->findOrFail($user->id);

        $validated = $request->validated();

        // Capture old values for audit log
        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AuditLogService::logUpdated($user, $oldValues, 'Pengguna diperbarui: ' . $user->name);

        return FlashMessage::success(redirect()->route('vendor.users.show', $user->id), "Pengguna \"{$user->name}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified user from the current vendor.
     * If user only belongs to this vendor, delete the user entirely.
     */
    public function destroy(User $user)
    {
        $vendor = $this->requireVendor();
        $vendorId = $vendor->id;

        // Ensure the user belongs to this vendor
        $vendor->users()->findOrFail($user->id);

        $userName = $user->name;

        // Check if user belongs to other vendors
        if ($user->vendorUser()->count() > 1) {
            // Just detach from this vendor
            $user->vendorUser()->detach($vendorId);
            AuditLogService::log('detach_user', 'Pengguna dilepas dari vendor: ' . $userName);
            return FlashMessage::success(redirect()->route('vendor.users.index'), "Pengguna \"{$userName}\" berhasil dilepas dari vendor ini.");
        } else {
            // User only belongs to this vendor, delete entirely
            $user->delete();
            AuditLogService::logDeleted($user, 'Pengguna dihapus: ' . $userName);
            return FlashMessage::success(redirect()->route('vendor.users.index'), "Pengguna \"{$userName}\" berhasil dihapus.");
        }
    }
}
