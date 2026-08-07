<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LelangUserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('usertype', 'like', "%{$search}%");
            });
        }

        // Filter by usertype
        if ($request->has('usertype') && $request->usertype !== '') {
            $query->where('usertype', $request->usertype);
        }

        // Filter by lelang profile status
        if ($request->has('lelang') && $request->lelang !== '') {
            $lelangUserIds = LelangUserProfile::pluck('user_id')->toArray();
            if ($request->lelang === 'with_profile') {
                $query->whereIn('id', $lelangUserIds);
            } elseif ($request->lelang === 'without_profile') {
                $query->whereNotIn('id', $lelangUserIds);
            } elseif ($request->lelang === 'verified') {
                $verifiedUserIds = LelangUserProfile::where('is_verified', true)->pluck('user_id')->toArray();
                $query->whereIn('id', $verifiedUserIds);
            } elseif ($request->lelang === 'suspended') {
                $suspendedUserIds = LelangUserProfile::where('status', 'suspended')->pluck('user_id')->toArray();
                $query->whereIn('id', $suspendedUserIds);
            }
        }

        $perPage = $request->get('perPage', 15);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Count stats for lelang filter
        $lelangStats = [
            'total_profiles' => LelangUserProfile::count(),
            'verified' => LelangUserProfile::where('is_verified', true)->count(),
            'suspended' => LelangUserProfile::where('status', 'suspended')->count(),
        ];

        return view('dev.users.index', compact('users', 'lelangStats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dev.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'usertype' => 'required|string'
            ]);

            $validatedData['password'] = bcrypt($request->password);
            User::create($validatedData);

            return redirect()->route('users.index')->with('toast_success', 'User created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error creating user');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return view('dev.users.show', compact('user'));
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error showing user');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return view('dev.users.edit', compact('user'));
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error editing user');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8|confirmed',
                'usertype' => 'required|string'
            ]);

            $user = User::findOrFail($id);

            if ($request->filled('password')) {
                $validatedData['password'] = bcrypt($request->password);
            } else {
                unset($validatedData['password']);
            }

            $user->update($validatedData);
            return redirect()->route('users.index')->with('toast_success', 'User updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error updating user');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            User::destroy($id);
            return redirect()->route('users.index')->with('toast_success', 'User deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error deleting user');
        }
    }
}
