<?php

namespace App\Http\Controllers;

use App\Models\VendorRating;
use App\Models\Vendor;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorRatingController extends Controller
{
    /**
     * Display vendor profile with ratings
     */
    public function show(Vendor $vendor)
    {
        $vendor->load(['ratings.user', 'verifiedRatings.user']);

        $averageRating = $vendor->average_rating;
        $ratingCount = $vendor->rating_count;
        $ratingDistribution = VendorRating::getRatingDistribution($vendor->id);

        return view('vendor.profile', compact('vendor', 'averageRating', 'ratingCount', 'ratingDistribution'));
    }

    /**
     * Show rating form for completed auction
     */
    public function create(Auction $auction)
    {
        // Only allow rating if auction is completed and user is the auction creator
        if ($auction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk memberikan rating');
        }

        if ($auction->status !== 'completed') {
            abort(403, 'Lelang belum selesai');
        }

        // Check if already rated
        $existingRating = VendorRating::where('user_id', Auth::id())
            ->where('auction_id', $auction->id)
            ->first();

        if ($existingRating) {
            return redirect()->route('user.auctions.show', $auction)
                ->with('info', 'Anda sudah memberikan rating untuk lelang ini');
        }

        $vendor = $auction->winnerVendor;

        return view('user.auctions.rate', compact('auction', 'vendor'));
    }

    /**
     * Store rating
     */
    public function store(Request $request, Auction $auction)
    {
        // Only allow rating if auction is completed and user is the auction creator
        if ($auction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk memberikan rating');
        }

        if ($auction->status !== 'completed') {
            abort(403, 'Lelang belum selesai');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'rating_details' => 'nullable|array',
            'rating_details.quality' => 'nullable|integer|min:1|max:5',
            'rating_details.speed' => 'nullable|integer|min:1|max:5',
            'rating_details.service' => 'nullable|integer|min:1|max:5',
            'rating_details.communication' => 'nullable|integer|min:1|max:5'
        ]);

        // Check if already rated
        $existingRating = VendorRating::where('user_id', Auth::id())
            ->where('auction_id', $auction->id)
            ->first();

        if ($existingRating) {
            return redirect()->route('user.auctions.show', $auction)
                ->with('error', 'Anda sudah memberikan rating untuk lelang ini');
        }

        DB::beginTransaction();
        try {
            VendorRating::create([
                'vendor_id' => $auction->winner_vendor_id,
                'user_id' => Auth::id(),
                'auction_id' => $auction->id,
                'transaksi_id' => $auction->transaksi_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'rating_details' => $request->rating_details,
                'is_verified' => false // Will be verified by admin
            ]);

            DB::commit();

            return redirect()->route('user.auctions.show', $auction)
                ->with('success', 'Rating berhasil dikirim! Rating akan ditampilkan setelah diverifikasi admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan rating: ' . $e->getMessage());
        }
    }

    /**
     * Get vendor ratings for auction bidding
     */
    public function getVendorRatings(Vendor $vendor)
    {
        $ratings = $vendor->verifiedRatings()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $averageRating = $vendor->average_rating;
        $ratingCount = $vendor->rating_count;

        return response()->json([
            'ratings' => $ratings,
            'average_rating' => round($averageRating, 1),
            'rating_count' => $ratingCount
        ]);
    }
}
