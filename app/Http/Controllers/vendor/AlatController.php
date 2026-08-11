<?php

namespace App\Http\Controllers\vendor;

use App\Models\Vendor;
use App\Models\Vendor\Alat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Concerns\HasVendorContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreAlatRequest;
use App\Http\Requests\UpdateAlatRequest;
use App\Http\Responses\FlashMessage;



class AlatController extends Controller
{
    use HasVendorContext;


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->requireVendor();

        try {
            // Validate input
            $request->validate([
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|in:aktif,maintenance,rusak',
                'tersedia' => 'nullable|in:yes,no'
            ]);

            $query = Alat::query();

            // Search filter
            if ($request->has('search') && !empty($request->search)) {
                $query->search($request->search);
            }

            // Status filter
            if ($request->has('status') && !empty($request->status)) {
                $query->byStatus($request->status);
            }

            // Availability filter (tambahan)
            if ($request->has('tersedia') && $request->tersedia !== '') {
                $query->where('tersedia', $request->tersedia === 'yes');
            }

            // Sorting
            $sort = $request->input('sort', 'created_at');
            $order = $request->input('order', 'desc');
            $query->orderBy($sort, $order);

            $alat = $query->paginate(10);
            return view('alat.index', compact('alat'));
        } catch (\Exception $e) {
            return FlashMessage::backError('Error getting alat: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->requireVendor();

        return view('alat.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlatRequest $request)
    {
        try {
            $vendorId = $this->requireVendor()->id;

            // Create alat with vendor_id
            Alat::create(array_merge($request->all(), ['vendor_id' => $vendorId]));

            return FlashMessage::success(redirect()->route('vendor.tools.index'), 'Alat berhasil ditambahkan');
        } catch (\Exception $e) {
            return FlashMessage::backError('Error adding alat: ' . $e->getMessage())->withInput();
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->requireVendor();

        try {
            $alat = Alat::findOrFail($id);
            return view('alat.show', compact('alat'));
        } catch (\Exception $e) {
            return FlashMessage::backError('Error finding alat: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->requireVendor();

        try {
            $alat = Alat::findOrFail($id);
            return view('alat.edit', compact('alat'));
        } catch (\Exception $e) {
            return FlashMessage::backError('Error finding alat: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlatRequest $request, string $id)
    {
        $this->requireVendor();

        try {
            $alat = Alat::findOrFail($id);

            // Preserve the vendor_id
            $data = $request->all();
            $data['vendor_id'] = $alat->vendor_id;

            $alat->update($data);
            return FlashMessage::success(redirect()->route('vendor.tools.index'), 'Alat berhasil diperbarui');
        } catch (\Exception $e) {
            return FlashMessage::backError('Error updating alat: ' . $e->getMessage())->withInput();
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->requireVendor();

        try {
            $alat = Alat::findOrFail($id);
            $alat->delete();
            return FlashMessage::success(redirect()->route('vendor.tools.index'), 'Alat berhasil dihapus');
        } catch (\Exception $e) {
            return FlashMessage::backError('Error deleting alat: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update alat fields (status, tersedia)
     */
    public function bulkUpdate(Request $request)
    {
        $this->requireVendor();

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:alats,id',
            'field' => 'required|in:status,tersedia',
            'value' => 'required',
        ]);

        $field = $request->field;
        $value = $request->value;

        // Validate value based on field
        if ($field === 'status') {
            if (!in_array($value, ['aktif', 'maintenance', 'rusak'])) {
                return FlashMessage::backError('Status tidak valid.');
            }
        } elseif ($field === 'tersedia') {
            $value = (bool) $value;
        }

        $updated = Alat::whereIn('id', $request->ids)->update([$field => $value]);

        return FlashMessage::backSuccess("Berhasil memperbarui {$updated} alat ({$field}).");
    }
}
