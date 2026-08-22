<?php

namespace App\Http\Controllers\vendor\pos;

use App\Http\Responses\FlashMessage;

use App\Models\Vendor\Bahan;
use Illuminate\Http\Request;
use App\Models\Vendor\Produk;
use App\Http\Controllers\Controller;
use App\Models\Vendor\EstimasiProduk;
use App\Models\Vendor\WholesalePrice;
use App\Models\Vendor\SpesifikasiProduk;
use Illuminate\Support\Facades\Log;
use App\Http\Concerns\HasVendorContext;
use App\Services\PriceCalculationService;
use App\Services\StockService;



class PosController extends Controller
{
    use HasVendorContext;

    protected PriceCalculationService $priceCalcService;
    protected StockService $stockService;

    public function __construct(PriceCalculationService $priceCalcService, StockService $stockService)
    {
        $this->priceCalcService = $priceCalcService;
        $this->stockService = $stockService;
    }

    // Menampilkan halaman pos
    public function index()
    {
        try {
            // Dapatkan vendor dari user yang sedang login
            $vendor = $this->requireVendor();

            $products = Produk::with([
                'vendor',
                'kategori',
                'spesifikasiProduk.spesifikasi',
                'spesifikasiProduk.bahans.wholesalePrice',
                'estimasiProduk'
            ])->where('vendor_id', $vendor->id)
                ->get();

            $categories = $products->pluck('kategori')->unique();

            // Get stock alerts for banner display
            $stockAlerts = $this->stockService->getUnreadAlerts($vendor->id);
            $criticalBahan = $stockAlerts->where('type', 'out_of_stock');

            return view('pos.pos-home', compact('products', 'categories', 'stockAlerts', 'criticalBahan'));
        } catch (\Exception $e) {
            Log::error('Error in POS index: ' . $e->getMessage());
            return FlashMessage::backError('Failed to load POS: ' . $e->getMessage());
        }
    }

