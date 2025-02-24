<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $vendors = Vendor::paginate(5);
            return view('dev.vendors.index', compact('vendors'));
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error loading vendors');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $users = User::where('usertype', 'vendor')->get();

            return view('dev.vendors.create', compact('users'));
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error loading create vendor form');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $validation = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:vendors,email',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:500',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
                'website' => 'nullable|string|url|max:255',
                'is_active' => 'required|boolean',
                'user_id' => 'nullable|exists:users,id'
            ]);

            // save logo
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $file = $request->file('logo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('vendors', $fileName, 'public');
                $validation['logo'] = 'storage/' . $filePath;
            }
            
            $vendor = Vendor::create($validation);

            if ($request->user_id) {
                try {
                    $user = User::where('id', $request->user_id)
                        ->where('usertype', 'vendor')
                        ->firstOrFail();
                    $vendor->users()->attach($user);
                } catch (\Exception $e) {
                    return redirect()->back()->withInput()
                        ->with('toast_error', 'Error attaching user: ' . $e->getMessage());
                }
            }

            return redirect()->route('vendors.index')->with('toast_success', 'Vendor created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('toast_error', 'Error creating vendor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            return view('dev.vendors.show', compact('vendor'));
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error loading vendor');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            return view('dev.vendors.edit', compact('vendor'));
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error loading edit vendor form');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $validation = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'address' => 'required|string',
                'logo' => 'nullable|string',
                'website' => 'nullable|string',
                'is_active' => 'required|boolean',
                'users' => 'nullable|array'
            ]);

            if ($request->hasFile('logo')) {
                if ($vendor->logo && Storage::disk('public')->exists($vendor->logo)) {
                    Storage::disk('public')->delete($vendor->logo);
                }
                $validation['logo'] = $request->file('logo')->store('vendors', 'public');
            } else {
                $validation['logo'] = $vendor->logo;
            }
            $vendor->update($validation);
            $user = User::find($request->users);
            $vendor->users()->sync($user);

            return redirect()->route('vendors.index')->with('toast_success', 'Vendor updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error updating vendor');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            if ($vendor->logo && Storage::disk('public')->exists($vendor->logo)) {
                Storage::disk('public')->delete($vendor->logo);
            }
            $vendor->delete();

            return redirect()->route('vendors.index')->with('toast_success', 'Vendor deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error deleting vendor');
        }
    }
}
