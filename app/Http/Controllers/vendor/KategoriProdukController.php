<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Http\Concerns\HasVendorContext;
use App\Models\Vendor\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\StoreKategoriProdukRequest;
use App\Http\Requests\UpdateKategoriProdukRequest;
use App\Http\Responses\FlashMessage;



class KategoriProdukController extends Controller
{
    use HasVendorContext;


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->requireVendor();

        $query = KategoriProduk::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nama_kategori', 'like', '%' . $request->search . '%');
        }

        // Sort functionality
        $sortField = $request->sort ?? 'nama_kategori';
        $sortOrder = $request->order ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        $kategori = $query->paginate(10)->appends(request()->query());

        return view('kategori_produk.index', compact('kategori'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->requireVendor();

        return view('kategori_produk.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKategoriProdukRequest $request)
    {
        $vendorId = $this->requireVendor()->id;

        $validated = $request->validated();

        KategoriProduk::create([
            'nama_kategori' => $validated['nama_kategori'],
            'slug' => Str::slug($validated['nama_kategori']),
            'vendor_id' => $vendorId
        ]);

        return FlashMessage::success(redirect()->route('vendor.categories.index'), 'Kategori produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KategoriProduk $kategoriProduk)
    {
        $this->requireVendor();

        return view('kategori_produk.show', compact('kategoriProduk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriProduk $kategoriProduk)
    {
        $this->requireVendor();

        return view('kategori_produk.edit', compact('kategoriProduk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKategoriProdukRequest $request, KategoriProduk $kategoriProduk)
    {
        $this->requireVendor();

        $validated = $request->validated();

        $kategoriProduk->update([
            'nama_kategori' => $validated['nama_kategori'],
            'slug' => Str::slug($request->nama_kategori)
        ]);

        return FlashMessage::success(redirect()->route('vendor.categories.index'), 'Kategori produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriProduk $kategoriProduk)
    {
        $this->requireVendor();

        // Check if category has products
        if ($kategoriProduk->produk()->count() > 0) {
            return FlashMessage::error(redirect()->route('vendor.categories.index'), 'Kategori tidak dapat dihapus karena masih memiliki produk terkait.');
        }

        $kategoriProduk->delete();

        return FlashMessage::success(redirect()->route('vendor.categories.index'), 'Kategori produk berhasil dihapus.');
    }
}
