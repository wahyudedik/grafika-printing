<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuctionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auctions = Auction::with(['user', 'bids.vendor'])
            ->where('status', 'active')
            ->where('deadline', '>', now())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('user.auctions.index', compact('auctions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.auctions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'budget' => 'required|numeric|min:0',
            'deadline' => 'required|date|after:today',
            'specifications' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('auction_files', $fileName, 'public');
            $data['file_path'] = $fileName;
        }

        Auction::create($data);

        return redirect()->route('auctions.index')
            ->with('success', 'Permintaan cetak berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Auction $auction)
    {
        $auction->load(['user', 'bids.vendor', 'winnerVendor']);

        return view('user.auctions.show', compact('auction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Auction $auction)
    {
        // Only allow user who created the auction to edit
        if ($auction->user_id !== Auth::id()) {
            abort(403);
        }

        return view('user.auctions.edit', compact('auction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Auction $auction)
    {
        // Only allow user who created the auction to update
        if ($auction->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'budget' => 'required|numeric|min:0',
            'deadline' => 'required|date|after:today',
            'specifications' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);

        $data = $request->all();

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($auction->file_path && Storage::disk('public')->exists('auction_files/' . $auction->file_path)) {
                Storage::disk('public')->delete('auction_files/' . $auction->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('auction_files', $fileName, 'public');
            $data['file_path'] = $fileName;
        }

        $auction->update($data);

        return redirect()->route('auctions.show', $auction)
            ->with('success', 'Permintaan cetak berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auction $auction)
    {
        // Only allow user who created the auction to delete
        if ($auction->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete file if exists
        if ($auction->file_path && Storage::disk('public')->exists('auction_files/' . $auction->file_path)) {
            Storage::disk('public')->delete('auction_files/' . $auction->file_path);
        }

        $auction->delete();

        return redirect()->route('auctions.index')
            ->with('success', 'Permintaan cetak berhasil dihapus!');
    }

    /**
     * Show user's auctions
     */
    public function myAuctions()
    {
        $auctions = Auction::where('user_id', Auth::id())
            ->with(['bids.vendor', 'winnerVendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.auctions.my-auctions', compact('auctions'));
    }

    /**
     * Close auction and select winner
     */
    public function closeAuction(Request $request, Auction $auction)
    {
        // Only allow user who created the auction to close
        if ($auction->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'winner_bid_id' => 'required|exists:auction_bids,id'
        ]);

        $winnerBid = AuctionBid::findOrFail($request->winner_bid_id);

        $auction->update([
            'status' => 'closed',
            'winner_vendor_id' => $winnerBid->vendor_id,
            'winning_bid' => $winnerBid->bid_amount
        ]);

        $winnerBid->update(['status' => 'accepted']);

        return redirect()->route('auctions.show', $auction)
            ->with('success', 'Lelang berhasil ditutup dan pemenang dipilih!');
    }
}
