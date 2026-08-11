<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Responses\FlashMessage;


class VendorBankAccountController extends Controller
{
    /**
     * Display bank account management page
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(404, 'Vendor not found');
        }

        return view('vendor.bank-accounts.index', compact('vendor'));
    }

    /**
     * Show form to add/edit bank account
     */
    public function create()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(404, 'Vendor not found');
        }

        return view('vendor.bank-accounts.create', compact('vendor'));
    }

    /**
     * Store bank account details
     */
    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(404, 'Vendor not found');
        }

        $validator = Validator::make($request->all(), [
            'account_type' => 'required|in:primary,secondary,ewallet',
            'bank_name' => 'required_if:account_type,primary,secondary|string|max:255',
            'account_number' => 'required_if:account_type,primary,secondary|string|max:50',
            'account_name' => 'required_if:account_type,primary,secondary|string|max:255',
            'bank_code' => 'nullable|string|max:10',
            'ewallet_provider' => 'required_if:account_type,ewallet|string|max:255',
            'ewallet_number' => 'required_if:account_type,ewallet|string|max:50',
            'ewallet_name' => 'required_if:account_type,ewallet|string|max:255',
            'bank_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'bank_notes'
        ]);

        if ($request->account_type === 'primary') {
            $data['primary_bank_name'] = $request->bank_name;
            $data['primary_account_number'] = $request->account_number;
            $data['primary_account_name'] = $request->account_name;
            $data['primary_bank_code'] = $request->bank_code;
        } elseif ($request->account_type === 'secondary') {
            $data['secondary_bank_name'] = $request->bank_name;
            $data['secondary_account_number'] = $request->account_number;
            $data['secondary_account_name'] = $request->account_name;
            $data['secondary_bank_code'] = $request->bank_code;
        } elseif ($request->account_type === 'ewallet') {
            $data['ewallet_provider'] = $request->ewallet_provider;
            $data['ewallet_number'] = $request->ewallet_number;
            $data['ewallet_name'] = $request->ewallet_name;
        }

        $vendor->update($data);

        return FlashMessage::success(redirect()->route('vendor.bank-accounts.index'), 'Detail rekening berhasil disimpan');
    }

    /**
     * Show form to edit bank account
     */
    public function edit($type)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(404, 'Vendor not found');
        }

        if (!in_array($type, ['primary', 'secondary', 'ewallet'])) {
            abort(404, 'Invalid account type');
        }

        return view('vendor.bank-accounts.edit', compact('vendor', 'type'));
    }

    /**
     * Update bank account details
     */
    public function update(Request $request, $type)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(404, 'Vendor not found');
        }

        if (!in_array($type, ['primary', 'secondary', 'ewallet'])) {
            abort(404, 'Invalid account type');
        }

        $validator = Validator::make($request->all(), [
            'bank_name' => 'required_if:type,primary,secondary|string|max:255',
            'account_number' => 'required_if:type,primary,secondary|string|max:50',
            'account_name' => 'required_if:type,primary,secondary|string|max:255',
            'bank_code' => 'nullable|string|max:10',
            'ewallet_provider' => 'required_if:type,ewallet|string|max:255',
            'ewallet_number' => 'required_if:type,ewallet|string|max:50',
            'ewallet_name' => 'required_if:type,ewallet|string|max:255',
            'bank_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'bank_notes'
        ]);

        if ($type === 'primary') {
            $data['primary_bank_name'] = $request->bank_name;
            $data['primary_account_number'] = $request->account_number;
            $data['primary_account_name'] = $request->account_name;
            $data['primary_bank_code'] = $request->bank_code;
        } elseif ($type === 'secondary') {
            $data['secondary_bank_name'] = $request->bank_name;
            $data['secondary_account_number'] = $request->account_number;
            $data['secondary_account_name'] = $request->account_name;
            $data['secondary_bank_code'] = $request->bank_code;
        } elseif ($type === 'ewallet') {
            $data['ewallet_provider'] = $request->ewallet_provider;
            $data['ewallet_number'] = $request->ewallet_number;
            $data['ewallet_name'] = $request->ewallet_name;
        }

        $vendor->update($data);

        return FlashMessage::success(redirect()->route('vendor.bank-accounts.index'), 'Detail rekening berhasil diperbarui');
    }

    /**
     * Delete bank account
     */
    public function destroy($type)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(404, 'Vendor not found');
        }

        if (!in_array($type, ['primary', 'secondary', 'ewallet'])) {
            abort(404, 'Invalid account type');
        }

        $data = [];

        if ($type === 'primary') {
            $data['primary_bank_name'] = null;
            $data['primary_account_number'] = null;
            $data['primary_account_name'] = null;
            $data['primary_bank_code'] = null;
        } elseif ($type === 'secondary') {
            $data['secondary_bank_name'] = null;
            $data['secondary_account_number'] = null;
            $data['secondary_account_name'] = null;
            $data['secondary_bank_code'] = null;
        } elseif ($type === 'ewallet') {
            $data['ewallet_provider'] = null;
            $data['ewallet_number'] = null;
            $data['ewallet_name'] = null;
        }

        $vendor->update($data);

        return FlashMessage::success(redirect()->route('vendor.bank-accounts.index'), 'Detail rekening berhasil dihapus');
    }

    /**
     * Get bank account details for API
     */
    public function getAccountDetails()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }

        return response()->json([
            'primary' => $vendor->getPrimaryBankAccount(),
            'secondary' => $vendor->getSecondaryBankAccount(),
            'ewallet' => $vendor->getEwalletAccount(),
            'all_accounts' => $vendor->getAllBankAccounts(),
            'default_account' => $vendor->getDefaultWithdrawalAccount(),
            'verified' => $vendor->hasVerifiedBankAccount()
        ]);
    }

    /**
     * Get available banks for dropdown
     */
    public function getBanks()
    {
        $banks = [
            ['code' => 'BCA', 'name' => 'Bank Central Asia'],
            ['code' => 'BRI', 'name' => 'Bank Rakyat Indonesia'],
            ['code' => 'BNI', 'name' => 'Bank Negara Indonesia'],
            ['code' => 'MANDIRI', 'name' => 'Bank Mandiri'],
            ['code' => 'BTN', 'name' => 'Bank Tabungan Negara'],
            ['code' => 'CIMB', 'name' => 'CIMB Niaga'],
            ['code' => 'DANAMON', 'name' => 'Bank Danamon'],
            ['code' => 'PERMATA', 'name' => 'Bank Permata'],
            ['code' => 'MAYBANK', 'name' => 'Maybank Indonesia'],
            ['code' => 'OCBC', 'name' => 'OCBC NISP'],
            ['code' => 'PANIN', 'name' => 'Bank Panin'],
            ['code' => 'UOB', 'name' => 'United Overseas Bank'],
        ];

        return response()->json($banks);
    }

    /**
     * Get available e-wallet providers
     */
    public function getEwalletProviders()
    {
        $providers = [
            ['code' => 'OVO', 'name' => 'OVO'],
            ['code' => 'DANA', 'name' => 'DANA'],
            ['code' => 'GOPAY', 'name' => 'GoPay'],
            ['code' => 'SHOPEEPAY', 'name' => 'ShopeePay'],
            ['code' => 'LINKAJA', 'name' => 'LinkAja'],
            ['code' => 'JENIUS', 'name' => 'Jenius'],
            ['code' => 'DIGIBANK', 'name' => 'Digibank by DBS'],
        ];

        return response()->json($providers);
    }
}
