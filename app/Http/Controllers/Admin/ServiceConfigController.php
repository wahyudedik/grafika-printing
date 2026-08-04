<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceConfig;
use App\Services\ServiceConfigOverride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ServiceConfigController extends Controller
{
    /**
     * Display a listing of all service configs grouped by service
     */
    public function index()
    {
        $configs = ServiceConfig::getGroupedByService();
        $services = ServiceConfig::getAvailableServices();
        $statistics = ServiceConfig::getStatistics();

        return view('dev.service-configs.index', compact('configs', 'services', 'statistics'));
    }

    /**
     * Display configs for a specific service
     */
    public function show(string $service)
    {
        $services = ServiceConfig::getAvailableServices();

        if (!isset($services[$service])) {
            return redirect()->route('admin.service-configs.index')
                ->with('error', "Service '{$service}' tidak ditemukan.");
        }

        $configs = ServiceConfig::forService($service);
        $serviceInfo = $services[$service];

        return view('dev.service-configs.show', compact('configs', 'service', 'serviceInfo', 'services'));
    }

    /**
     * Store a new config
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|exists:service_configs,service_name|bail',
            'key' => 'required|string|max:255',
            'value' => 'nullable|string',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_encrypted' => 'boolean',
            'is_masked' => 'boolean',
        ]);

        // Check if key already exists for this service
        $exists = ServiceConfig::where('service_name', $validated['service_name'])
            ->where('key', $validated['key'])
            ->exists();

        if ($exists) {
            return back()->with('error', "Key '{$validated['key']}' sudah ada untuk service ini.");
        }

        ServiceConfig::setValue(
            $validated['service_name'],
            $validated['key'],
            $validated['value'],
            [
                'label' => $validated['label'],
                'description' => $validated['description'] ?? null,
                'is_encrypted' => $validated['is_encrypted'] ?? false,
                'is_masked' => $validated['is_masked'] ?? true,
            ]
        );

        // Apply config override
        ServiceConfigOverride::applyFor($validated['service_name']);

        return back()->with('success', "Config '{$validated['label']}' berhasil ditambahkan.");
    }

    /**
     * Update an existing config
     */
    public function update(Request $request, ServiceConfig $serviceConfig)
    {
        $validated = $request->validate([
            'value' => 'nullable|string',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $serviceConfig->update([
            'label' => $validated['label'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Update value if provided
        if ($request->has('value')) {
            $serviceConfig->setSecureValue($validated['value']);
        }

        // Apply config override
        ServiceConfigOverride::applyFor($serviceConfig->service_name);

        return back()->with('success', "Config '{$serviceConfig->label}' berhasil diupdate.");
    }

    /**
     * Remove a config
     */
    public function destroy(ServiceConfig $serviceConfig)
    {
        $serviceName = $serviceConfig->service_name;
        $label = $serviceConfig->label;

        $serviceConfig->delete();

        // Clear cache
        ServiceConfig::clearServiceCache($serviceName);
        ServiceConfigOverride::applyFor($serviceName);

        return back()->with('success', "Config '{$label}' berhasil dihapus.");
    }

    /**
     * Toggle config active status
     */
    public function toggle(ServiceConfig $serviceConfig)
    {
        $serviceConfig->toggleActive();

        // Apply config override
        ServiceConfigOverride::applyFor($serviceConfig->service_name);

        $status = $serviceConfig->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Config '{$serviceConfig->label}' berhasil {$status}.");
    }

    /**
     * Test connection for a service
     */
    public function testConnection(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string',
        ]);

        // Apply latest config from DB first
        ServiceConfigOverride::applyFor($validated['service_name']);

        $result = ServiceConfigOverride::testConnection($validated['service_name']);

        return response()->json($result);
    }

    /**
     * Seed default configs from .env
     */
    public function seedDefaults()
    {
        $count = ServiceConfig::seedDefaults();

        if ($count > 0) {
            // Apply all overrides
            ServiceConfigOverride::applyAll();

            return back()->with('success', "{$count} config default berhasil di-import dari .env.");
        }

        return back()->with('info', 'Semua config default sudah ada di database.');
    }

    /**
     * Clear all config cache
     */
    public function clearCache()
    {
        ServiceConfig::clearAllCache();
        Artisan::call('config:clear');

        return back()->with('success', 'Cache config berhasil dibersihkan.');
    }
}
