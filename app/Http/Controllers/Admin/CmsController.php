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
            'social' => 'Social Media'
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
                'is_active' => $setting->is_active,
                'message' => 'Setting status updated!'
            ]);
        } catch (\Exception $e) {
            Log::error('CMS setting toggle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get settings for API
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
            Log::error('CMS settings API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get settings: ' . $e->getMessage()
            ], 500);
        }
    }
}
