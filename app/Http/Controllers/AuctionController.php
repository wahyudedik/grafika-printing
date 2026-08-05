<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Services\AuctionToPosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'alamat_pengiriman' => 'required|string',
            'no_telp' => 'required|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'email_pengiriman' => 'nullable|email',
            'catatan_khusus' => 'nullable|string'
        ], [
            'no_telp.regex' => 'Format nomor telepon tidak valid. Gunakan format: 08123456789, +628123456789, atau (0812) 345-6789',
            'deadline.after' => 'Deadline harus setelah hari ini',
            'budget.min' => 'Budget harus lebih dari 0',
            'quantity.min' => 'Jumlah produksi harus minimal 1'
        ]);

        $data = $request->all();
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

        Auction::create($data);

        return redirect()->route('user.auctions.index')
            ->with('success', 'Permintaan cetak berhasil dibuat! Lelang Anda sedang menunggu verifikasi admin.');
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

        // Prevent editing after payment is completed
        if ($auction->status === 'paid' || $auction->status === 'completed') {
            return redirect()->route('user.auctions.show', $auction)
                ->with('error', 'Lelang ini sudah dibayar dan tidak dapat diedit lagi.');
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

        // Prevent updating after payment is completed
        if ($auction->status === 'paid' || $auction->status === 'completed') {
            return redirect()->route('user.auctions.show', $auction)
                ->with('error', 'Lelang ini sudah dibayar dan tidak dapat diedit lagi.');
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
            if (!empty($auction->file_path) && Storage::disk('public')->exists('auction_files/' . $auction->file_path)) {
                Storage::disk('public')->delete('auction_files/' . $auction->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('auction_files', $fileName, 'public');
            $data['file_path'] = $fileName;
        }

        $auction->update($data);

        return redirect()->route('user.auctions.show', $auction)
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

        return redirect()->route('user.auctions.index')
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

            return redirect()->route('user.auctions.show', $auction)
                ->with('error', 'Gagal membuat link pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Create payment link for auction that is waiting for payment
     */
    public function createPayment(Auction $auction)
    {
        // Check if auction is waiting for payment
        if ($auction->status !== 'waiting_payment') {
            return redirect()->route('user.auctions.show', $auction)
                ->with('error', 'Lelang ini tidak memerlukan pembayaran.');
        }

        // Check if user is the auction owner
        if ($auction->user_id !== Auth::id()) {
            abort(403);
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
