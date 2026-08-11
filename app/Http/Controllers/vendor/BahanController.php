<?php

namespace App\Http\Controllers\vendor;

use App\Models\Vendor\Bahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Concerns\HasVendorContext;
use App\Models\Vendor\WholesalePrice;
use App\Http\Requests\StoreBahanRequest;
use App\Http\Requests\UpdateBahanRequest;
use App\Http\Responses\FlashMessage;



class BahanController extends Controller
{
    use HasVendorContext;


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->requireVendor();

        $bahan = Bahan::search($request->search)
            ->stockFilter($request->stok)
            ->wholesaleFilter($request->has_wholesale)
            ->latest()
            ->paginate(10);

        return view('bahan.index', compact('bahan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->requireVendor();

        return view('bahan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBahanRequest $request)
    {
        $vendorId = $this->requireVendor()->id;

        $validated = $request->validated();

        return DB::transaction(function () use ($request, $validated, $vendorId) {
            // Add vendor_id to validated data
            $validated['vendor_id'] = $vendorId;

            // Create bahan
            $bahan = Bahan::create($validated);

            // Process wholesale prices
            $this->processWholesalePrices($bahan->id, $request, $vendorId);

            return FlashMessage::success(redirect()->route('vendor.materials.index'), 'Bahan berhasil ditambahkan!');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->requireVendor();

        $bahan = Bahan::with('wholesalePrices')->findOrFail($id);
        return view('bahan.show', compact('bahan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->requireVendor();

        $bahan = Bahan::with('wholesalePrices')->findOrFail($id);
        return view('bahan.edit', compact('bahan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBahanRequest $request, string $id)
    {
        $this->requireVendor();

        $validated = $request->validated();

        return DB::transaction(function () use ($request, $validated, $id) {
            $bahan = Bahan::findOrFail($id);
            $bahan->update($validated);

            // Update existing wholesale prices
            $this->updateExistingWholesalePrices($request);

            // Add new wholesale prices
            $this->addNewWholesalePrices($bahan->id, $request, $this->getVendorId());

            // Delete removed wholesale prices
            $this->deleteRemovedWholesalePrices($request);

            return FlashMessage::success(redirect()->route('vendor.materials.index'), 'Bahan berhasil diperbarui!');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->requireVendor();

        return DB::transaction(function () use ($id) {
            $bahan = Bahan::findOrFail($id);

            // Delete associated wholesale prices
            $bahan->wholesalePrices()->delete();

            // Delete the bahan
            $bahan->delete();

            return FlashMessage::success(redirect()->route('vendor.materials.index'), 'Bahan berhasil dihapus!');
        });
    }


    /**
     * Process wholesale prices for create
     */
    private function processWholesalePrices($bahanId, $request, $vendorId)
    {
        if (!$request->has('wholesale_min_qty') || !is_array($request->wholesale_min_qty)) {
            return;
        }

        $wholesaleData = [];

        foreach ($request->wholesale_min_qty as $key => $min_qty) {
            if (empty($min_qty) || !isset($request->wholesale_price[$key])) {
                continue;
            }

            $wholesaleData[] = [
                'vendor_id' => $vendorId,
                'bahan_id' => $bahanId,
                'min_quantity' => $min_qty,
                'max_quantity' => $request->wholesale_max_qty[$key] ?? null,
                'harga' => $request->wholesale_price[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($wholesaleData)) {
            WholesalePrice::insert($wholesaleData);
        }
    }

    /**
     * Update existing wholesale prices
     */
    private function updateExistingWholesalePrices($request)
    {
        if (!$request->has('wholesale_id') || !is_array($request->wholesale_id)) {
            return;
        }

        foreach ($request->wholesale_id as $key => $wholesale_id) {
            if (empty($wholesale_id)) {
                continue;
            }

            $wholesale = WholesalePrice::find($wholesale_id);
            if (!$wholesale) {
                continue;
            }

            $wholesale->update([
                'min_quantity' => $request->wholesale_min_qty[$key],
                'max_quantity' => $request->wholesale_max_qty[$key],
                'harga' => $request->wholesale_price[$key],
            ]);
        }
    }

    /**
     * Add new wholesale prices
     */
    private function addNewWholesalePrices($bahanId, $request, $vendorId)
    {
        if (!$request->has('new_wholesale_min_qty') || !is_array($request->new_wholesale_min_qty)) {
            return;
        }

        foreach ($request->new_wholesale_min_qty as $key => $min_qty) {
            if (empty($min_qty) || !isset($request->new_wholesale_price[$key])) {
                continue;
            }

            WholesalePrice::create([
                'vendor_id' => $vendorId,
                'bahan_id' => $bahanId,
                'min_quantity' => $min_qty,
                'max_quantity' => $request->new_wholesale_max_qty[$key] ?? null,
                'harga' => $request->new_wholesale_price[$key],
            ]);
        }
    }

    /**
     * Delete removed wholesale prices
     */
    private function deleteRemovedWholesalePrices($request)
    {
        if (!$request->has('deleted_wholesale_ids') || empty($request->deleted_wholesale_ids)) {
            return;
        }

        $deleted_ids = explode(',', $request->deleted_wholesale_ids);
        WholesalePrice::whereIn('id', $deleted_ids)->delete();
    }

    /**
     * Bulk update bahan fields (stok, hpp)
     */
    public function bulkUpdate(Request $request)
    {
        $this->requireVendor();

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:bahans,id',
            'field' => 'required|in:stok,hpp',
            'value' => 'required',
        ]);

        $field = $request->field;
        $value = $request->value;

        // Validate value based on field
        if ($field === 'stok') {
            if (!is_numeric($value) || $value < 0) {
                return FlashMessage::backError('Nilai stok harus angka positif.');
            }
            $value = (int) $value;
        } elseif ($field === 'hpp') {
            if (!is_numeric($value) || $value < 0) {
                return FlashMessage::backError('Nilai HPP harus angka positif.');
            }
            $value = (float) $value;
        }

        $updated = Bahan::whereIn('id', $request->ids)->update([$field => $value]);

        return FlashMessage::backSuccess("Berhasil memperbarui {$updated} bahan ({$field}).");
    }
}
