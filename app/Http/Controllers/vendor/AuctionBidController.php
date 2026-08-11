<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Http\Responses\FlashMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Concerns\HasVendorContext;



class AuctionBidController extends Controller
{
    use HasVendorContext;


    /**
     * Display a listing of available auctions for bidding
     */
    public function index()
    {
        $auctions = Auction::with(['user', 'bids'])
            ->where('status', 'active')
            ->where('deadline', '>', now())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('auctions.index', compact('auctions'));
    }

    /**
     * Show the form for creating a new bid
     */
    public function create(Auction $auction)
    {
        // Check if auction is still active
        if (!$auction->isActive()) {
            return FlashMessage::error(redirect()->route('vendor.auctions.index'), 'Lelang sudah tidak aktif atau sudah berakhir');
        }

        // Check if vendor already bid on this auction
        $vendorUser = $this->requireVendor();
        $vendorId = $vendorUser ? $vendorUser->id : null;

        $existingBid = null;
        if ($vendorId) {
            $existingBid = AuctionBid::where('auction_id', $auction->id)
                ->where('vendor_id', $vendorId)
                ->first();
        }

        if ($existingBid) {
            return FlashMessage::info(redirect()->route('vendor.auctions.show', $auction), 'Anda sudah memberikan penawaran untuk lelang ini');
        }

        return view('auctions.bid', compact('auction'));
    }

    /**
     * Store a newly created bid
     */
    public function store(Request $request, Auction $auction)
    {
        // Check if auction is still active
        if (!$auction->isActive()) {
            return FlashMessage::error(redirect()->route('vendor.auctions.index'), 'Lelang sudah tidak aktif atau sudah berakhir');
        }

        $request->validate([
            'bid_amount' => 'required|numeric|min:1',
            'message' => 'nullable|string|max:1000'
        ]);

        // Get vendor ID
        $user = Auth::user();
        $vendorUser = $user->vendorUser->first();

        if (!$vendorUser) {
            return FlashMessage::error(redirect()->route('vendor.auctions.index'), 'Anda tidak memiliki akses vendor. Silakan hubungi administrator.');
        }

        $vendorId = $vendorUser->id; // Use vendor's ID directly

        // Check if vendor already bid on this auction
        $existingBid = AuctionBid::where('auction_id', $auction->id)
            ->where('vendor_id', $vendorId)
            ->first();

        if ($existingBid) {
            return FlashMessage::error(redirect()->route('vendor.auctions.show', $auction), 'Anda sudah memberikan penawaran untuk lelang ini');
        }

        // Create new bid
        $bid = AuctionBid::create([
            'auction_id' => $auction->id,
            'vendor_id' => $vendorId,
            'bid_amount' => $request->bid_amount,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        // Notify auction owner about new bid
        try {
            $auction->user->notify(
                new \App\Notifications\NewBidOnAuction($auction, $bid, $vendorUser)
            );
        } catch (\Exception $e) {
            \Log::warning('Gagal mengirim notifikasi bid baru: ' . $e->getMessage());
        }

        return FlashMessage::success(redirect()->route('vendor.auctions.show', $auction), 'Penawaran berhasil dikirim!');
    }

    /**
     * Display the specified auction
     */
    public function show(Auction $auction)
    {
        $auction->load(['user', 'bids.vendor', 'winnerVendor']);

        // Get vendor's bid for this auction
        $vendorUser = $this->requireVendor();
        $vendorId = $vendorUser ? $vendorUser->id : null;

        $myBid = null;
        if ($vendorId) {
            $myBid = AuctionBid::where('auction_id', $auction->id)
                ->where('vendor_id', $vendorId)
                ->first();
        }

        return view('auctions.show', compact('auction', 'myBid'));
    }

    /**
     * Show the form for editing the specified bid
     */
    public function edit(AuctionBid $bid)
    {
        // Check if bid belongs to current vendor
        $vendorUser = $this->requireVendor();
        if (!$vendorUser) {
            abort(403, 'Anda tidak memiliki akses vendor.');
        }

        $vendorId = $vendorUser->id;
        if ($bid->vendor_id !== $vendorId) {
            abort(403);
        }

        // Check if auction is still active
        if (!$bid->auction->isActive()) {
            return FlashMessage::error(redirect()->route('vendor.auctions.show', $bid->auction), 'Lelang sudah tidak aktif, tidak bisa mengedit penawaran');
        }

        return view('auctions.edit-bid', compact('bid'));
    }

    /**
     * Update the specified bid
     */
    public function update(Request $request, AuctionBid $bid)
    {
        // Check if bid belongs to current vendor
        $vendorUser = $this->requireVendor();
        if (!$vendorUser) {
            abort(403, 'Anda tidak memiliki akses vendor.');
        }

        $vendorId = $vendorUser->id;
        if ($bid->vendor_id !== $vendorId) {
            abort(403);
        }

        // Check if auction is still active
        if (!$bid->auction->isActive()) {
            return FlashMessage::error(redirect()->route('vendor.auctions.show', $bid->auction), 'Lelang sudah tidak aktif, tidak bisa mengedit penawaran');
        }

        $request->validate([
            'bid_amount' => 'required|numeric|min:1',
            'message' => 'nullable|string|max:1000'
        ]);

        $bid->update([
            'bid_amount' => $request->bid_amount,
            'message' => $request->message
        ]);

        return FlashMessage::success(redirect()->route('vendor.auctions.show', $bid->auction), 'Penawaran berhasil diperbarui!');
    }

    /**
     * Remove the specified bid
     */
    public function destroy(AuctionBid $bid)
    {
        // Check if bid belongs to current vendor
        $vendorUser = $this->requireVendor();
        if (!$vendorUser) {
            abort(403, 'Anda tidak memiliki akses vendor.');
        }

        $vendorId = $vendorUser->id;
        if ($bid->vendor_id !== $vendorId) {
            abort(403);
        }

        // Check if auction is still active
        if (!$bid->auction->isActive()) {
            return FlashMessage::error(redirect()->route('vendor.auctions.show', $bid->auction), 'Lelang sudah tidak aktif, tidak bisa menghapus penawaran');
        }

        $auction = $bid->auction;
        $bid->delete();

        return FlashMessage::success(redirect()->route('vendor.auctions.show', $auction), 'Penawaran berhasil dihapus!');
    }

    /**
     * Show vendor's bids
     */
    public function myBids()
    {
        $vendorUser = $this->requireVendor();
        if (!$vendorUser) {
            abort(403, 'Anda tidak memiliki akses vendor.');
        }

        $vendorId = $vendorUser->id;

        $bids = AuctionBid::with(['auction.user', 'auction.bids'])
            ->where('vendor_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('auctions.my-bids', compact('bids'));
    }
}
