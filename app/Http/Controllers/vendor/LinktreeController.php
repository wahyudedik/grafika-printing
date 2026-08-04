<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Linktree;
use App\Models\Vendor\LinktreeLink;
use App\Models\Vendor\LinktreeSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LinktreeController extends Controller
{
    /**
     * Display a listing of the vendor's linktrees.
     */
    public function index()
    {
        $vendor = Auth::user()->vendorUser()->first();
        $linktrees = Linktree::where('vendor_id', $vendor->id)
            ->withCount(['activeLinks', 'activeSocials'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendor.linktree.index', compact('linktrees', 'vendor'));
    }

    /**
     * Show the form for creating a new linktree.
     */
    public function create()
    {
        $vendor = Auth::user()->vendorUser()->first();

        // Check if vendor already has a linktree
        $existingCount = Linktree::where('vendor_id', $vendor->id)->count();

        return view('vendor.linktree.create', compact('vendor', 'existingCount'));
    }

    /**
     * Store a newly created linktree.
     */
    public function store(Request $request)
    {
        $vendor = Auth::user()->vendorUser()->first();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'custom_url' => 'required|string|max:50|unique:linktrees,custom_url|regex:/^[a-z0-9\-]+$/',
            'bio' => 'nullable|string|max:500',
            'template' => 'required|in:minimal,colorful,dark,professional',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'bg_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'button_style' => 'required|in:rounded,square,pill',
        ]);

        // Set defaults
        $validated['vendor_id'] = $vendor->id;
        $validated['is_active'] = false;
        $validated['views_count'] = 0;
        $validated['clicks_count'] = 0;

        // Set default colors based on template
        $validated['primary_color'] = $validated['primary_color'] ?? $this->getDefaultColor($validated['template'], 'primary');
        $validated['secondary_color'] = $validated['secondary_color'] ?? $this->getDefaultColor($validated['template'], 'secondary');
        $validated['bg_color'] = $validated['bg_color'] ?? $this->getDefaultColor($validated['template'], 'bg');
        $validated['text_color'] = $validated['text_color'] ?? $this->getDefaultColor($validated['template'], 'text');

        $linktree = Linktree::create($validated);

        return redirect()->route('vendor.linktree.edit', $linktree)
            ->with('success', 'Linktree berhasil dibuat! Sekarang tambahkan link dan sosial media.');
    }

    /**
     * Display the specified linktree with links management.
     */
    public function show(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $linktree->load(['links' => function ($query) {
            $query->orderBy('sort_order');
        }, 'socials' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        return view('vendor.linktree.show', compact('linktree'));
    }

    /**
     * Show the form for editing the specified linktree.
     */
    public function edit(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $linktree->load(['links' => function ($query) {
            $query->orderBy('sort_order');
        }, 'socials' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        $vendor = Auth::user()->vendorUser()->first();

        return view('vendor.linktree.edit', compact('linktree', 'vendor'));
    }

    /**
     * Update the specified linktree.
     */
    public function update(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'custom_url' => 'required|string|max:50|regex:/^[a-z0-9\-]+$/',
            'bio' => 'nullable|string|max:500',
            'template' => 'required|in:minimal,colorful,dark,professional',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'bg_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'button_style' => 'required|in:rounded,square,pill',
            'is_active' => 'boolean',
            'show_qris' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        // Check custom_url uniqueness (excluding current linktree)
        if (Linktree::where('custom_url', $validated['custom_url'])->where('id', '!=', $linktree->id)->exists()) {
            return back()->withErrors(['custom_url' => 'URL kustom sudah digunakan oleh linktree lain.'])->withInput();
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['show_qris'] = $request->boolean('show_qris');

        $linktree->update($validated);

        return back()->with('success', 'Pengaturan linktree berhasil diperbarui!');
    }

    /**
     * Remove the specified linktree.
     */
    public function destroy(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        // Delete related links and socials
        $linktree->links()->delete();
        $linktree->socials()->delete();
        $linktree->delete();

        return redirect()->route('vendor.linktree.index')
            ->with('success', 'Linktree berhasil dihapus.');
    }

    /**
     * Toggle linktree active status.
     */
    public function toggleActive(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $linktree->update(['is_active' => !$linktree->is_active]);

        $status = $linktree->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Linktree berhasil {$status}!");
    }

    // =========================================================================
    // LINK MANAGEMENT
    // =========================================================================

    /**
     * Store a new link for the linktree.
     */
    public function storeLink(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'icon' => 'nullable|string|max:50',
            'type' => 'required|in:link,qris,whatsapp,phone,email',
        ]);

        $validated['vendor_id'] = $linktree->vendor_id;
        $validated['linktree_id'] = $linktree->id;
        $validated['is_active'] = true;
        $validated['sort_order'] = $linktree->links()->max('sort_order') + 1;
        $validated['clicks_count'] = 0;

        LinktreeLink::create($validated);

        return back()->with('success', 'Link berhasil ditambahkan!');
    }

    /**
     * Update a link.
     */
    public function updateLink(Request $request, Linktree $linktree, LinktreeLink $link)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeLinkOwned($link, $linktree);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'icon' => 'nullable|string|max:50',
            'type' => 'required|in:link,qris,whatsapp,phone,email',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $link->update($validated);

        return back()->with('success', 'Link berhasil diperbarui!');
    }

    /**
     * Delete a link.
     */
    public function destroyLink(Linktree $linktree, LinktreeLink $link)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeLinkOwned($link, $linktree);

        $link->delete();

        return back()->with('success', 'Link berhasil dihapus!');
    }

    /**
     * Reorder links.
     */
    public function reorderLinks(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:linktree_links,id',
        ]);

        foreach ($request->order as $index => $linkId) {
            LinktreeLink::where('id', $linkId)
                ->where('linktree_id', $linktree->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    // =========================================================================
    // SOCIAL MEDIA MANAGEMENT
    // =========================================================================

    /**
     * Store a new social media link.
     */
    public function storeSocial(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $validated = $request->validate([
            'platform' => 'required|in:instagram,facebook,twitter,tiktok,youtube,whatsapp',
            'url' => 'required|url|max:500',
        ]);

        // Check if platform already exists for this linktree
        $exists = LinktreeSocial::where('linktree_id', $linktree->id)
            ->where('platform', $validated['platform'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Platform ' . $validated['platform'] . ' sudah ditambahkan!');
        }

        $validated['vendor_id'] = $linktree->vendor_id;
        $validated['linktree_id'] = $linktree->id;
        $validated['is_active'] = true;
        $validated['sort_order'] = $linktree->socials()->max('sort_order') + 1;

        LinktreeSocial::create($validated);

        return back()->with('success', 'Social media berhasil ditambahkan!');
    }

    /**
     * Update a social media link.
     */
    public function updateSocial(Request $request, Linktree $linktree, LinktreeSocial $social)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeSocialOwned($social, $linktree);

        $validated = $request->validate([
            'platform' => 'required|in:instagram,facebook,twitter,tiktok,youtube,whatsapp',
            'url' => 'required|url|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $social->update($validated);

        return back()->with('success', 'Social media berhasil diperbarui!');
    }

    /**
     * Delete a social media link.
     */
    public function destroySocial(Linktree $linktree, LinktreeSocial $social)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeSocialOwned($social, $linktree);

        $social->delete();

        return back()->with('success', 'Social media berhasil dihapus!');
    }

    // =========================================================================
    // UPLOAD HANDLING
    // =========================================================================

    /**
     * Upload avatar for linktree.
     */
    public function uploadAvatar(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $file = $request->file('avatar');
        $filename = 'linktree_avatar_' . $linktree->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('linktree/avatars'), $filename);

        // Delete old avatar if exists
        if ($linktree->avatar && file_exists(public_path('linktree/avatars/' . $linktree->avatar))) {
            unlink(public_path('linktree/avatars/' . $linktree->avatar));
        }

        $linktree->update(['avatar' => $filename]);

        return back()->with('success', 'Avatar berhasil diupload!');
    }

    /**
     * Upload banner for linktree.
     */
    public function uploadBanner(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $request->validate([
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $file = $request->file('banner');
        $filename = 'linktree_banner_' . $linktree->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('linktree/banners'), $filename);

        // Delete old banner if exists
        if ($linktree->banner && file_exists(public_path('linktree/banners/' . $linktree->banner))) {
            unlink(public_path('linktree/banners/' . $linktree->banner));
        }

        $linktree->update(['banner' => $filename]);

        return back()->with('success', 'Banner berhasil diupload!');
    }

    /**
     * Upload QRIS image for linktree.
     */
    public function uploadQris(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $request->validate([
            'qris_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $file = $request->file('qris_image');
        $filename = 'linktree_qris_' . $linktree->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('linktree/qris'), $filename);

        // Delete old QRIS if exists
        if ($linktree->qris_image && file_exists(public_path('linktree/qris/' . $linktree->qris_image))) {
            unlink(public_path('linktree/qris/' . $linktree->qris_image));
        }

        $linktree->update(['qris_image' => $filename, 'show_qris' => true]);

        return back()->with('success', 'Gambar QRIS berhasil diupload!');
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Authorize that the linktree belongs to the current vendor.
     */
    private function authorizeLinktree(Linktree $linktree): void
    {
        $vendor = Auth::user()->vendorUser()->first();
        if ($linktree->vendor_id !== $vendor->id) {
            abort(403, 'Anda tidak memiliki akses ke linktree ini.');
        }
    }

    /**
     * Authorize that the link belongs to the linktree.
     */
    private function authorizeLinkOwned(LinktreeLink $link, Linktree $linktree): void
    {
        if ($link->linktree_id !== $linktree->id) {
            abort(403, 'Link ini bukan milik linktree ini.');
        }
    }

    /**
     * Authorize that the social belongs to the linktree.
     */
    private function authorizeSocialOwned(LinktreeSocial $social, Linktree $linktree): void
    {
        if ($social->linktree_id !== $linktree->id) {
            abort(403, 'Social media ini bukan milik linktree ini.');
        }
    }

    /**
     * Get default color based on template.
     */
    private function getDefaultColor(string $template, string $type): string
    {
        $colors = [
            'minimal' => [
                'primary' => '#374151',
                'secondary' => '#6B7280',
                'bg' => '#FFFFFF',
                'text' => '#1F2937',
            ],
            'colorful' => [
                'primary' => '#8B5CF6',
                'secondary' => '#EC4899',
                'bg' => '#F5F3FF',
                'text' => '#1F2937',
            ],
            'dark' => [
                'primary' => '#6366F1',
                'secondary' => '#8B5CF6',
                'bg' => '#111827',
                'text' => '#F9FAFB',
            ],
            'professional' => [
                'primary' => '#1E3A5F',
                'secondary' => '#2563EB',
                'bg' => '#F1F5F9',
                'text' => '#0F172A',
            ],
        ];

        return $colors[$template][$type] ?? $colors['minimal'][$type];
    }
}