    // Menampilkan halaman kategori
    public function category($slug)
    {
        try {
            // Dapatkan vendor dari user yang sedang login
            $vendor = $this->requireVendor();

            $products = Produk::whereHas('kategori', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })->with([
                'vendor',
                'kategori',
                'spesifikasiProduk.bahans.wholesalePrice',
                'estimasiProduk'
            ])->where('vendor_id', $vendor->id)
                ->get();

            $categories = Produk::with('kategori')
                ->where('vendor_id', $vendor->id)
                ->get()
                ->pluck('kategori')
                ->unique();

            return view('pos.pos-home', compact('products', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error in POS category: ' . $e->getMessage());
            return FlashMessage::error(redirect()->route('vendor.pos.index'), 'Failed to load category: ' . $e->getMessage());
        }
    }

    // Menampilkan halaman pencarian
    public function search(Request $request)
    {
        try {
            // Dapatkan vendor dari user yang sedang login
            $vendor = $this->requireVendor();
            $search = $request->get('search');

            $products = Produk::where('vendor_id', $vendor->id)
                ->where(function ($query) use ($search) {
                    $query->where('nama_produk', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                })
                ->with([
                    'vendor',
                    'kategori',
                    'spesifikasiProduk.bahans.wholesalePrice',
                    'estimasiProduk'
                ])->get();

            $categories = Produk::with('kategori')
                ->where('vendor_id', $vendor->id)
                ->get()
                ->pluck('kategori')
                ->unique();

            return view('pos.pos-home', compact('products', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error in POS search: ' . $e->getMessage());
            return FlashMessage::error(redirect()->route('vendor.pos.index'), 'Search failed: ' . $e->getMessage());
        }
    }

    // Fungsi untuk menambahkan produk ke keranjang
    public function addToCart(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'product_id' => 'required|exists:produks,id',
                'quantity' => 'required|integer|min:1',
                'specifications' => 'required|array',
            ]);

            // Dapatkan vendor dari user yang sedang login
            $vendor = $this->requireVendor();
            $product = Produk::with('spesifikasiProduk')
                ->where('vendor_id', $vendor->id)
                ->findOrFail($request->product_id);

            // Check if required specifications are provided
            foreach ($product->spesifikasiProduk as $spec) {
                if ($spec->wajib_diisi && (!isset($request->specifications[$spec->id]) || $request->specifications[$spec->id] === '')) {
                    return FlashMessage::backError('Please fill in all required specifications');
                }
            }

            $quantity = $request->quantity;
            $specifications = $request->specifications;

            if (!$specifications) {
                return FlashMessage::backError('Please select product specifications');
            }

            // Bug 5 Fix: Validasi stok bahan sebelum menghitung harga
            foreach ($specifications as $specId => $value) {
                $spesifikasiProduk = SpesifikasiProduk::with(['spesifikasi', 'bahans'])
                    ->find($specId);

                if (!$spesifikasiProduk) {
                    continue;
                }

                if ($spesifikasiProduk->spesifikasi->tipe_input === 'select') {
                    $bahan = Bahan::find($value);
                    if ($bahan && $bahan->stok !== null && $bahan->stok < $quantity) {
                        return FlashMessage::backError("Stok bahan \"{$bahan->nama_bahan}\" tidak mencukupi (tersedia: {$bahan->stok}, dibutuhkan: {$quantity})");
                    }
                } else {
                    $inputValue = (float) $value;
                    $bahan = $spesifikasiProduk->bahans->first();
                    $requiredStock = $inputValue * $quantity;
                    if ($bahan && $bahan->stok !== null && $bahan->stok < $requiredStock) {
                        return FlashMessage::backError("Stok bahan \"{$bahan->nama_bahan}\" tidak mencukupi (tersedia: {$bahan->stok}, dibutuhkan: {$requiredStock})");
                    }
                }
            }

            // Gunakan PriceCalculationService untuk menghitung harga
            $result = $this->priceCalcService->calculateItemTotal($specifications, $quantity);

            $cartItem = [
                'product_id' => $product->id,
                'product_name' => $product->nama_produk,
                'quantity' => $quantity,
                'specifications' => $result['specifications'],
                'total_price' => $result['total_price'],
                'estimated_time' => EstimasiProduk::where('produk_id', $product->id)
                    ->first()?->calculateTotalProductionTime($quantity) ?? 0
            ];

            $cart = session()->get('cart', []);
            $cart[] = $cartItem;
            session()->put('cart', $cart);

            return FlashMessage::success(redirect()->route('vendor.pos.cart'), 'Product added to cart successfully');
        } catch (\Exception $e) {
            Log::error('Error adding to cart: ' . $e->getMessage());
            return FlashMessage::backError('Failed to add product to cart: ' . $e->getMessage());
        }
    }

    // Fungsi untuk menampilkan keranjang
    public function cart()
    {
        try {
            // Dapatkan vendor dari user yang sedang login
            $vendor = $this->requireVendor();
            $cartItems = session('cart', []);
            $products = Produk::where('vendor_id', $vendor->id)->get();

            // Recalculate harga menggunakan PriceCalculationService
            foreach ($cartItems as &$item) {
                $quantity = $item['quantity'];
                $specIds = array_keys($item['specifications']);

                // Build specifications array dari data yang tersimpan
                $specifications = [];
                foreach ($item['specifications'] as $specId => $spec) {
                    $specifications[$specId] = $spec['value'];
                }

                $result = $this->priceCalcService->calculateItemTotal($specifications, $quantity);

                // Update harga per spesifikasi
                foreach ($result['specifications'] as $specId => $specData) {
                    if (isset($item['specifications'][$specId])) {
                        $item['specifications'][$specId]['price'] = $specData['price'];
                    }
                }

                $item['total_price'] = $result['total_price'];
            }

            return view('pos.cart', compact('cartItems', 'products'));
        } catch (\Exception $e) {
            Log::error('Error in cart view: ' . $e->getMessage());
            return FlashMessage::error(redirect()->route('vendor.pos.index'), 'Failed to load cart: ' . $e->getMessage());
        }
    }

    // Fungsi untuk menghapus item dari keranjang
    public function removeItem($index)
    {
        try {
            $cart = session()->get('cart', []);

            if (isset($cart[$index])) {
                unset($cart[$index]);
                $cart = array_values($cart); // Reindex array
                session()->put('cart', $cart);
            }

            return FlashMessage::success(redirect()->route('vendor.pos.cart'), 'Item removed successfully');
        } catch (\Exception $e) {
            Log::error('Error removing item: ' . $e->getMessage());
            return FlashMessage::error(redirect()->route('vendor.pos.cart'), 'Failed to remove item: ' . $e->getMessage());
        }
    }

    // Fungsi untuk menghapus semua item dari keranjang
    public function clearCart()
    {
        try {
            session()->forget('cart');
            return FlashMessage::success(redirect()->route('vendor.pos.cart'), 'Cart cleared successfully');
        } catch (\Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage());
            return FlashMessage::error(redirect()->route('vendor.pos.cart'), 'Failed to clear cart: ' . $e->getMessage());
        }
    }

    // Fungsi untuk memeriksa harga
    public function checkPrice(Request $request)
    {
        try {
            // Dapatkan vendor dari user yang sedang login
            $vendor = $this->requireVendor();
            $product = Produk::where('vendor_id', $vendor->id)
                ->findOrFail($request->product_id);

            $quantity = $request->quantity ?? 1;
            $specifications = $request->specifications ?? [];

            // Gunakan PriceCalculationService untuk menghitung harga
            $result = $this->priceCalcService->calculateItemTotal($specifications, $quantity);

            // Format specification details untuk response JSON
            $specificationDetails = [];
            foreach ($result['specifications'] as $specId => $specData) {
                $spesifikasiProduk = SpesifikasiProduk::with('spesifikasi')->find($specId);
                $namaSpesifikasi = $spesifikasiProduk->spesifikasi->nama_spesifikasi ?? 'Unknown';

                $valueLabel = $specData['value'];
                if ($specData['input_type'] === 'number' && $spesifikasiProduk) {
                    $valueLabel = $specData['value'] . ' ' . ($spesifikasiProduk->spesifikasi->satuan ?? '');
                } elseif ($specData['input_type'] === 'select') {
                    $bahan = Bahan::find($specData['bahan_id']);
                    $valueLabel = $bahan->nama_bahan ?? $specData['value'];
                }

                $specificationDetails[] = [
                    'name' => $namaSpesifikasi,
                    'value' => $valueLabel,
                    'price' => number_format($specData['price'], 0, ',', '.'),
                ];
            }

            return response()->json([
                'quantity' => $quantity,
                'specifications' => $specificationDetails,
                'totalPrice' => number_format($result['total_price'], 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking price: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to calculate price: ' . $e->getMessage()
            ], 500);
        }
    }
}
