<?php

namespace App\Http\Controllers\Vendor;

use App\Facades\Tenant;
use App\Http\Controllers\Controller;
use App\Models\Vendor\Linktree;
use App\Models\Vendor\LinktreeLink;
use App\Models\Vendor\LinktreeSocial;
use App\Models\Vendor\LinktreeOrder;
use App\Models\Vendor\LinktreeProduct;
use App\Models\Vendor\Produk;
use App\Http\Requests\StoreLinktreeRequest;
use App\Http\Requests\UpdateLinktreeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Concerns\HasVendorContext;
use App\Http\Responses\FlashMessage;
use App\Services\AuditLogService;
use App\Actions\Linktree\CreateLinktree;



class LinktreeController extends Controller
{
    use HasVendorContext;


    /**
     * Display a listing of the vendor's linktrees.
     */
    public function index()
    {
        $vendor = $this->getVendor();
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
        $vendor = $this->getVendor();

        // Check if vendor already has a linktree
        $existingCount = Linktree::where('vendor_id', $vendor->id)->count();

        return view('vendor.linktree.create', compact('vendor', 'existingCount'));
    }

    /**
     * Store a newly created linktree.
     */
    public function store(StoreLinktreeRequest $request)
    {
        $vendor = $this->getVendor();

        $validated = $request->validated();
        $validated['vendor_id'] = $vendor->id;

        try {
            $linktree = (new CreateLinktree)->run($validated);

            AuditLogService::logCreated($linktree, 'Linktree baru dibuat: ' . $linktree->name);

            return FlashMessage::success(redirect()->route('vendor.linktree.edit', $linktree), 'Linktree berhasil dibuat! Sekarang tambahkan link dan sosial media.');
        } catch (\Exception $e) {
            \Log::error('Gagal membuat linktree: ' . $e->getMessage());
            return FlashMessage::backError('Gagal membuat linktree. Silakan coba lagi.')->withInput();
        }
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
     * Show analytics dashboard for the linktree.
     */
    public function analytics(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $linktree->load(['links' => function ($query) {
            $query->orderBy('clicks_count', 'desc');
        }, 'socials']);

        // Top links by clicks
        $topLinks = $linktree->links()
            ->where('clicks_count', '>', 0)
            ->orderBy('clicks_count', 'desc')
            ->limit(10)
            ->get();

        // Conversion rate (clicks / views)
        $conversionRate = $linktree->views_count > 0
            ? round(($linktree->clicks_count / $linktree->views_count) * 100, 1)
            : 0;

        // Total social clicks (estimated from socials count)
        $socialCount = $linktree->socials()->count();

        return view('vendor.linktree.analytics', compact('linktree', 'topLinks', 'conversionRate', 'socialCount'));
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

        $vendor = $this->getVendor();

        return view('vendor.linktree.edit', compact('linktree', 'vendor'));
    }

    /**
     * Update the specified linktree.
     */
    public function update(UpdateLinktreeRequest $request, Linktree $linktree)
    {
        $this->authorize('update', $linktree);

        $validated = $request->validated();

        // Capture old values for audit log
        $oldValues = [
            'name' => $linktree->name,
            'custom_url' => $linktree->custom_url,
            'template' => $linktree->template,
            'is_active' => $linktree->is_active,
        ];

        // Check custom_url uniqueness (excluding current linktree)
        if (Linktree::where('custom_url', $validated['custom_url'])->where('id', '!=', $linktree->id)->exists()) {
            return back()->withErrors(['custom_url' => 'URL kustom sudah digunakan oleh linktree lain.'])->withInput();
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['show_qris'] = $request->boolean('show_qris');

        try {
            $linktree->update($validated);

            AuditLogService::logUpdated($linktree, $oldValues, 'Linktree diperbarui: ' . $linktree->name);

            return FlashMessage::backSuccess('Pengaturan linktree berhasil diperbarui!');
        } catch (\Exception $e) {
            \Log::error('Gagal update linktree: ' . $e->getMessage());
            return FlashMessage::backError('Gagal memperbarui linktree. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Remove the specified linktree.
     */
    public function destroy(Linktree $linktree)
    {
        $this->authorize('delete', $linktree);

        try {
            // Delete related links and socials
            $linktree->links()->delete();
            $linktree->socials()->delete();

            AuditLogService::logDeleted($linktree, 'Linktree dihapus: ' . $linktree->name);

            $linktree->delete();

            return FlashMessage::success(redirect()->route('vendor.linktree.index'), 'Linktree berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Gagal hapus linktree: ' . $e->getMessage());
            return FlashMessage::backError('Gagal menghapus linktree. Silakan coba lagi.');
        }
    }

    /**
     * Toggle linktree active status.
     */
    public function toggleActive(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $linktree->update(['is_active' => !$linktree->is_active]);

        $status = $linktree->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return FlashMessage::backSuccess("Linktree berhasil {$status}!");
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

        return FlashMessage::backSuccess('Link berhasil ditambahkan!');
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

        return FlashMessage::backSuccess('Link berhasil diperbarui!');
    }

    /**
     * Delete a link.
     */
    public function destroyLink(Linktree $linktree, LinktreeLink $link)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeLinkOwned($link, $linktree);

        $link->delete();

        return FlashMessage::backSuccess('Link berhasil dihapus!');
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
            return FlashMessage::backError('Platform ' . $validated['platform'] . ' sudah ditambahkan!');
        }

        $validated['vendor_id'] = $linktree->vendor_id;
        $validated['linktree_id'] = $linktree->id;
        $validated['is_active'] = true;
        $validated['sort_order'] = $linktree->socials()->max('sort_order') + 1;

        LinktreeSocial::create($validated);

        return FlashMessage::backSuccess('Social media berhasil ditambahkan!');
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

        return FlashMessage::backSuccess('Social media berhasil diperbarui!');
    }

    /**
     * Delete a social media link.
     */
    public function destroySocial(Linktree $linktree, LinktreeSocial $social)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeSocialOwned($social, $linktree);

        $social->delete();

        return FlashMessage::backSuccess('Social media berhasil dihapus!');
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

        try {
            $file = $request->file('avatar');
            $filename = 'linktree_avatar_' . $linktree->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Ensure directory exists
            $dir = public_path('linktree/avatars');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $file->move($dir, $filename);

            // Delete old avatar if exists
            if ($linktree->avatar && file_exists(public_path('linktree/avatars/' . $linktree->avatar))) {
                unlink(public_path('linktree/avatars/' . $linktree->avatar));
            }

            $linktree->update(['avatar' => $filename]);

            return FlashMessage::backSuccess('Avatar berhasil diupload!');
        } catch (\Exception $e) {
            \Log::error('Gagal upload avatar: ' . $e->getMessage());
            return FlashMessage::backError('Gagal mengupload avatar. Pastikan file gambar valid dan coba lagi.');
        }
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

        try {
            $file = $request->file('banner');
            $filename = 'linktree_banner_' . $linktree->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Ensure directory exists
            $dir = public_path('linktree/banners');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $file->move($dir, $filename);

            // Delete old banner if exists
            if ($linktree->banner && file_exists(public_path('linktree/banners/' . $linktree->banner))) {
                unlink(public_path('linktree/banners/' . $linktree->banner));
            }

            $linktree->update(['banner' => $filename]);

            return FlashMessage::backSuccess('Banner berhasil diupload!');
        } catch (\Exception $e) {
            \Log::error('Gagal upload banner: ' . $e->getMessage());
            return FlashMessage::backError('Gagal mengupload banner. Pastikan file gambar valid dan coba lagi.');
        }
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

        try {
            $file = $request->file('qris_image');
            $filename = 'linktree_qris_' . $linktree->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Ensure directory exists
            $dir = public_path('linktree/qris');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $file->move($dir, $filename);

            // Delete old QRIS if exists
            if ($linktree->qris_image && file_exists(public_path('linktree/qris/' . $linktree->qris_image))) {
                unlink(public_path('linktree/qris/' . $linktree->qris_image));
            }

            $linktree->update(['qris_image' => $filename, 'show_qris' => true]);

            return FlashMessage::backSuccess('Gambar QRIS berhasil diupload!');
        } catch (\Exception $e) {
            \Log::error('Gagal upload QRIS: ' . $e->getMessage());
            return FlashMessage::backError('Gagal mengupload gambar QRIS. Pastikan file gambar valid dan coba lagi.');
        }
    }

    // =========================================================================
    // BULK LINK MANAGEMENT
    // =========================================================================

    /**
     * Export links as CSV file for download.
     */
    public function exportLinks(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $links = $linktree->links()->orderBy('sort_order')->get();

        $filename = 'linktree_' . $linktree->custom_url . '_links_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($links) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, ['Judul', 'URL', 'Ikon', 'Aktif', 'Urutan']);

            foreach ($links as $link) {
                fputcsv($file, [
                    $link->title,
                    $link->url,
                    $link->icon ?? '',
                    $link->is_active ? 'Ya' : 'Tidak',
                    $link->sort_order,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import links form.
     */
    public function importLinksForm(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $linkCount = $linktree->links()->count();

        return view('vendor.linktree.import', compact('linktree', 'linkCount'));
    }

    /**
     * Process imported links from CSV file.
     */
    public function importLinks(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // Max 5MB
            'import_mode' => 'required|in:append,replace',
        ]);

        $file = $request->file('csv_file');
        $importMode = $request->input('import_mode');

        try {
            $handle = fopen($file->getRealPath(), 'r');

            if ($handle === false) {
                return back()->withErrors(['csv_file' => 'Gagal membuka file CSV.']);
            }

            // Read header row
            $header = fgetcsv($handle);

            if ($header === false || count($header) < 2) {
                fclose($handle);
                return back()->withErrors(['csv_file' => 'File CSV tidak valid atau kosong.']);
            }

            // Normalize header (lowercase, trim)
            $header = array_map(fn($h) => strtolower(trim($h)), $header);

            // Map header to field names
            $fieldMap = [];
            $possibleMaps = [
                'judul' => 'title', 'title' => 'title', 'nama' => 'title', 'name' => 'title',
                'url' => 'url', 'link' => 'url', 'website' => 'url',
                'ikon' => 'icon', 'icon' => 'icon', 'emoji' => 'icon',
                'aktif' => 'is_active', 'active' => 'is_active', 'status' => 'is_active',
                'urutan' => 'sort_order', 'order' => 'sort_order', 'sort' => 'sort_order', 'posisi' => 'sort_order',
            ];

            foreach ($header as $index => $col) {
                if (isset($possibleMaps[$col])) {
                    $fieldMap[$possibleMaps[$col]] = $index;
                }
            }

            if (!isset($fieldMap['title']) || !isset($fieldMap['url'])) {
                fclose($handle);
                return back()->withErrors([
                    'csv_file' => 'File CSV harus memiliki kolom "Judul/Title" dan "URL/Link". Kolom yang ditemukan: ' . implode(', ', $header),
                ]);
            }

            $importedCount = 0;
            $skippedCount = 0;
            $errors = [];

            // If replace mode, delete existing links first
            if ($importMode === 'replace') {
                $linktree->links()->delete();
            }

            // Get next sort order
            $maxOrder = $linktree->links()->max('sort_order') ?? 0;

            $rowNumber = 1; // 1-indexed (header is row 0)
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;

                $title = trim($row[$fieldMap['title']] ?? '');
                $url = trim($row[$fieldMap['url']] ?? '');

                // Skip empty rows
                if (empty($title) && empty($url)) {
                    continue;
                }

                // Validate URL
                if (empty($url)) {
                    $errors[] = "Baris {$rowNumber}: URL kosong, dilewati.";
                    $skippedCount++;
                    continue;
                }

                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    // Try prepending https://
                    $urlWithProtocol = 'https://' . $url;
                    if (filter_var($urlWithProtocol, FILTER_VALIDATE_URL)) {
                        $url = $urlWithProtocol;
                    } else {
                        $errors[] = "Baris {$rowNumber}: URL tidak valid ({$url}), dilewati.";
                        $skippedCount++;
                        continue;
                    }
                }

                // Get optional fields
                $icon = isset($fieldMap['icon']) ? trim($row[$fieldMap['icon']] ?? '') : null;
                $isActive = true; // Default active
                if (isset($fieldMap['is_active'])) {
                    $activeVal = strtolower(trim($row[$fieldMap['is_active']] ?? 'ya'));
                    $isActive = in_array($activeVal, ['ya', 'yes', 'true', '1', 'aktif', 'on']);
                }
                $sortOrder = isset($fieldMap['sort_order']) ? (int) ($row[$fieldMap['sort_order']] ?? 0) : 0;

                if ($sortOrder <= 0) {
                    $maxOrder++;
                    $sortOrder = $maxOrder;
                }

                try {
                    LinktreeLink::create([
                        'linktree_id' => $linktree->id,
                        'title' => $title,
                        'url' => $url,
                        'icon' => $icon ?: null,
                        'is_active' => $isActive,
                        'sort_order' => $sortOrder,
                    ]);
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: Gagal menyimpan - {$e->getMessage()}";
                    $skippedCount++;
                }
            }

            fclose($handle);

            $message = "Import selesai! {$importedCount} link berhasil diimpor.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} baris dilewati.";
            }

            $flash = FlashMessage::success(redirect()->route('vendor.linktree.show', $linktree), $message);
            if (!empty($errors)) {
                $flash->with('errors_list', $errors);
            }

            return $flash;

        } catch (\Exception $e) {
            \Log::error("Linktree import error: {$e->getMessage()}");
            return back()->withErrors([
                'csv_file' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage(),
            ]);
        }
    }

    // =========================================================================
    // PRODUCT CATALOG MANAGEMENT
    // =========================================================================

    /**
     * Show product management page for a linktree.
     */
    public function manageProducts(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        // Load linktree products with produk relationship
        $linktree->load(['linktreeProducts' => function ($query) {
            $query->with('produk')->orderBy('sort_order');
        }]);

        // Get vendor's products that are NOT already added to this linktree
        $addedProdukIds = $linktree->linktreeProducts->pluck('produk_id')->toArray();
        $availableProduks = Produk::where('vendor_id', $linktree->vendor_id)
            ->whereNotIn('id', $addedProdukIds)
            ->orderBy('nama_produk')
            ->get();

        return view('vendor.linktree.products', compact('linktree', 'availableProduks'));
    }

    /**
     * Add a product to the linktree catalog.
     */
    public function addProduct(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $validated = $request->validate([
            'produk_id' => 'required|integer|exists:produks,id',
            'custom_price' => 'nullable|string|max:50',
            'custom_description' => 'nullable|string|max:1000',
        ]);

        // Check if product already exists in this linktree
        $exists = LinktreeProduct::where('linktree_id', $linktree->id)
            ->where('produk_id', $validated['produk_id'])
            ->exists();

        if ($exists) {
            return FlashMessage::backError('Produk ini sudah ditambahkan ke linktree.');
        }

        // Verify the product belongs to the same vendor
        $produk = Produk::find($validated['produk_id']);
        if ($produk->vendor_id !== $linktree->vendor_id) {
            abort(403, 'Produk ini bukan milik vendor Anda.');
        }

        // Get next sort order
        $maxOrder = LinktreeProduct::where('linktree_id', $linktree->id)->max('sort_order') ?? 0;

        LinktreeProduct::create([
            'linktree_id' => $linktree->id,
            'produk_id' => $validated['produk_id'],
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
            'custom_price' => $validated['custom_price'] ?? null,
            'custom_description' => $validated['custom_description'] ?? null,
        ]);

        return FlashMessage::success(redirect()->route('vendor.linktree.products', $linktree), 'Produk berhasil ditambahkan ke linktree!');
    }

    /**
     * Update a linktree product (custom price, description, active status).
     */
    public function updateProduct(Request $request, Linktree $linktree, LinktreeProduct $product)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeProductOwned($product, $linktree);

        $validated = $request->validate([
            'custom_price' => 'nullable|string|max:50',
            'custom_description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        $product->update($validated);

        return FlashMessage::success(redirect()->route('vendor.linktree.products', $linktree), 'Produk berhasil diperbarui!');
    }

    /**
     * Toggle active status of a linktree product.
     */
    public function toggleProduct(Linktree $linktree, LinktreeProduct $product)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeProductOwned($product, $linktree);

        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return FlashMessage::success(redirect()->route('vendor.linktree.products', $linktree), "Produk berhasil {$status}!");
    }

    /**
     * Remove a product from the linktree catalog.
     */
    public function removeProduct(Linktree $linktree, LinktreeProduct $product)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeProductOwned($product, $linktree);

        $product->delete();

        return FlashMessage::success(redirect()->route('vendor.linktree.products', $linktree), 'Produk berhasil dihapus dari linktree.');
    }

