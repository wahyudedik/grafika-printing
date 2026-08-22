<?php

namespace App\Http\Controllers\vendor;

use Carbon\Carbon;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use App\Models\Vendor\Transaksi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Concerns\HasVendorContext;
use App\Models\Vendor\TransaksiItem;

class LaporanController extends Controller
{
    use HasVendorContext;

    public function penjualanHarian(Request $request)
    {
        $this->requireVendor();
        // Validate input
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d'
        ]);

        $date = $request->input('date', now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        $transaksis = Transaksi::with(['pelanggan', 'transaksiItem.produk'])
            ->whereDate('tanggal_dibuat', $selectedDate)
            ->where('status', '!=', 'cancelled')
            ->orderBy('tanggal_dibuat')
            ->get();

        $totalPenjualan = $transaksis->sum('total_harga');
        $totalHpp = $transaksis->sum('hpp_total');
        $totalLaba = $transaksis->sum('laba_total');
        $totalTransaksi = $transaksis->count();

        // Group by hour for detailed view
        $penjualanPerJam = $transaksis->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_dibuat)->format('H');
        })->map(function ($group) {
            return [
                'jumlah_transaksi' => $group->count(),
                'total_penjualan' => $group->sum('total_harga')
            ];
        });

        return view('laporan.harian', compact(
            'transaksis',
            'totalPenjualan',
            'totalHpp',
            'totalLaba',
            'totalTransaksi',
            'selectedDate',
            'penjualanPerJam'
        ));
    }

    public function penjualanBulanan(Request $request)
    {
        $this->requireVendor();
        // Validate input
        $request->validate([
            'month' => 'nullable|date_format:Y-m'
        ]);

        $month = $request->input('month', now()->format('Y-m'));
        list($year, $monthNum) = explode('-', $month);
        $selectedMonth = Carbon::createFromDate($year, $monthNum, 1);

        $transaksis = Transaksi::with(['pelanggan'])
            ->whereYear('tanggal_dibuat', $year)
            ->whereMonth('tanggal_dibuat', $monthNum)
            ->where('status', '!=', 'cancelled')
            ->orderBy('tanggal_dibuat')
            ->get();

        $totalPenjualan = $transaksis->sum('total_harga');
        $totalTransaksi = $transaksis->count();

        // Group by day
        $penjualanPerHari = $transaksis->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_dibuat)->format('d');
        })->map(function ($group) {
            return [
                'jumlah_transaksi' => $group->count(),
                'total_penjualan' => $group->sum('total_harga')
            ];
        });

        // Group by product for top products analysis
        $produkTerlaris = TransaksiItem::with('produk')
            ->whereHas('transaksi', function ($query) use ($year, $monthNum) {
                $query->whereYear('tanggal_dibuat', $year)
                    ->whereMonth('tanggal_dibuat', $monthNum)
                    ->where('status', '!=', 'cancelled');
            })
            ->select('produk_id')
            ->selectRaw('SUM(kuantitas) as total_qty')
            ->groupBy('produk_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'nama_produk' => $item->produk->name, // Or whatever the actual column name is
                    'total_qty' => $item->total_qty
                ];
            });
        return view('laporan.bulanan', compact(
            'transaksis',
            'totalPenjualan',
            'totalTransaksi',
            'selectedMonth',
            'penjualanPerHari',
            'produkTerlaris'
        ));
    }

    public function penjualanTahunan(Request $request)
    {
        $this->requireVendor();
        $year = $request->input('year', now()->year);

        $transaksis = Transaksi::with(['pelanggan'])
            ->whereYear('tanggal_dibuat', $year)
            ->where('status', '!=', 'cancelled')
            ->orderBy('tanggal_dibuat')
            ->get();

        $totalPenjualan = $transaksis->sum('total_harga');
        $totalTransaksi = $transaksis->count();

        // Group by month
        $penjualanPerBulan = $transaksis->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_dibuat)->format('m');
        })->map(function ($group) {
            return [
                'jumlah_transaksi' => $group->count(),
                'total_penjualan' => $group->sum('total_harga')
            ];
        });

        // Get top 10 customers
        $pelangganTerbaik = DB::table('transaksis')
            ->join('pelanggans', 'transaksis.pelanggan_id', '=', 'pelanggans.id')
            ->select('pelanggans.nama')
            ->selectRaw('SUM(transaksis.total_harga) as total_pembelian')
            ->whereYear('transaksis.tanggal_dibuat', $year)
            ->where('transaksis.status', '!=', 'cancelled')
            ->groupBy('pelanggans.nama')
            ->orderByDesc('total_pembelian')
            ->limit(10)
            ->get();

        // Year selection options
        $years = Transaksi::selectRaw('YEAR(tanggal_dibuat) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('laporan.tahunan', compact(
            'transaksis',
            'totalPenjualan',
            'totalTransaksi',
            'year',
            'years',
            'penjualanPerBulan',
            'pelangganTerbaik'
        ));
    }

    public function exportPenjualan(Request $request)
    {
        $this->requireVendor();
        $type = $request->input('type', 'daily');
        $date = $request->input('date');

        switch ($type) {
            case 'daily':
                $selectedDate = Carbon::parse($date);
                $transaksis = Transaksi::with(['pelanggan', 'transaksiItem.produk'])
                    ->whereDate('tanggal_dibuat', $selectedDate)
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('tanggal_dibuat')
                    ->get();

                $pdf = app('dompdf.wrapper')->loadView('laporan.pdf.harian', [
                    'transaksis' => $transaksis,
                    'tanggal' => $selectedDate->format('d F Y'),
                    'totalPenjualan' => $transaksis->sum('total_harga')
                ]);

                return $pdf->download('laporan-penjualan-' . $selectedDate->format('Y-m-d') . '.pdf');

            case 'monthly':
                list($year, $monthNum) = explode('-', $date);
                $selectedMonth = Carbon::createFromDate($year, $monthNum, 1);

                $transaksis = Transaksi::with(['pelanggan'])
                    ->whereYear('tanggal_dibuat', $year)
                    ->whereMonth('tanggal_dibuat', $monthNum)
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('tanggal_dibuat')
                    ->get();

                $pdf = app('dompdf.wrapper')->loadView('laporan.pdf.bulanan', [
                    'transaksis' => $transaksis,
                    'bulan' => $selectedMonth->format('F Y'),
                    'totalPenjualan' => $transaksis->sum('total_harga')
                ]);

                return $pdf->download('laporan-penjualan-' . $selectedMonth->format('Y-m') . '.pdf');

            case 'yearly':
                $transaksis = Transaksi::with(['pelanggan'])
                    ->whereYear('tanggal_dibuat', $date)
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('tanggal_dibuat')
                    ->get();

                $pdf = app('dompdf.wrapper')->loadView('laporan.pdf.tahunan', [
                    'transaksis' => $transaksis,
                    'tahun' => $date,
                    'totalPenjualan' => $transaksis->sum('total_harga')
                ]);

                return $pdf->download('laporan-penjualan-' . $date . '.pdf');
        }
    }
}
