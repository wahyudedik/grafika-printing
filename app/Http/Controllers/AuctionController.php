<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\LelangUserProfile;
use App\Services\AuctionToPosService;
use App\Http\Requests\StoreAuctionRequest;
use App\Http\Requests\UpdateAuctionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Responses\FlashMessage;
use App\Services\AuditLogService;


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
    public function store(StoreAuctionRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        // Generate auction code
        $data['kode'] = 'AUCTION-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // Set default values for POS integration
        $data['metode_pembayaran'] = 'auction_win';
        $data['progress_percentage'] = 0;
        $data['pos_integrated'] = false;

        // Calculate admin fees
        $adminFeeService = app(\App\Services\AdminFeeService::class);
        $fees = $adminFeeService->calculateTotalFees($request->budget, 'bank_transfer');

        $data['admin_fee_amount'] = $fees['admin_fee'];
        $data['payment_gateway_fee'] = $fees['payment_gateway_fee'];
        $data['total_amount_with_fees'] = $fees['total_amount'];
        $data['vendor_receives'] = $fees['vendor_receives'];
        $data['admin_receives'] = $fees['admin_receives'];
        $data['fee_breakdown'] = json_encode($fees['admin_fee_breakdown']);
        $data['fees_calculated'] = true;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('auction_files', $fileName, 'public');
            $data['file_path'] = $fileName;
        }

        $auction = Auction::create($data);

        // Auto-create LelangUserProfile if user doesn't have one yet
        LelangUserProfile::getOrCreate(Auth::id());

        AuditLogService::logCreated($auction, 'Auction baru dibuat: ' . $auction->title);

        return FlashMessage::success(redirect()->route('user.auctions.index'), 'Permintaan cetak berhasil dibuat! Lelang Anda sedang menunggu verifikasi admin.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Auction $auction)
    {
        // Authorization: only owner, admin/dev, or winning vendor can view
        $this->authorize('view', $auction);

        $auction->load(['user', 'bids.vendor', 'winnerVendor']);
        $auction->loadCount('bids');

        return view('user.auctions.show', compact('auction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Auction $auction)
    {
        $this->authorize('update', $auction);

        // Prevent editing after payment is completed
        if ($auction->status === 'paid' || $auction->status === 'completed') {
            return FlashMessage::error(redirect()->route('user.auctions.show', $auction), 'Lelang ini sudah dibayar dan tidak dapat diedit lagi.');
        }

        return view('user.auctions.edit', compact('auction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuctionRequest $request, Auction $auction)
    {
        $this->authorize('update', $auction);

        // Prevent updating after payment is completed
        if ($auction->status === 'paid' || $auction->status === 'completed') {
            return FlashMessage::error(redirect()->route('user.auctions.show', $auction), 'Lelang ini sudah dibayar dan tidak dapat diedit lagi.');
        }

        $data = $request->validated();

        // Capture old values for audit log
        $oldValues = [
            'title' => $auction->title,
            'description' => $auction->description,
            'budget' => $auction->budget,
            'deadline' => $auction->deadline,
            'status' => $auction->status,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if (!empty($auction->file_path) && Storage::disk('public')->exists('auction_files/' . $auction->file_path)) {
                Storage::disk('public')->delete('auction_files/' . $auction->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('auction_files', $fileName, 'public');
            $data['file_path'] = $fileName;
        }

        $auction->update($data);

        AuditLogService::logUpdated($auction, $oldValues, 'Auction diupdate: ' . $auction->title);

        return FlashMessage::success(redirect()->route('user.auctions.show', $auction), 'Permintaan cetak berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auction $auction)
    {
        $this->authorize('delete', $auction);

        // Delete file if exists
        if ($auction->file_path && Storage::disk('public')->exists('auction_files/' . $auction->file_path)) {
            Storage::disk('public')->delete('auction_files/' . $auction->file_path);
        }

        $auction->delete();

        AuditLogService::logDeleted($auction, 'Auction dihapus: ' . $auction->title);

        return FlashMessage::success(redirect()->route('user.auctions.index'), 'Permintaan cetak berhasil dihapus!');
    }

    /**
     * Show user's auctions
     */
    public function myAuctions()
    {
        $auctions = Auction::where('user_id', Auth::id())
            ->with(['bids.vendor', 'winnerVendor'])
            ->withCount('bids')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.auctions.my-auctions', compact('auctions'));
    }

    /**
     * Close auction and select winner
     */
    public function closeAuction(Request $request, Auction $auction)
    {
        $this->authorize('update', $auction);

        $request->validate([
            'winner_bid_id' => 'required|exists:auction_bids,id'
        ]);

        $winnerBid = AuctionBid::findOrFail($request->winner_bid_id);

        // Validasi bahwa bid benar-benar milik lelang ini
        if ($winnerBid->auction_id !== $auction->id) {
            abort(403, 'Penawaran ini bukan milik lelang ini.');
        }

        // Update auction status to waiting for payment
        $auction->update([
            'status' => 'waiting_payment',
            'winner_vendor_id' => $winnerBid->vendor_id,
            'winning_bid' => $winnerBid->bid_amount
        ]);

        $winnerBid->update(['status' => 'accepted']);

        // Create payment link directly
        try {
            $xenditService = app(\App\Services\XenditService::class);

            $externalId = 'auction_' . $auction->id . '_' . time();
            $amount = $auction->total_amount_with_fees > 0 ? $auction->total_amount_with_fees : $winnerBid->bid_amount;

            $paymentData = [
                'external_id' => $externalId,
                'amount' => $amount,
                'description' => 'Pembayaran Lelang: ' . $auction->title,
                'customer' => [
                    'given_names' => Auth::user()->name ?? 'Customer',
                    'email' => Auth::user()->email ?? 'customer@example.com'
                ],
                'items' => [
                    [
                        'name' => $auction->title,
                        'quantity' => $auction->quantity,
                        'price' => $amount,
                        'category' => 'Printing Service'
                    ]
                ],
                'success_redirect_url' => route('user.auctions.show', $auction) . '?payment=success',
                'failure_redirect_url' => route('user.auctions.show', $auction) . '?payment=failed',
                'invoice_duration' => 86400, // 24 hours
                'payment_methods' => [
                    'BCA',
                    'BNI',
                    'BRI',
                    'BSI',
                    'MANDIRI',
                    'PERMATA',
                    'ALFAMART',
                    'INDOMARET',
                    'OVO',
                    'DANA',
                    'LINKAJA',
                    'SHOPEEPAY',
                    'QRIS'
                ]
            ];

            $response = $xenditService->createPaymentLink($paymentData);

            if ($response && isset($response['invoice_url'])) {
                // Save payment record
                $payment = \App\Models\XenditPayment::create([
                    'external_id' => $externalId,
                    'xendit_id' => $response['id'] ?? null,
                    'type' => 'payment_link',
                    'amount' => $amount,
                    'description' => $paymentData['description'],
                    'status' => 'pending',
                    'customer' => $paymentData['customer'],
                    'items' => $paymentData['items'],
                    'checkout_url' => $response['invoice_url'],
                    'success_redirect_url' => $paymentData['success_redirect_url'],
                    'failure_redirect_url' => $paymentData['failure_redirect_url'],
                    'expires_at' => now()->addHours(24),
                    'auction_id' => $auction->id
                ]);

                Log::info('Payment link created successfully', [
                    'auction_id' => $auction->id,
                    'payment_id' => $payment->id,
                    'checkout_url' => $response['invoice_url']
                ]);

                // Redirect directly to Xendit checkout
                return redirect($response['invoice_url']);
            } else {
                throw new \Exception('Failed to create payment link');
            }
        } catch (\Exception $e) {
            Log::error('Failed to create payment link', [
                'auction_id' => $auction->id,
                'error' => $e->getMessage()
            ]);

            return FlashMessage::error(redirect()->route('user.auctions.show', $auction), 'Gagal membuat link pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Create payment link for auction that is waiting for payment
     */
    public function createPayment(Auction $auction)
    {
        // Authorization: only auction owner can create payment
        if ($auction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk pembayaran ini.');
        }

        // Check if auction is waiting for payment
        if ($auction->status !== 'waiting_payment') {
            return FlashMessage::error(redirect()->route('user.auctions.show', $auction), 'Lelang ini tidak memerlukan pembayaran.');
        }

        // Redirect to payment confirmation page
        return redirect()->route('user.payments.confirmation', $auction);
    }

    /**
     * Close auction (alias for closeAuction)
     */
    public function close(Request $request, Auction $auction)
    {
        return $this->closeAuction($request, $auction);
    }
}
