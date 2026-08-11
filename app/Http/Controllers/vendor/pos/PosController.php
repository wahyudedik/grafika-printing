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



class PosController extends Controller
{
    use HasVendorContext;


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
            return view('pos.pos-home', compact('products', 'categories'));
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

            $specDetails = [];
            $totalSpecPrice = 0;
            $wholesalePrice = new WholesalePrice();

            foreach ($specifications as $specId => $value) {
                $spesifikasiProduk = SpesifikasiProduk::with(['spesifikasi', 'bahans'])
                    ->find($specId);

                if (!$spesifikasiProduk) {
                    continue;
                }

                if ($spesifikasiProduk->spesifikasi->tipe_input === 'select') {
                    $bahan = Bahan::with('wholesalePrice')->find($value);
                    if ($bahan) {
                        $finalPrice = $wholesalePrice->calculateFinalPrice((float) $bahan->hpp, $quantity, $bahan->id);
                        $specPrice = $finalPrice * $quantity;

                        $specDetails[$specId] = [
                            'value' => $value,
                            'bahan_id' => $bahan->id,
                            'input_type' => 'select',
                            'price' => $specPrice,
                            'nama_spesifikasi' => $spesifikasiProduk->spesifikasi->nama_spesifikasi
                        ];
                    }
                } else {
                    $inputValue = (float)$value;
                    $bahan = $spesifikasiProduk->bahans->first();
                    if ($bahan) {
                        $pricePerUnit = $wholesalePrice->calculateFinalPrice((float) $bahan->hpp, $inputValue, $bahan->id);
                        $specPrice = $pricePerUnit * $inputValue * $quantity;

                        $specDetails[$specId] = [
                            'value' => $inputValue,
                            'bahan_id' => $bahan->id,
                            'input_type' => 'number',
                            'price' => $specPrice,
                            'nama_spesifikasi' => $spesifikasiProduk->spesifikasi->nama_spesifikasi
                        ];
                    }
                }
                $totalSpecPrice += $specPrice;
            }

            $cartItem = [
                'product_id' => $product->id,
                'product_name' => $product->nama_produk,
                'quantity' => $quantity,
                'specifications' => $specDetails,
                'total_price' => $totalSpecPrice,
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

            foreach ($cartItems as &$item) {
                $totalPrice = 0;
                $quantity = $item['quantity'];

                foreach ($item['specifications'] as $specId => $spec) {
                    $spesifikasiProduk = SpesifikasiProduk::with(['spesifikasi', 'bahans.wholesalePrice'])->find($specId);
                    $bahan = Bahan::find($spec['bahan_id']);
                    $wholesalePrice = new WholesalePrice();

                    if ($spec['input_type'] === 'select' && $bahan) {
                        $finalPrice = $wholesalePrice->calculateFinalPrice((float) $bahan->hpp, $quantity, $bahan->id);
                        $specPrice = $finalPrice * $quantity;
                    } elseif ($bahan) {
                        $inputValue = (float)$spec['value'];
                        $pricePerUnit = $wholesalePrice->calculateFinalPrice((float) $bahan->hpp, $inputValue, $bahan->id);
                        $specPrice = $pricePerUnit * $inputValue * $quantity;
                    } else {
                        $specPrice = $spec['price'] ?? 0;
                    }

                    $totalPrice += $specPrice;
                    $item['specifications'][$specId]['price'] = $specPrice;
                }

                $item['total_price'] = $totalPrice;
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
            $total = 0;
            $specificationDetails = [];

            foreach ($specifications as $specId => $value) {
                $spesifikasiProduk = SpesifikasiProduk::with(['spesifikasi', 'bahans.wholesalePrice'])->find($specId);

                if (!$spesifikasiProduk) {
                    continue;
                }

                $wholesalePrice = new WholesalePrice();

                if ($spesifikasiProduk->spesifikasi->tipe_input === 'select') {
                    $bahan = Bahan::with('wholesalePrice')->find($value);
                    if ($bahan) {
                        $finalPrice = $wholesalePrice->calculateFinalPrice((float) $bahan->hpp, $quantity, $bahan->id);
                        $total += $finalPrice * $quantity;

                        $specificationDetails[] = [
                            'name' => $spesifikasiProduk->spesifikasi->nama_spesifikasi,
                            'value' => $bahan->nama_bahan,
                            'price' => number_format($finalPrice * $quantity, 0, ',', '.')
                        ];
                    }
                } elseif ($spesifikasiProduk->spesifikasi->tipe_input === 'number') {
                    $inputValue = (float)$value;
                    $bahan = $spesifikasiProduk->bahans->first();
                    if ($bahan) {
                        $pricePerUnit = $wholesalePrice->calculateFinalPrice((float) $bahan->hpp, $inputValue, $bahan->id);
                        $materialCost = $pricePerUnit * $inputValue; // Multiply by input value
                        $total += $materialCost * $quantity;

                        $specificationDetails[] = [
                            'name' => $spesifikasiProduk->spesifikasi->nama_spesifikasi,
                            'value' => $inputValue . ' ' . $spesifikasiProduk->spesifikasi->satuan,
                            'price' => number_format($materialCost * $quantity, 0, ',', '.')
                        ];
                    }
                }
            }

            return response()->json([
                'quantity' => $quantity,
                'specifications' => $specificationDetails,
                'totalPrice' => number_format($total, 0, ',', '.')
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking price: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to calculate price: ' . $e->getMessage()
            ], 500);
        }
    }
}
