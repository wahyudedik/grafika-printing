<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\FlashMessage;


class AuctionManagementController extends Controller
{
    /**
     * Display a listing of all auctions
     */
    public function index(Request $request)
    {
        $query = Auction::with(['user', 'bids.vendor', 'winnerVendor']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $auctions = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('dev.auctions.index', compact('auctions'));
    }

    /**
     * Display the specified auction with all bids
     */
    public function show(Auction $auction)
    {
        $auction->load(['user', 'bids.vendor', 'winnerVendor']);

        // Get bids ordered by amount (lowest first)
        $bids = $auction->bids()->with('vendor')->orderBy('bid_amount', 'asc')->get();

        return view('dev.auctions.show', compact('auction', 'bids'));
    }

    /**
     * Approve an auction (make it active)
     */
    public function approve(Auction $auction)
    {
        if ($auction->status === 'pending') {
            $auction->update([
                'status' => 'active',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Send notification to user
            $auction->user->notify(new \App\Notifications\AuctionApproved($auction));

            return FlashMessage::success(redirect()->route('admin.auctions.index'), 'Lelang berhasil disetujui dan diaktifkan!');
        }

        return FlashMessage::error(redirect()->route('admin.auctions.index'), 'Lelang tidak dapat disetujui karena statusnya bukan pending.');
    }

    /**
     * Reject an auction
     */
    public function reject(Request $request, Auction $auction)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($auction->status === 'pending') {
            $auction->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'rejected_by' => Auth::id(),
                'rejected_at' => now()
            ]);

            // Send notification to user
            $auction->user->notify(new \App\Notifications\AuctionRejected($auction, $request->rejection_reason));

            return FlashMessage::success(redirect()->route('admin.auctions.index'), 'Lelang berhasil ditolak!');
        }

        return FlashMessage::error(redirect()->route('admin.auctions.index'), 'Lelang tidak dapat ditolak karena statusnya bukan pending.');
    }

    /**
     * Close an auction manually
     */
    public function close(Auction $auction)
    {
        if ($auction->status === 'active') {
            $auction->update(['status' => 'closed']);

            return FlashMessage::success(redirect()->route('admin.auctions.index'), 'Lelang berhasil ditutup!');
        }

        return FlashMessage::error(redirect()->route('admin.auctions.index'), 'Lelang tidak dapat ditutup karena statusnya bukan active.');
    }

    /**
     * Remove the specified auction
     */
    public function destroy(Auction $auction)
    {
        // Delete all related bids first
        $auction->bids()->delete();

        // Delete auction files if exist
        if ($auction->file_path && file_exists(storage_path('app/public/' . $auction->file_path))) {
            unlink(storage_path('app/public/' . $auction->file_path));
        }

        // Delete the auction
        $auction->delete();

        return FlashMessage::success(redirect()->route('admin.auctions.index'), 'Lelang berhasil dihapus!');
    }

    /**
     * Show auction statistics
     */
    public function statistics()
    {
        $stats = [
            'total_auctions' => Auction::count(),
            'active_auctions' => Auction::where('status', 'active')->count(),
            'pending_auctions' => Auction::where('status', 'pending')->count(),
            'closed_auctions' => Auction::where('status', 'closed')->count(),
            'rejected_auctions' => Auction::where('status', 'rejected')->count(),
            'total_bids' => AuctionBid::count(),
            'total_users' => Auction::distinct('user_id')->count(),
        ];

        // Recent auctions
        $recentAuctions = Auction::with(['user', 'bids'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dev.auctions.statistics', compact('stats', 'recentAuctions'));
    }

    /**
     * Show all bids for a specific auction
     */
    public function bids(Auction $auction)
    {
        $bids = $auction->bids()->with('vendor')->orderBy('bid_amount', 'asc')->get();

        return view('dev.auctions.bids', compact('auction', 'bids'));
    }

    /**
     * Show the form for editing the specified auction
     */
    public function edit(Auction $auction)
    {
        $auction->load('user');

        return view('dev.auctions.edit', compact('auction'));
    }

    /**
     * Update the specified auction
     */
    public function update(Request $request, Auction $auction)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'specifications' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'budget' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'deadline' => 'required|date|after:today',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        $data = $request->only([
            'title',
            'description',
            'specifications',
            'quantity',
            'budget',
            'category',
            'deadline'
        ]);

        // Handle file upload if new file is provided
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($auction->file_path && file_exists(storage_path('app/public/' . $auction->file_path))) {
                unlink(storage_path('app/public/' . $auction->file_path));
            }

            // Store new file
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('auction_files', $fileName, 'public');
            $data['file_path'] = $filePath;
        }

        $auction->update($data);

        return FlashMessage::success(redirect()->route('admin.auctions.show', $auction), 'Data lelang berhasil diperbarui!');
    }
}
