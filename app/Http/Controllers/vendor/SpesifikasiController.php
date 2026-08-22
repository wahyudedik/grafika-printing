<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Http\Concerns\HasVendorContext;
use App\Models\Vendor\Spesifikasi;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSpesifikasiRequest;
use App\Http\Requests\UpdateSpesifikasiRequest;
use App\Http\Responses\FlashMessage;



class SpesifikasiController extends Controller
{
    use HasVendorContext;


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->requireVendor();

        $query = Spesifikasi::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nama_spesifikasi', 'like', '%' . $request->search . '%');
        }

        // Filter by tipe_input
        if ($request->has('tipe_input') && !empty($request->tipe_input)) {
            $query->where('tipe_input', $request->tipe_input);
        }

        $spesifikasi = $query->paginate(10);
        return view('spesifikasi.index', compact('spesifikasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->requireVendor();

        $tipeInput = Spesifikasi::TIPE_INPUT;
        return view('spesifikasi.create', compact('tipeInput'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpesifikasiRequest $request)
    {
        $validated = $request->validated();

        // Set default empty string for satuan when not provided
        if (!isset($validated['satuan'])) {
            $validated['satuan'] = '';
        }

        Spesifikasi::create($validated);

        return FlashMessage::success(redirect()->route('vendor.specifications.index'), 'Spesifikasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->requireVendor();

        $spesifikasi = Spesifikasi::with('spesifikasiProduk')->findOrFail($id);
        return view('spesifikasi.show', compact('spesifikasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->requireVendor();

        $spesifikasi = Spesifikasi::findOrFail($id);
        $tipeInput = Spesifikasi::TIPE_INPUT;
        return view('spesifikasi.edit', compact('spesifikasi', 'tipeInput'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpesifikasiRequest $request, string $id)
    {
        $this->requireVendor();

        $spesifikasi = Spesifikasi::findOrFail($id);

        $validated = $request->validated();

        // Set default empty string for satuan when not provided
        if (!isset($validated['satuan'])) {
            $validated['satuan'] = '';
        }

        $spesifikasi->update($validated);

        return FlashMessage::success(redirect()->route('vendor.specifications.index'), 'Spesifikasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->requireVendor();

        $spesifikasi = Spesifikasi::findOrFail($id);

        // Check if the specification is being used
        if ($spesifikasi->spesifikasiProduk()->count() > 0) {
            return FlashMessage::error(redirect()->route('vendor.specifications.index'), 'Spesifikasi tidak dapat dihapus karena sedang digunakan oleh produk.');
        }

        $spesifikasi->delete();

        return FlashMessage::success(redirect()->route('vendor.specifications.index'), 'Spesifikasi berhasil dihapus.');
    }
}
