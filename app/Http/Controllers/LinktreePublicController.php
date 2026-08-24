<?php

namespace App\Http\Controllers;

use App\Models\Vendor\Linktree;
use App\Models\Vendor\LinktreeOrder;
use App\Models\Vendor\LinktreeProduct;
use App\Models\LinktreeAbTest;
use App\Models\LinktreeAbTestResult;
use App\Models\ServiceConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LinktreePublicController extends Controller
{
    /**
     * Display the public linktree page.
     * Supports A/B testing: if a running test exists, randomly show a variant template.
     * Detects Xendit active status to show appropriate payment method.
     */
    public function show(Request $request, string $customUrl)
    {
        $linktree = Linktree::where('custom_url', $customUrl)
            ->where('is_active', true)
            ->with(['activeLinks', 'activeSocials'])
            ->with(['activeLinktreeProducts' => function ($query) {
                $query->with(['produk' => function($q) {
                    $q->with(['spesifikasiProduk' => function($q2) {
                        $q2->with(['spesifikasi', 'bahanSpesifikasiProduk']);
                    }, 'kategori']);
                }]);
            }])
            ->first();

        if (!$linktree) {
            abort(404, 'Linktree tidak ditemukan atau belum aktif.');
        }

        // Check for running A/B test
        $abTest = $linktree->abTests()->where('status', 'running')->first();
        $activeVariant = null;

        if ($abTest) {
            // Get or create visitor ID from cookie
            $visitorId = $request->cookie('lt_vid_' . $linktree->id);

            if (!$visitorId) {
                $visitorId = md5(uniqid(mt_rand(), true));
            }

            // Determine which variant to show
            $activeVariant = $abTest->getVariantForVisitor($visitorId);

            // Record this impression (only once per visitor per test)
            $existingResult = LinktreeAbTestResult::where('ab_test_id', $abTest->id)
                ->where('visitor_id', $visitorId)
                ->first();

            if (!$existingResult) {
                LinktreeAbTestResult::create([
                    'ab_test_id' => $abTest->id,
                    'variant' => $activeVariant,
                    'visitor_id' => $visitorId,
                    'is_click' => false,
                    'shown_at' => now(),
                ]);
            }
        }

        // Increment views count
        $linktree->incrementViews();

        // Get template classes - use A/B test variant if active
        if ($abTest && $activeVariant) {
            $variantTemplate = $activeVariant === 'variant_a' ? $abTest->variant_a : $abTest->variant_b;
            // Create a temporary template classes from the variant
            $templateClasses = $this->getVariantTemplateClasses($variantTemplate);
        } else {
            $templateClasses = $linktree->getTemplateClasses();
        }

        // Get vendor info
        $vendor = $linktree->vendor;

        // Detect Xendit active status
        $xenditActive = $this->isXenditActive();

        // Get vendor bank account for manual transfer fallback
        $bankAccount = null;
        if ($vendor) {
            $bankAccount = $vendor->getPrimaryBankAccount();
        }

        // QRIS available only if Xendit is active and linktree has QRIS image
        $qrisAvailable = $xenditActive && !empty($linktree->qris_image);

        // Check if product catalog has products
        $hasProducts = $linktree->activeLinktreeProducts->count() > 0;

        $view = view('linktree.public', compact(
            'linktree',
            'templateClasses',
            'vendor',
            'xenditActive',
            'bankAccount',
            'qrisAvailable',
            'hasProducts'
        ));

        // Set visitor cookie for A/B test consistency
        if ($abTest && $activeVariant) {
            $cookieName = 'lt_vid_' . $linktree->id;
            return response()->view('linktree.public', compact(
                'linktree',
                'templateClasses',
                'vendor',
                'xenditActive',
                'bankAccount',
                'qrisAvailable',
                'hasProducts'
            ))->cookie($cookieName, $visitorId, 30 * 24 * 60); // 30 days
        }

        return $view;
    }

    /**
     * Track link click and redirect.
     * Also records A/B test click if applicable.
     */
    public function trackClick(Request $request, string $customUrl, int $linkId)
    {
        $linktree = Linktree::where('custom_url', $customUrl)
            ->where('is_active', true)
            ->first();

        if (!$linktree) {
            abort(404);
        }

        $link = $linktree->links()->where('id', $linkId)->where('is_active', true)->first();

        if (!$link) {
            abort(404);
        }

        // Increment click counters
        $link->incrementClicks();
        $linktree->incrementClicks();

        // Record A/B test click if applicable
        $abTest = $linktree->abTests()->where('status', 'running')->first();
        if ($abTest) {
            $visitorId = $request->cookie('lt_vid_' . $linktree->id);
            if ($visitorId) {
                LinktreeAbTestResult::where('ab_test_id', $abTest->id)
                    ->where('visitor_id', $visitorId)
                    ->update(['is_click' => true]);
            }
        }

        return redirect()->away($link->url);
    }

    /**
     * Generate template classes for an A/B test variant.
     */
    private function getVariantTemplateClasses(string $template): array
    {
        $configs = [
            'minimal' => [
                'bg' => 'bg-white',
                'text' => 'text-gray-900',
                'primary' => 'bg-gray-700',
                'primary_text' => 'text-white',
                'secondary' => 'bg-gray-100',
                'border' => 'border-gray-200',
                'card' => 'bg-white border-gray-200',
            ],
            'colorful' => [
                'bg' => 'bg-violet-50',
                'text' => 'text-gray-900',
                'primary' => 'bg-violet-600',
                'primary_text' => 'text-white',
                'secondary' => 'bg-pink-100',
                'border' => 'border-violet-200',
                'card' => 'bg-white border-violet-200',
            ],
            'dark' => [
                'bg' => 'bg-gray-900',
                'text' => 'text-gray-100',
                'primary' => 'bg-indigo-600',
                'primary_text' => 'text-white',
                'secondary' => 'bg-gray-800',
                'border' => 'border-gray-700',
                'card' => 'bg-gray-800 border-gray-700',
            ],
            'professional' => [
                'bg' => 'bg-slate-50',
                'text' => 'text-slate-900',
                'primary' => 'bg-blue-800',
                'primary_text' => 'text-white',
                'secondary' => 'bg-blue-50',
                'border' => 'border-slate-200',
                'card' => 'bg-white border-slate-200',
            ],
        ];

        return $configs[$template] ?? $configs['minimal'];
    }

    /**
     * Check if Xendit payment gateway is active.
     * Checks ServiceConfig first, then falls back to env config.
     */
    private function isXenditActive(): bool
    {
        try {
            // Check if Xendit is enabled in ServiceConfig
            $xenditEnabled = ServiceConfig::getValue('xendit', 'enabled', null);
            if ($xenditEnabled !== null) {
                return filter_var($xenditEnabled, FILTER_VALIDATE_BOOLEAN);
            }

            // Fallback: check if Xendit API key is configured in env
            $apiKey = config('services.xendit.api_key');
            return !empty($apiKey) && $apiKey !== 'your-xendit-api-key';
        } catch (\Exception $e) {
            Log::warning('Failed to check Xendit status: ' . $e->getMessage());
            // Fallback to env check
            $apiKey = config('services.xendit.api_key');
            return !empty($apiKey) && $apiKey !== 'your-xendit-api-key';
        }
    }

    /**
     * Show product detail for modal (JSON API).
     */
    public function showProduct(string $customUrl, string $linktreeProduct)
    {
        $linktree = Linktree::where('custom_url', $customUrl)->first();
        if (!$linktree) {
            abort(404, 'Linktree tidak ditemukan.');
        }

        $product = LinktreeProduct::where('id', $linktreeProduct)
            ->where('linktree_id', $linktree->id)
            ->first();

        if (!$product) {
            abort(404, 'Produk tidak ditemukan.');
        }

        // Eager load full specs
        $product->load(['produk' => function($q) {
            $q->with(['spesifikasiProduk' => function($q2) {
                $q2->with(['spesifikasi', 'bahanSpesifikasiProduk']);
            }, 'kategori']);
        }]);

        return response()->json([
            'product' => $product,
            'specs' => $product->full_specs,
            'bahans' => $product->bahans_list,
        ]);
    }

    /**
     * Store a new order from linktree product.
     */
    public function storeOrder(Request $request, string $customUrl, string $linktreeProduct)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'selected_specs' => 'required|array|min:1',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $linktree = Linktree::where('custom_url', $customUrl)->first();
        if (!$linktree) {
            return back()->withErrors(['error' => 'Linktree tidak ditemukan.']);
        }

        $product = LinktreeProduct::where('id', $linktreeProduct)
            ->where('linktree_id', $linktree->id)
            ->first();

        if (!$product || !$product->is_active) {
            return back()->withErrors(['error' => 'Produk tidak tersedia.']);
        }

        // Validasi harga: pastikan harga yang dikirim customer sesuai dengan harga aktual produk
        if ($request->has('unit_price') && $request->unit_price !== null) {
            $product->load('produk');
            $expectedPrice = $product->produk->harga ?? $product->harga ?? 0;
            if ($expectedPrice > 0 && abs((float) $request->unit_price - (float) $expectedPrice) > 1) {
                return back()->withErrors(['price' => 'Harga tidak sesuai dengan harga produk saat ini.']);
            }
        }

        $order = new LinktreeOrder();
        $order->vendor_id = $linktree->vendor_id;
        $order->fill([
            'linktree_id' => $linktree->id,
            'linktree_product_id' => $product->id,
            'produk_id' => $product->produk_id,
            'user_id' => auth()->id(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'selected_specs' => $request->selected_specs,
            'quantity' => $request->quantity,
            'notes' => $request->notes,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $order->save();

        // Generate WhatsApp message
        $order->whatsapp_message = $order->generateWhatsAppMessage();
        $order->save();

        return redirect()->route('linktree.order.success', [
            'customUrl' => $linktree->custom_url,
            'uuid' => $order->uuid,
        ]);
    }

    /**
     * Order success page with WhatsApp button.
     */
    public function orderSuccess(string $customUrl, string $uuid)
    {
        $linktree = Linktree::where('custom_url', $customUrl)->first();
        if (!$linktree) {
            abort(404, 'Linktree tidak ditemukan.');
        }

        $order = LinktreeOrder::where('uuid', $uuid)
            ->where('linktree_id', $linktree->id)
            ->with(['produk', 'linktreeProduct'])
            ->firstOrFail();

        return view('linktree.order-success', [
            'linktree' => $linktree,
            'order' => $order,
            'whatsappUrl' => $order->getWhatsAppUrl(),
        ]);
    }
}
