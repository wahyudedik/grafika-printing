<?php

namespace App\Http\Controllers\vendor;

use App\Http\Responses\FlashMessage;
use App\Http\Controllers\Controller;
use App\Http\Concerns\HasVendorContext;
use App\Models\Vendor\Pelanggan;
use App\Facades\Tenant;
use App\Http\Requests\StorePelangganRequest;
use App\Http\Requests\UpdatePelangganRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class PelangganController extends Controller
{
    use HasVendorContext;


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->requireVendor();

        $query = Pelanggan::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'like', "%{$searchTerm}%")
                    ->orWhere('kode', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('no_telp', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by transaksi status if provided
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'active') {
                $query->whereHas('transaksi');
            } elseif ($request->status === 'inactive') {
                $query->whereDoesntHave('transaksi');
            }
        }

        // Eager load transaction count + latest transaction to avoid N+1 queries
        $query->withCount('transaksi')
            ->with(['transaksi' => function ($q) {
                $q->latest('created_at')->limit(1);
            }]);

        $pelanggan = $query->latest()->paginate(10);

        // Update transaksi_terakhir from the eager-loaded latest transaction
        foreach ($pelanggan as $customer) {
            if (!$customer->transaksi_terakhir && $customer->transaksi_count > 0 && $customer->transaksi->isNotEmpty()) {
                $customer->transaksi_terakhir = $customer->transaksi->first()->created_at;
            }
        }

        return view('pelanggan.index', compact('pelanggan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->requireVendor();

        return view('pelanggan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePelangganRequest $request)
    {
        $vendorId = $this->requireVendor()->id;

        $validated = $request->validated();

        // Generate a unique customer code
        $validated['kode'] = 'C' . strtoupper(Str::random(6));

        // Set vendor_id from authenticated user's vendor
        $validated['vendor_id'] = $vendorId;

        $pelanggan = Pelanggan::create($validated);

        return FlashMessage::success(redirect()->route('vendor.customers.index'), 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->requireVendor();

        $pelanggan = Pelanggan::findOrFail($id);

        return view('pelanggan.show', compact('pelanggan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->requireVendor();

        $pelanggan = Pelanggan::findOrFail($id);

        return view('pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePelangganRequest $request, string $id)
    {
        $this->requireVendor();

        $pelanggan = Pelanggan::findOrFail($id);

        $validated = $request->validated();

        // Ensure we don't change the vendor_id
        $pelanggan->update($validated);

        return FlashMessage::success(redirect()->route('vendor.customers.index'), 'Pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->requireVendor();

        $pelanggan = Pelanggan::findOrFail($id);

        // Check if the customer has any transactions
        if ($pelanggan->transaksi()->count() > 0) {
            return FlashMessage::error(redirect()->route('vendor.customers.index'), 'Pelanggan tidak dapat dihapus karena memiliki transaksi.');
        }

        $pelanggan->delete();

        return FlashMessage::success(redirect()->route('vendor.customers.index'), 'Pelanggan berhasil dihapus.');
    }
}
