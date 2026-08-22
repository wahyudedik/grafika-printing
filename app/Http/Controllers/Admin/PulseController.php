<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PulseController extends Controller
{
    /**
     * Display the Pulse dashboard.
     */
    public function index()
    {
        return view('dev.pulse.dashboard');
    }

    /**
     * Display server statistics.
     */
    public function statistics()
    {
        return view('dev.pulse.statistics');
    }

    /**
     * Display application performance metrics.
     */
    public function performance()
    {
        return view('dev.pulse.performance');
    }

    /**
     * Display user activity metrics.
     */
    public function activity()
    {
        $topActiveUsers = User::with('vendorUser')->latest()->take(5)->get();

        return view('dev.pulse.activity', compact('topActiveUsers'));
    }
}
