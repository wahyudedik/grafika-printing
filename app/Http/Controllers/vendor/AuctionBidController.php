<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionBidController extends Controller
{
    public function __construct()
    {
        $this->middleware('vendor');
    }

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

        return view('vendor.auctions.index', compact('auctions'));
    }

    /**
     * Show the form for creating a new bid
     */
    public function create(Auction $auction)
    {
        // Check if auction is still active
        if (!$auction->isActive()) {
            return redirect()->route('vendor.auctions.index')
                ->with('error', 'Lelang sudah tidak aktif atau sudah berakhir');
        }

        // Check if vendor already bid on this auction
        $existingBid = AuctionBid::where('auction_id', $auction->id)
            ->where('vendor_id', Auth::user()->vendorUser->first()->vendor_id)
            ->first();

        if ($existingBid) {
            return redirect()->route('vendor.auctions.show', $auction)
                ->with('info', 'Anda sudah memberikan penawaran untuk lelang ini');
        }

        return view('vendor.auctions.bid', compact('auction'));
    }

    /**
     * Store a newly created bid
     */
    public function store(Request $request, Auction $auction)
    {
        // Check if auction is still active
        if (!$auction->isActive()) {
            return redirect()->route('vendor.auctions.index')
                ->with('error', 'Lelang sudah tidak aktif atau sudah berakhir');
        }

        $request->validate([
            'bid_amount' => 'required|numeric|min:0',
            'message' => 'nullable|string|max:1000'
        ]);

        // Get vendor ID
        $vendorId = Auth::user()->vendorUser->first()->vendor_id;

        // Check if vendor already bid on this auction
        $existingBid = AuctionBid::where('auction_id', $auction->id)
            ->where('vendor_id', $vendorId)
            ->first();

        if ($existingBid) {
            return redirect()->route('vendor.auctions.show', $auction)
                ->with('error', 'Anda sudah memberikan penawaran untuk lelang ini');
        }

        // Create new bid
        AuctionBid::create([
            'auction_id' => $auction->id,
            'vendor_id' => $vendorId,
            'bid_amount' => $request->bid_amount,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        return redirect()->route('vendor.auctions.show', $auction)
            ->with('success', 'Penawaran berhasil dikirim!');
    }

    /**
     * Display the specified auction
     */
    public function show(Auction $auction)
    {
        $auction->load(['user', 'bids.vendor', 'winnerVendor']);

        // Get vendor's bid for this auction
        $vendorId = Auth::user()->vendorUser->first()->vendor_id;
        $myBid = AuctionBid::where('auction_id', $auction->id)
            ->where('vendor_id', $vendorId)
            ->first();

        return view('vendor.auctions.show', compact('auction', 'myBid'));
    }

    /**
     * Show the form for editing the specified bid
     */
    public function edit(AuctionBid $bid)
    {
        // Check if bid belongs to current vendor
        $vendorId = Auth::user()->vendorUser->first()->vendor_id;
        if ($bid->vendor_id !== $vendorId) {
            abort(403);
        }

        // Check if auction is still active
        if (!$bid->auction->isActive()) {
            return redirect()->route('vendor.auctions.show', $bid->auction)
                ->with('error', 'Lelang sudah tidak aktif, tidak bisa mengedit penawaran');
        }

        return view('vendor.auctions.edit-bid', compact('bid'));
    }

    /**
     * Update the specified bid
     */
    public function update(Request $request, AuctionBid $bid)
    {
        // Check if bid belongs to current vendor
        $vendorId = Auth::user()->vendorUser->first()->vendor_id;
        if ($bid->vendor_id !== $vendorId) {
            abort(403);
        }

        // Check if auction is still active
        if (!$bid->auction->isActive()) {
            return redirect()->route('vendor.auctions.show', $bid->auction)
                ->with('error', 'Lelang sudah tidak aktif, tidak bisa mengedit penawaran');
        }

        $request->validate([
            'bid_amount' => 'required|numeric|min:0',
            'message' => 'nullable|string|max:1000'
        ]);

        $bid->update([
            'bid_amount' => $request->bid_amount,
            'message' => $request->message
        ]);

        return redirect()->route('vendor.auctions.show', $bid->auction)
            ->with('success', 'Penawaran berhasil diperbarui!');
    }

    /**
     * Remove the specified bid
     */
    public function destroy(AuctionBid $bid)
    {
        // Check if bid belongs to current vendor
        $vendorId = Auth::user()->vendorUser->first()->vendor_id;
        if ($bid->vendor_id !== $vendorId) {
            abort(403);
        }

        // Check if auction is still active
        if (!$bid->auction->isActive()) {
            return redirect()->route('vendor.auctions.show', $bid->auction)
                ->with('error', 'Lelang sudah tidak aktif, tidak bisa menghapus penawaran');
        }

        $auction = $bid->auction;
        $bid->delete();

        return redirect()->route('vendor.auctions.show', $auction)
            ->with('success', 'Penawaran berhasil dihapus!');
    }

    /**
     * Show vendor's bids
     */
    public function myBids()
    {
        $vendorId = Auth::user()->vendorUser->first()->vendor_id;

        $bids = AuctionBid::with(['auction.user', 'auction.bids'])
            ->where('vendor_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vendor.auctions.my-bids', compact('bids'));
    }
}
