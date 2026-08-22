<?php

namespace App\Http\Controllers;

use App\Models\Vendor\TransactionReview;
use App\Models\Vendor\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransactionReviewController extends Controller
{
    /**
     * Form review untuk transaksi selesai.
     */
    public function create(Transaksi $transaksi)
    {
        // Authorization: pastikan transaksi milik user yang login
        if ($transaksi->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        // Pastikan transaksi status completed
        if ($transaksi->status !== 'completed') {
            return redirect()
                ->route('user.transactions.show', $transaksi->id)
                ->with('error', 'Anda hanya bisa memberikan ulasan untuk transaksi yang sudah selesai.');
        }

        // Cek apakah sudah ada review
        $hasReview = TransactionReview::withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->where('transaksi_id', $transaksi->id)
            ->exists();

        if ($hasReview) {
            return redirect()
                ->route('user.transactions.show', $transaksi->id)
                ->with('info', 'Anda sudah memberikan ulasan untuk transaksi ini.');
        }

        // Load relations
        $transaksi->load(['vendor', 'transaksiItem.produk']);

        return view('user.transactions.review', compact('transaksi'));
    }

    /**
     * Simpan review.
     */
    public function store(Request $request, Transaksi $transaksi)
    {
        // Authorization: pastikan transaksi milik user yang login
        if ($transaksi->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        // Cek status transaksi — hanya transaksi completed yang bisa di-review
        if ($transaksi->status !== 'completed') {
            return redirect()
                ->route('user.transactions.show', $transaksi->id)
                ->with('error', 'Anda hanya bisa memberikan ulasan untuk transaksi yang sudah selesai.');
        }

        // Validasi input
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
            'quality_rating' => 'nullable|integer|between:1,5',
            'speed_rating' => 'nullable|integer|between:1,5',
            'service_rating' => 'nullable|integer|between:1,5',
        ]);

        // Cek duplikat
        $exists = TransactionReview::withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->where('transaksi_id', $transaksi->id)
            ->exists();

        if ($exists) {
            abort(409, 'Anda sudah memberikan ulasan untuk transaksi ini.');
        }

        // Create review
        TransactionReview::create([
            'vendor_id' => $transaksi->vendor_id,
            'user_id' => auth()->id(),
            'transaksi_id' => $transaksi->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'quality_rating' => $validated['quality_rating'] ?? null,
            'speed_rating' => $validated['speed_rating'] ?? null,
            'service_rating' => $validated['service_rating'] ?? null,
        ]);

        return redirect()
            ->route('user.transactions.show', $transaksi->id)
            ->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }

    /**
     * Hapus review.
     */
    public function destroy(TransactionReview $review)
    {
        // Authorization: pastikan review milik user yang login
        if ($review->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus ulasan ini.');
        }

        $transaksiId = $review->transaksi_id;

        $review->delete();

        return redirect()
            ->route('user.transactions.show', $transaksiId)
            ->with('success', 'Ulasan berhasil dihapus.');
    }
}
