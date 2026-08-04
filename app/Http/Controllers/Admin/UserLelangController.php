<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LelangUserProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserLelangController extends Controller
{
    /**
     * Display a listing of all lelang user profiles.
     */
    public function index(Request $request)
    {
        $query = LelangUserProfile::with(['user']);

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->search($search);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by verification
        if ($request->has('verified') && $request->verified !== '') {
            $query->where('is_verified', $request->verified === 'true');
        }

        $profiles = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => LelangUserProfile::count(),
            'active' => LelangUserProfile::where('status', 'active')->count(),
            'suspended' => LelangUserProfile::where('status', 'suspended')->count(),
            'verified' => LelangUserProfile::where('is_verified', true)->count(),
        ];

        return view('dev.user-lelang.index', compact('profiles', 'stats'));
    }

    /**
     * Display the specified lelang user profile.
     */
    public function show(LelangUserProfile $profile)
    {
        $profile->load(['user', 'auctions']);

        // Get auction stats
        $auctionStats = [
            'total' => $profile->auctions()->count(),
            'active' => $profile->auctions()->where('status', 'active')->count(),
            'completed' => $profile->auctions()->where('status', 'completed')->count(),
            'won' => $profile->total_won,
        ];

        // Recent auctions
        $recentAuctions = $profile->auctions()
            ->with('bids.vendor')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dev.user-lelang.show', compact('profile', 'auctionStats', 'recentAuctions'));
    }

    /**
     * Show the form for creating a new lelang user profile.
     */
    public function create()
    {
        // Get users with usertype 'user' that don't have a lelang profile yet
        $existingUserIds = LelangUserProfile::pluck('user_id')->toArray();
        $users = User::where('usertype', 'user')
            ->whereNotIn('id', $existingUserIds)
            ->orderBy('name')
            ->get();

        return view('dev.user-lelang.create', compact('users'));
    }

    /**
     * Store a newly created lelang user profile.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'company_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'status' => 'required|in:active,suspended,pending',
            'notes' => 'nullable|string|max:1000',
        ]);

        $profile = LelangUserProfile::create($validated);

        return redirect()->route('admin.user-lelang.show', $profile)
            ->with('success', 'Profil User Lelang berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified lelang user profile.
     */
    public function edit(LelangUserProfile $profile)
    {
        $profile->load('user');

        return view('dev.user-lelang.edit', compact('profile'));
    }

    /**
     * Update the specified lelang user profile.
     */
    public function update(Request $request, LelangUserProfile $profile)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'status' => 'required|in:active,suspended,pending',
            'notes' => 'nullable|string|max:1000',
        ]);

        $profile->update($validated);

        return redirect()->route('admin.user-lelang.show', $profile)
            ->with('success', 'Profil User Lelang berhasil diperbarui.');
    }

    /**
     * Remove the specified lelang user profile.
     */
    public function destroy(LelangUserProfile $profile)
    {
        $profile->delete();

        return redirect()->route('admin.user-lelang.index')
            ->with('success', 'Profil User Lelang berhasil dihapus.');
    }

    /**
     * Verify a lelang user profile.
     */
    public function verify(LelangUserProfile $profile)
    {
        $profile->verify(Auth::id());

        return redirect()->route('admin.user-lelang.show', $profile)
            ->with('success', 'Profil User Lelang berhasil diverifikasi.');
    }

    /**
     * Suspend a lelang user profile.
     */
    public function suspend(Request $request, LelangUserProfile $profile)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $profile->suspend($request->reason);

        return redirect()->route('admin.user-lelang.show', $profile)
            ->with('success', 'Profil User Lelang berhasil ditangguhkan.');
    }

    /**
     * Reactivate a suspended lelang user profile.
     */
    public function reactivate(LelangUserProfile $profile)
    {
        $profile->reactivate();

        return redirect()->route('admin.user-lelang.show', $profile)
            ->with('success', 'Profil User Lelang berhasil diaktifkan kembali.');
    }
}