    /**
     * Reorder products in the linktree catalog via AJAX.
     */
    public function reorderProducts(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $request->validate([
            'product_order' => 'required|array',
            'product_order.*' => 'integer|exists:linktree_products,id',
        ]);

        foreach ($request->product_order as $index => $productId) {
            LinktreeProduct::where('id', $productId)
                ->where('linktree_id', $linktree->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan produk berhasil diperbarui.']);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Authorize that the current vendor owns this linktree.
     * Aborts with 403 if the linktree does not belong to the current vendor.
     */
    /**
     * Get the current vendor's first linktree.
     */
    private function getLinktree()
    {
        $vendor = $this->getVendor();

        return Linktree::where('vendor_id', $vendor->id)->first();
    }

    private function authorizeLinktree(Linktree $linktree = null): void
    {
        $linktree = $linktree ?? $this->getLinktree();

        if (!$linktree || !$this->isOwnedByCurrentVendor($linktree)) {
            abort(403, 'Akses ditolak: linktree ini bukan milik vendor Anda.');
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
     * Authorize that the linktree product belongs to the linktree.
     */
    private function authorizeProductOwned(LinktreeProduct $product, Linktree $linktree): void
    {
        if ($product->linktree_id !== $linktree->id) {
            abort(403, 'Produk ini bukan milik linktree ini.');
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

    // =========================================================================
    // ORDER MANAGEMENT
    // =========================================================================

    /**
     * List all orders for the vendor's linktree.
     */
    public function orders()
    {
        $this->authorizeLinktree();

        $orders = LinktreeOrder::where('vendor_id', Tenant::getVendorId())
            ->with(['produk', 'user'])
            ->latest()
            ->paginate(20);

        return view('vendor.linktree.orders', [
            'linktree' => $this->getLinktree(),
            'orders' => $orders,
        ]);
    }

    /**
     * Show detail of a specific order.
     */
    public function showOrder(string $uuid)
    {
        $this->authorizeLinktree();

        $order = LinktreeOrder::where('uuid', $uuid)
            ->where('vendor_id', Tenant::getVendorId())
            ->with(['produk', 'user', 'linktreeProduct'])
            ->firstOrFail();

        return view('vendor.linktree.order-detail', [
            'linktree' => $this->getLinktree(),
            'order' => $order,
        ]);
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(Request $request, string $uuid)
    {
        $this->authorizeLinktree();

        $request->validate([
            'status' => 'required|in:confirmed,processing,shipped,completed,cancelled',
            'vendor_notes' => 'nullable|string|max:1000',
        ]);

        $order = LinktreeOrder::where('uuid', $uuid)
            ->where('vendor_id', Tenant::getVendorId())
            ->firstOrFail();

        $order->update([
            'status' => $request->status,
            'vendor_notes' => $request->vendor_notes ?? $order->vendor_notes,
        ]);

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, string $uuid)
    {
        $this->authorizeLinktree();

        $request->validate([
            'payment_status' => 'required|in:unpaid,proof_sent,confirmed,rejected',
        ]);

        $order = LinktreeOrder::where('uuid', $uuid)
            ->where('vendor_id', Tenant::getVendorId())
            ->firstOrFail();

        $order->update([
            'payment_status' => $request->payment_status,
        ]);

        return back()->with('success', 'Status pembayaran diperbarui.');
    }
}
