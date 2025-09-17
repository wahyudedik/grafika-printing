<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        return view('dev.pulse.activity');
    }
}
