<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function vendorDashboard()
    {
        try {
            return view('dashboard')->with('toast_success', 'Welcome to your dashboard');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error getting vendors: ' . $e->getMessage());
        }
    }

    public function devDashboard()
    {
        try {
            return view('dev.dashboard')->with('toast_success', 'Welcome to your dashboard');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error getting vendors: ' . $e->getMessage());
        }
    }
}
