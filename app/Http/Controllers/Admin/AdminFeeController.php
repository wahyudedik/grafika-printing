<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminFeeSetting;
use App\Models\AdminFeeTransaction;
use App\Services\AdminFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminFeeController extends Controller
{
    protected $adminFeeService;

    public function __construct(AdminFeeService $adminFeeService)
    {
        $this->adminFeeService = $adminFeeService;
    }

    /**
     * Display admin fee settings
     */
    public function index()
    {
        $settings = AdminFeeSetting::with(['createdBy', 'updatedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.admin-fees.index', compact('settings'));
    }

    /**
     * Show form to create admin fee setting
     */
    public function create()
    {
        return view('admin.admin-fees.create');
    }

    /**
     * Store admin fee setting
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:admin_fee_settings,name',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_amount' => 'nullable|numeric|min:0|gt:minimum_amount',
            'category' => 'required|string|max:255',
            'effective_from' => 'nullable|date',
            'effective_until' => 'nullable|date|after:effective_from',
            'conditions' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $setting = AdminFeeSetting::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'value' => $request->value,
            'minimum_amount' => $request->minimum_amount ?? 0,
            'maximum_amount' => $request->maximum_amount,
            'category' => $request->category,
            'effective_from' => $request->effective_from,
            'effective_until' => $request->effective_until,
            'conditions' => $request->conditions,
            'created_by' => Auth::id(),
            'is_active' => true
        ]);

        return redirect()->route('admin.admin-fees.index')
            ->with('success', 'Pengaturan biaya admin berhasil dibuat');
    }

    /**
     * Show admin fee setting details
     */
    public function show(AdminFeeSetting $adminFee)
    {
        $adminFee->load(['createdBy', 'updatedBy']);

        return view('admin.admin-fees.show', compact('adminFee'));
    }

    /**
     * Show form to edit admin fee setting
     */
    public function edit(AdminFeeSetting $adminFee)
    {
        return view('admin.admin-fees.edit', compact('adminFee'));
    }

    /**
     * Update admin fee setting
     */
    public function update(Request $request, AdminFeeSetting $adminFee)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:admin_fee_settings,name,' . $adminFee->id,
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_amount' => 'nullable|numeric|min:0|gt:minimum_amount',
            'category' => 'required|string|max:255',
            'effective_from' => 'nullable|date',
            'effective_until' => 'nullable|date|after:effective_from',
            'conditions' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $adminFee->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'value' => $request->value,
            'minimum_amount' => $request->minimum_amount ?? 0,
            'maximum_amount' => $request->maximum_amount,
            'category' => $request->category,
            'effective_from' => $request->effective_from,
            'effective_until' => $request->effective_until,
            'conditions' => $request->conditions,
            'is_active' => $request->has('is_active'),
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('admin.admin-fees.index')
            ->with('success', 'Pengaturan biaya admin berhasil diperbarui');
    }

    /**
     * Toggle admin fee setting status
     */
    public function toggleStatus(AdminFeeSetting $adminFee)
    {
        $adminFee->update([
            'is_active' => !$adminFee->is_active,
            'updated_by' => Auth::id()
        ]);

        $status = $adminFee->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Pengaturan biaya admin berhasil {$status}");
    }

    /**
     * Delete admin fee setting
     */
    public function destroy(AdminFeeSetting $adminFee)
    {
        $adminFee->delete();

        return redirect()->route('admin.admin-fees.index')
            ->with('success', 'Pengaturan biaya admin berhasil dihapus');
    }

    /**
     * Display admin fee transactions
     */
    public function transactions(Request $request)
    {
        $query = AdminFeeTransaction::with(['auction', 'vendor', 'user']);

        // Filter by date range
        if ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by vendor
        if ($request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.admin-fees.transactions', compact('transactions'));
    }

    /**
     * Display admin fee statistics
     */
    public function statistics(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now()->endOfMonth();

        $statistics = $this->adminFeeService->getAdminFeeStatistics($startDate, $endDate);

        return view('admin.admin-fees.statistics', compact('statistics', 'startDate', 'endDate'));
    }

    /**
     * Get fee preview for testing
     */
    public function getFeePreview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        $preview = $this->adminFeeService->getFeePreview(
            $request->amount,
            $request->payment_method ?? 'bank_transfer'
        );

        return response()->json($preview);
    }

    /**
     * Get vendor fee statistics
     */
    public function getVendorStatistics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        $statistics = $this->adminFeeService->getVendorFeeStatistics(
            $request->vendor_id,
            $request->start_date,
            $request->end_date
        );

        return response()->json($statistics);
    }
}
