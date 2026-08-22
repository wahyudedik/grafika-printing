<?php

namespace App\Http\Controllers;

use App\Models\Vendor\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserTransactionController extends Controller
{
    /**
     * List semua transaksi user yang login.
     * Menggunakan withoutGlobalScopes() karena user perlu
     * melihat transaksi dari SEMUA vendor, bukan hanya satu tenant.
     */
    public function index(Request $request): View
    {
        $query = Transaksi::withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->with(['vendor', 'pelanggan', 'transaksiItem.produk']);

        // Filter by status
        $filters = [
            'status' => $request->input('status'),
            'search' => $request->input('search'),
        ];

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by kode transaksi
        if ($request->filled('search')) {
            $query->where('kode', 'like', "%{$request->search}%");
        }

        $transaksi = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('user.transactions.index', compact('transaksi', 'filters'));
    }

    /**
     * Detail transaksi.
     */
    public function show(Transaksi $transaksi): View
    {
        // Authorization: pastikan transaksi milik user yang login
        if ($transaksi->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        $transaksi->load([
            'vendor',
            'pelanggan',
            'transaksiItem.produk',
            'transaksiItemSpecifications.bahan',
            'transactionReview',
        ]);

        return view('user.transactions.show', compact('transaksi'));
    }

    /**
     * Print/view invoice transaksi.
     */
    public function invoice(Transaksi $transaksi): View
    {
        // Authorization: pastikan transaksi milik user yang login
        if ($transaksi->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        $transaksi->load([
            'vendor',
            'pelanggan',
            'transaksiItem.produk',
            'transaksiItemSpecifications.bahan',
        ]);

        return view('user.transactions.invoice', compact('transaksi'));
    }
}
