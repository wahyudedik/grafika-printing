<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CmsController extends Controller
{
    /**
     * Display CMS settings dashboard
     */
    public function index()
    {
        $settings = CmsSetting::getAllGrouped();
        $categories = [
            'general' => 'General Settings',
            'hero' => 'Hero Section',
            'footer' => 'Footer',
            'contact' => 'Contact Information',
            'social' => 'Social Media',
            'seo' => 'SEO Settings',
            'features' => 'Features Section',
            'testimonials' => 'Testimonials',
            'newsletter' => 'Newsletter'
        ];

        return view('admin.cms.index', compact('settings', 'categories'));
    }

    /**
     * Show settings by category
     */
    public function show($category)
    {
        $settings = CmsSetting::getByCategory($category);
        $categoryName = ucfirst($category);

        return view('admin.cms.show', compact('settings', 'category', 'categoryName'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'required|string',
            'settings.*.category' => 'required|string',
            'settings.*.label' => 'required|string'
        ]);

        try {
            foreach ($request->settings as $settingData) {
                $setting = CmsSetting::updateOrCreate(
                    ['key' => $settingData['key']],
                    [
                        'value' => $settingData['value'],
                        'type' => $settingData['type'],
                        'category' => $settingData['category'],
                        'label' => $settingData['label'],
                        'description' => $settingData['description'] ?? null,
                        'is_active' => $settingData['is_active'] ?? true,
                        'sort_order' => $settingData['sort_order'] ?? 0
                    ]
                );
            }

            // Clear cache
            CmsSetting::clearCache();

            return redirect()->back()
                ->with('toast_success', 'CMS settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('CMS update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('toast_error', 'Failed to update CMS settings: ' . $e->getMessage());
        }
    }

    /**
     * Update single setting
     */
    public function updateSetting(Request $request, $id)
    {
        $request->validate([
            'value' => 'nullable',
            'label' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            $setting = CmsSetting::findOrFail($id);
            $setting->update($request->all());

            // Clear cache
            CmsSetting::clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('CMS setting update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload image
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'key' => 'required|string'
        ]);

        try {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('cms/images', $filename, 'public');

            // Update setting
            CmsSetting::set($request->key, $path, 'image');

            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'message' => 'Image uploaded successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('CMS image upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new setting
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:cms_settings,key',
            'value' => 'nullable',
            'type' => 'required|string',
            'category' => 'required|string',
            'label' => 'required|string',
            'description' => 'nullable|string'
        ]);

        try {
            CmsSetting::create($request->all());

            return redirect()->back()
                ->with('toast_success', 'New setting created successfully!');
        } catch (\Exception $e) {
            Log::error('CMS setting creation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('toast_error', 'Failed to create setting: ' . $e->getMessage());
        }
    }

    /**
     * Delete setting
     */
    public function destroy($id)
    {
        try {
            $setting = CmsSetting::findOrFail($id);
            $setting->delete();

            // Clear cache
            CmsSetting::clearCache();

            return redirect()->back()
                ->with('toast_success', 'Setting deleted successfully!');
        } catch (\Exception $e) {
            Log::error('CMS setting deletion error: ' . $e->getMessage());
            return redirect()->back()
                ->with('toast_error', 'Failed to delete setting: ' . $e->getMessage());
        }
    }

    /**
     * Toggle setting status
     */
    public function toggle($id)
    {
        try {
            $setting = CmsSetting::findOrFail($id);
            $setting->update(['is_active' => !$setting->is_active]);

            // Clear cache
            CmsSetting::clearCache();

            return response()->json([
                'success' => true,
                'is_active' => $setting->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('CMS toggle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle setting'
            ], 500);
        }
    }


    /**
     * Get settings by category (API)
     */
    public function getSettings($category = null)
    {
        try {
            if ($category) {
                $settings = CmsSetting::getByCategory($category);
            } else {
                $settings = CmsSetting::getAllGrouped();
            }

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('CMS get settings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get settings'
            ], 500);
        }
    }

    /**
     * Preview landing page
     */
    public function preview()
    {
        return view('welcome');
    }

    /**
     * Export CMS settings
     */
    public function export()
    {
        try {
            $settings = CmsSetting::all();
            $filename = 'cms_settings_' . date('Y-m-d_H-i-s') . '.json';

            return response()->json($settings)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            Log::error('CMS export error: ' . $e->getMessage());
            return redirect()->back()
                ->with('toast_error', 'Failed to export settings: ' . $e->getMessage());
        }
    }

    /**
     * Import CMS settings
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json|max:1024'
        ]);

        try {
            $file = $request->file('file');
            $content = file_get_contents($file->getPathname());
            $settings = json_decode($content, true);

            if (!$settings) {
                throw new \Exception('Invalid JSON file');
            }

            foreach ($settings as $setting) {
                CmsSetting::updateOrCreate(
                    ['key' => $setting['key']],
                    $setting
                );
            }

            // Clear cache
            CmsSetting::clearCache();

            return redirect()->back()
                ->with('toast_success', 'Settings imported successfully!');
        } catch (\Exception $e) {
            Log::error('CMS import error: ' . $e->getMessage());
            return redirect()->back()
                ->with('toast_error', 'Failed to import settings: ' . $e->getMessage());
        }
    }

    /**
     * Reset to default settings
     */
    public function reset()
    {
        try {
            // Delete all current settings
            CmsSetting::truncate();

            // Run seeder
            \Artisan::call('db:seed', ['--class' => 'CmsSettingSeeder']);

            // Clear cache
            CmsSetting::clearCache();

            return redirect()->back()
                ->with('toast_success', 'Settings reset to default successfully!');
        } catch (\Exception $e) {
            Log::error('CMS reset error: ' . $e->getMessage());
            return redirect()->back()
                ->with('toast_error', 'Failed to reset settings: ' . $e->getMessage());
        }
    }
}
