# Saran Perbaikan Arsitektur — Grafika-Printing

> Dokumen ini berisi rekomendasi arsitektur berdasarkan hasil **Comprehensive Audit III** (7 Agustus 2026) dan **Audit Lanjutan** (11 Agustus 2026).
> Setiap rekomendasi dilengkapi dengan alasan, implementasi, dan prioritas.

---

## Ringkasan

| # | Rekomendasi | Prioritas | Estimasi | File Terdampak |
|---|-------------|-----------|----------|----------------|
| 1 | Extract Confirm Dialog Helper | ✅ DONE | - | - |
| 2 | Middleware Authorization Check | 🟡 PENTING | ~10 file | Controllers |
| 3 | Request Validation Classes | 🟡 PENTING | ~15 file | Requests |
| 4 | API Response Standardization | 🟢 NORMAL | ~12 file | Controllers |
| 5 | Rate Limiting | 🟡 PENTING | ~5 file | Routes, Providers |
| 6 | Activity Log Enhancement | 🟢 NORMAL | ~10 file | Controllers |
| 7 | Vendor Context Trait | 🔴 KRITIS | ~20 file | Controllers |
| 8 | Flash Message Standardization | 🟡 PENTING | ~25 file | Controllers, Views |
| 9 | Controller Refactoring (Fat → Thin) | 🟡 PENTING | ~8 file | Controllers, Actions |
| 10 | Centralized File Upload Service | 🟡 PENTING | ~10 file | Services, Controllers |
| 11 | Laravel Policies & Gates | 🟡 PENTING | ~15 file | Policies, Controllers |
| 12 | Soft Deletes & Model Conventions | 🟢 NORMAL | ~20 file | Models, Migrations |

---

## 1. ✅ Extract Confirm Dialog Helper — DONE

**Status:** Sudah diimplementasi di Comprehensive Audit II & III.

### Yang Sudah Ada
- `confirmDelete(formId)` — global function di `resources/views/components/alert.blade.php`
- `confirmAction(options)` — generic confirmation dialog
- `confirmFormSubmit(formId, options)` — form submission confirmation
- `safeSwalFire(options)` — safe wrapper untuk SweetAlert2

### Rekomendasi Lanjutan
Pindahkan helper functions ke file terpisah untuk better code organization:

```javascript
// resources/js/confirm.js
import Swal from 'sweetalert2';

export function confirmDelete(formId, options = {}) {
    const defaults = {
        title: 'Hapus Data?',
        text: 'Data yang sudah dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
    };
    const config = { ...defaults, ...options };
    
    Swal.fire(config).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId)?.submit();
        }
    });
}

export function confirmAction(options = {}) {
    return Swal.fire(options);
}

export function showToast(message, type = 'success') {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type,
        title: message,
        showConfirmButton: false,
        timer: 3000,
    });
}
```

**Prioritas:** 🟢 RENDAH (sudah berfungsi, optional refactor)

---

## 2. Middleware Authorization Check

### Masalah
Beberapa controller tidak melakukan authorization check yang ketat. User mungkin bisa mengakses data vendor lain melalui direct URL manipulation.

### Contoh yang Perlu Diperbaiki

#### 2.1 Vendor Controller Authorization
```php
// SEBELUM (tidak ada check)
public function show(Produk $produk)
{
    return view('produk.show', compact('produk'));
}

// SESUDAH (dengan authorization)
public function show(Produk $produk)
{
    // Pastikan produk milik vendor yang sedang login
    if ($produk->vendor_id !== Tenant::getVendorId()) {
        abort(403, 'Unauthorized');
    }
    return view('produk.show', compact('produk'));
}
```

#### 2.2 User Controller Authorization
```php
// SEBELUM
public function edit(string $id)
{
    $user = User::findOrFail($id);
    return view('pengguna.edit', compact('user'));
}

// SESUDAH
public function edit(string $id)
{
    $user = User::findOrFail($id);
    
    // Admin bisa edit semua, vendor hanya bisa edit user miliknya
    if (auth()->user()->usertype === 'vendor') {
        $hasAccess = DB::table('vendor_user')
            ->where('user_id', $user->id)
            ->where('vendor_id', Tenant::getVendorId())
            ->exists();
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized');
        }
    }
    
    return view('pengguna.edit', compact('user'));
}
```

#### 2.3 Buat Authorization Service
```php
// app/Services/AuthorizationService.php
namespace App\Services;

use App\Facades\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthorizationService
{
    /**
     * Check if current user can access vendor data
     */
    public function canAccessVendorData(int $vendorId): bool
    {
        $user = Auth::user();
        
        // Admin/Dev have full access
        if (in_array($user->usertype, ['admin', 'dev'])) {
            return true;
        }
        
        // Vendor can only access own data
        if ($user->usertype === 'vendor') {
            return Tenant::getVendorId() === $vendorId;
        }
        
        return false;
    }
    
    /**
     * Check if current user can access user data
     */
    public function canAccessUserData(int $userId): bool
    {
        $user = Auth::user();
        
        // Admin/Dev have full access
        if (in_array($user->usertype, ['admin', 'dev'])) {
            return true;
        }
        
        // User can only access own data
        if ($user->usertype === 'user') {
            return $user->id === $userId;
        }
        
        // Vendor can access users linked to their vendor
        if ($user->usertype === 'vendor') {
            return DB::table('vendor_user')
                ->where('user_id', $userId)
                ->where('vendor_id', Tenant::getVendorId())
                ->exists();
        }
        
        return false;
    }
    
    /**
     * Enforce authorization or abort
     */
    public function authorizeVendor(int $vendorId): void
    {
        if (!$this->canAccessVendorData($vendorId)) {
            abort(403, 'Unauthorized access to vendor data');
        }
    }
    
    public function authorizeUser(int $userId): void
    {
        if (!$this->canAccessUserData($userId)) {
            abort(403, 'Unauthorized access to user data');
        }
    }
}
```

#### 2.4 Register Service di Provider
```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->singleton(AuthorizationService::class);
}
```

#### 2.5 Gunakan di Controller
```php
// app/Http/Controllers/vendor/ProdukController.php
use App\Services\AuthorizationService;

class ProdukController extends Controller
{
    public function __construct(
        protected AuthorizationService $authService
    ) {}
    
    public function show(Produk $produk)
    {
        $this->authService->authorizeVendor($produk->vendor_id);
        return view('produk.show', compact('produk'));
    }
}
```

### File yang Perlu Diubah
- `app/Http/Controllers/vendor/ProdukController.php`
- `app/Http/Controllers/vendor/TransaksiController.php`
- `app/Http/Controllers/vendor/PenggunaController.php`
- `app/Http/Controllers/vendor/SpesifikasiController.php`
- `app/Http/Controllers/vendor/KategoriProdukController.php`
- `app/Http/Controllers/vendor/PelangganController.php`
- `app/Http/Controllers/vendor/LinktreeController.php`
- `app/Http/Controllers/AuctionController.php`
- `app/Http/Controllers/UserDashboardController.php`
- `app/Services/AuthorizationService.php` (BARU)

**Prioritas:** 🟡 PENTING — Security improvement
**Estimasi:** ~10 file

---

## 3. Request Validation Classes

### Masalah
Banyak controller melakukan inline validation di dalam method. Ini sulit di-maintain dan tidak reusable.

### Implementasi

#### 3.1 Buat Base Request
```php
// app/Http/Requests/BaseRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    // Common rules bisa ditambah di sini
}
```

#### 3.2 Contoh Request Classes

**StorePenggunaRequest:**
```php
// app/Http/Requests/StorePenggunaRequest.php
namespace App\Http\Requests;

class StorePenggunaRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'usertype' => 'required|in:user,vendor',
            'phone' => 'nullable|string|max:20',
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'usertype.required' => 'Tipe user wajib dipilih',
            'usertype.in' => 'Tipe user tidak valid',
        ];
    }
}
```

**UpdatePenggunaRequest:**
```php
// app/Http/Requests/UpdatePenggunaRequest.php
namespace App\Http\Requests;

class UpdatePenggunaRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->route('user')?->id,
            'password' => 'nullable|min:8|confirmed',
            'usertype' => 'required|in:user,vendor',
            'phone' => 'nullable|string|max:20',
        ];
    }
}
```

**StoreProdukRequest:**
```php
// app/Http/Requests/StoreProdukRequest.php
namespace App\Http\Requests;

class StoreProdukRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'kategori_id' => 'required|exists:kategori_produks,id',
            'gambar.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
```

**StoreManualTransferRequest:**
```php
// app/Http/Requests/StoreManualTransferRequest.php
namespace App\Http\Requests;

class StoreManualTransferRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:transaksis,id',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
            'transfer_amount' => 'required|numeric|min:1',
            'transfer_date' => 'required|date|before_or_equal:today',
            'proof_file' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ];
    }
}
```

**StoreAuctionRequest:**
```php
// app/Http/Requests/StoreAuctionRequest.php
namespace App\Http\Requests;

class StoreAuctionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:1000',
            'deadline' => 'required|date|after:today',
            'category' => 'required|string|max:100',
            'specs' => 'nullable|array',
            'specs.*' => 'string|max:255',
        ];
    }
}
```

#### 3.3 Gunakan di Controller
```php
// SEBELUM
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        // ... lainnya
    ]);
    
    // ... logic
}

// SESUDAH
public function store(StorePenggunaRequest $request)
{
    $validated = $request->validated();
    
    // ... logic tanpa inline validation
}
```

### File yang Perlu Diubah
- `app/Http/Controllers/vendor/PenggunaController.php`
- `app/Http/Controllers/vendor/ProdukController.php`
- `app/Http/Controllers/vendor/TransaksiController.php`
- `app/Http/Controllers/vendor/LinktreeController.php`
- `app/Http/Controllers/AuctionController.php`
- `app/Http/Controllers/UserController.php`
- `app/Http/Controllers/ManualTransferController.php`
- `app/Http/Requests/` (12+ files baru)

**Prioritas:** 🟡 PENTING — Code maintainability
**Estimasi:** ~15 file (5 existing + 10+ baru)

---

## 4. API Response Standardization

### Masalah
Response format tidak konsisten di berbagai controller. Beberapa mengembalikan JSON, beberapa redirect, beberapa view.

### Standard Format
```json
{
    "success": true,
    "message": "Data berhasil disimpan",
    "data": {
        "id": 1,
        "name": "John Doe"
    },
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

### Error Format
```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "name": ["Nama wajib diisi"],
        "email": ["Email sudah terdaftar"]
    }
}
```

### Buat Response Helper
```php
// app/Http/Responses/ApiResponse.php
namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
    
    public static function error(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];
        
        if ($errors) {
            $response['errors'] = $errors;
        }
        
        return response()->json($response, $code);
    }
    
    public static function paginated($paginator, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
    
    public static function created($data = null, string $message = 'Data berhasil dibuat'): JsonResponse
    {
        return self::success($data, $message, 201);
    }
    
    public static function noContent(string $message = 'Data berhasil dihapus'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 204);
    }
}
```

### Gunakan di Controller
```php
use App\Http\Responses\ApiResponse;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $produks = Produk::latest()->paginate(15);
        return ApiResponse::paginated($produks);
    }
    
    public function store(StoreProdukRequest $request)
    {
        $produk = Produk::create($request->validated());
        return ApiResponse::created($produk, 'Produk berhasil ditambahkan');
    }
}
```

### File yang Perlu Diubah
- `app/Http/Responses/ApiResponse.php` (BARU)
- `app/Http/Controllers/vendor/ProdukController.php`
- `app/Http/Controllers/vendor/TransaksiController.php`
- `app/Http/Controllers/vendor/PenggunaController.php`
- `app/Http/Controllers/vendor/LinktreeController.php`
- `app/Http/Controllers/AuctionController.php`
- `app/Http/Controllers/UserController.php`
- `app/Http/Controllers/Admin/UserLelangController.php`
- `app/Http/Controllers/Admin/PaymentManagementController.php`

**Prioritas:** 🟢 NORMAL — Code consistency
**Estimasi:** ~12 file

---

## 5. Rate Limiting

### Masalah
Route publik tidak memiliki rate limiting, sehingga rentan terhadap abuse.

### Implementasi

#### 5.1 Define Rate Limits di Provider
```php
// app/Providers/AppServiceProvider.php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

public function boot(): void
{
    // General API rate limit
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });
    
    // Public page rate limit (linktree)
    RateLimiter::for('public-page', function (Request $request) {
        return Limit::perMinute(30)->by($request->ip());
    });
    
    // Manual transfer (prevent spam)
    RateLimiter::for('manual-transfer', function (Request $request) {
        return Limit::perHour(5)->by($request->ip());
    });
    
    // Auth routes (prevent brute force)
    RateLimiter::for('auth', function (Request $request) {
        return Limit::perMinute(5)->by($request->ip());
    });
    
    // Webhook (high volume allowed)
    RateLimiter::for('webhook', function (Request $request) {
        return Limit::perMinute(100)->by($request->ip());
    });
}
```

#### 5.2 Apply ke Routes
```php
// routes/web.php

// Public linktree page
Route::middleware('throttle:public-page')->group(function () {
    Route::get('/l/{customUrl}', [LinktreePublicController::class, 'show']);
});

// Manual transfer
Route::middleware('throttle:manual-transfer')->group(function () {
    Route::post('/manual-transfer', [ManualTransferController::class, 'store']);
});

// Auth routes
Route::middleware('throttle:auth')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/register', [RegisteredUserController::class, 'store']);
});
```

#### 5.3 Custom Response
```php
// app/Providers/AppServiceProvider.php
use Illuminate\Http\Response;

public function boot(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
            ->response(function (Request $request, array $headers) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak request. Silakan coba lagi dalam beberapa menit.',
                ], Response::HTTP_TOO_MANY_REQUESTS, $headers);
            });
    });
}
```

### File yang Perlu Diubah
- `app/Providers/AppServiceProvider.php`
- `routes/web.php`
- `routes/api.php` (opsional)

**Prioritas:** 🟡 PENTING — Security
**Estimasi:** ~5 file

---

## 6. Activity Log Enhancement

### Masalah
`AuditLogService` sudah ada tapi belum digunakan secara konsisten di semua controller.

### Implementasi

#### 6.1 Enhance AuditLogService
```php
// app/Services/AuditLogService.php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log activity
     */
    public static function log(string $action, string $description = '', array $extra = []): void
    {
        $user = Auth::user();
        
        \App\Models\AuditLog::create([
            'user_id' => $user?->id,
            'vendor_id' => $extra['vendor_id'] ?? null,
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'model_type' => $extra['model_type'] ?? null,
            'model_id' => $extra['model_id'] ?? null,
            'old_values' => $extra['old_values'] ?? null,
            'new_values' => $extra['new_values'] ?? null,
        ]);
    }
    
    /**
     * Log CRUD operations
     */
    public static function logCreated($model, string $description = ''): void
    {
        static::log('created', $description ?: get_class($model) . ' created', [
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'new_values' => $model->toArray(),
        ]);
    }
    
    public static function logUpdated($model, array $oldValues, string $description = ''): void
    {
        static::log('updated', $description ?: get_class($model) . ' updated', [
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $model->toArray(),
        ]);
    }
    
    public static function logDeleted($model, string $description = ''): void
    {
        static::log('deleted', $description ?: get_class($model) . ' deleted', [
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $model->toArray(),
        ]);
    }
    
    /**
     * Log specific actions
     */
    public static function logPayment(string $action, $payment, string $description = ''): void
    {
        static::log("payment.{$action}", $description, [
            'model_type' => get_class($payment),
            'model_id' => $payment->id,
            'new_values' => [
                'amount' => $payment->amount,
                'status' => $payment->status,
            ],
        ]);
    }
    
    public static function logStatusChange($model, string $oldStatus, string $newStatus): void
    {
        static::log('status_changed', get_class($model) . " status: {$oldStatus} → {$newStatus}", [
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $newStatus],
        ]);
    }
}
```

#### 6.2 Gunakan di Controller
```php
// app/Http/Controllers/vendor/ProdukController.php
use App\Services\AuditLogService;

class ProdukController extends Controller
{
    public function store(StoreProdukRequest $request)
    {
        $produk = Produk::create($request->validated());
        
        AuditLogService::logCreated($produk, 'Produk baru ditambahkan: ' . $produk->nama_produk);
        
        return redirect()->route('vendor.produk.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }
    
    public function update(UpdateProdukRequest $request, Produk $produk)
    {
        $oldValues = $produk->toArray();
        
        $produk->update($request->validated());
        
        AuditLogService::logUpdated($produk, $oldValues, 'Produk diupdate: ' . $produk->nama_produk);
        
        return redirect()->route('vendor.produk.show', $produk)
            ->with('success', 'Produk berhasil diupdate');
    }
    
    public function destroy(Produk $produk)
    {
        $produkName = $produk->nama_produk;
        $produk->delete();
        
        AuditLogService::logDeleted($produk, 'Produk dihapus: ' . $produkName);
        
        return redirect()->route('vendor.produk.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}
```

### File yang Perlu Diubah
- `app/Services/AuditLogService.php` (enhance)
- `app/Http/Controllers/vendor/ProdukController.php`
- `app/Http/Controllers/vendor/TransaksiController.php`
- `app/Http/Controllers/vendor/PenggunaController.php`
- `app/Http/Controllers/vendor/LinktreeController.php`
- `app/Http/Controllers/AuctionController.php`
- `app/Http/Controllers/Admin/UserLelangController.php`
- `app/Http/Controllers/Admin/PaymentManagementController.php`

**Prioritas:** 🟢 NORMAL — Audit trail
**Estimasi:** ~10 file

---

## 7. 🔴 Vendor Context Trait (DRY Refactor)

### Masalah (KRITIS)
Pola `Auth::user()->vendorUser->first()` diulang **60+ kali** di seluruh controllers. Ini:
- **Melanggar DRY** — kode sama di banyak tempat
- **Rentan error** — jika logika vendor context berubah, harus update semua tempat
- **Tidak konsisten** — beberapa pakai `Auth::user()->vendorUser->first()`, beberapa `Auth::user()->vendorUser()->first()` (with/without parentheses), beberapa pakai `Tenant::getVendorId()`

### Bukti Dari Kode

```php
// POS Controller — 10x pengulangan
$vendor = Auth::user()->vendorUser->first();  // line 22, 46, 77, 118, 210, 289

// Withdrawal Controller — 6x pengulangan
$vendor = Auth::user()->vendorUser->first();  // line 20, 42, 69, 127, 141, 200

// Thermal Print Controller — 5x pengulangan
$vendor = Auth::user()->vendorUser->first();  // line 20, 53, 86, 110, 151

// Transaksi Controller — 7x pengulangan
$vendor = Auth::user()->vendorUser->first();  // line 75, 115, 219, 244, 289, 427, 468
```

### Implementasi

#### 7.1 Buat Trait
```php
// app/Http/Concerns/HasVendorContext.php
namespace App\Http\Concerns;

use App\Facades\Tenant;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

trait HasVendorContext
{
    /**
     * Get the current vendor from authenticated user.
     * Uses Tenant facade for consistent vendor context.
     */
    protected function getVendor(): ?Vendor
    {
        $vendorId = Tenant::getVendorId();
        
        if ($vendorId) {
            return Vendor::find($vendorId);
        }
        
        // Fallback: get vendor from user relationship
        return Auth::user()?->vendorUser()?->first();
    }
    
    /**
     * Get the current vendor ID.
     */
    protected function getVendorId(): ?int
    {
        return Tenant::getVendorId() ?? Auth::user()?->vendorUser()?->first()?->id;
    }
    
    /**
     * Require vendor context or abort.
     */
    protected function requireVendor(): Vendor
    {
        $vendor = $this->getVendor();
        
        if (!$vendor) {
            abort(403, 'Tidak ada vendor context yang tersedia.');
        }
        
        return $vendor;
    }
    
    /**
     * Check if given model belongs to current vendor.
     */
    protected function isOwnedByCurrentVendor($model): bool
    {
        if (!method_exists($model, 'vendor_id')) {
            return false;
        }
        
        return $model->vendor_id === $this->getVendorId();
    }
    
    /**
     * Enforce vendor ownership or abort.
     */
    protected function authorizeVendorOwnership($model): void
    {
        if (!$this->isOwnedByCurrentVendor($model)) {
            abort(403, 'Akses ditolak: data bukan milik vendor ini.');
        }
    }
}
```

#### 7.2 Gunakan di Controller
```php
// SEBELUM (POS Controller)
class PosController extends Controller
{
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendorUser->first();  // Duplikat!
        if (!$vendor) { ... }
        // ...
    }
    
    public function addToCart(Request $request)
    {
        $vendor = Auth::user()->vendorUser->first();  // Duplikat!
        if (!$vendor) { ... }
        // ...
    }
}

// SESUDAH
class PosController extends Controller
{
    use HasVendorContext;
    
    public function index(Request $request)
    {
        $vendor = $this->requireVendor();  // Single source of truth
        // ... logic
    }
    
    public function addToCart(Request $request)
    {
        $vendor = $this->requireVendor();  // Consistent!
        // ... logic
    }
    
    public function show(Transaksi $transaksi)
    {
        $this->authorizeVendorOwnership($transaksi);  // Authorization built-in
        // ... logic
    }
}
```

### File yang Perlu Diubah
- `app/Http/Concerns/HasVendorContext.php` (BARU)
- `app/Http/Controllers/vendor/pos/PosController.php` (10x duplikasi)
- `app/Http/Controllers/vendor/pos/ThermalPrintController.php` (5x duplikasi)
- `app/Http/Controllers/vendor/pos/InvoiceController.php` (2x duplikasi)
- `app/Http/Controllers/vendor/pos/CheckoutController.php` (4x duplikasi)
- `app/Http/Controllers/vendor/pos/PaymentController.php` (3x duplikasi)
- `app/Http/Controllers/vendor/TransaksiController.php` (7x duplikasi)
- `app/Http/Controllers/vendor/LinktreeController.php` (5x duplikasi)
- `app/Http/Controllers/VendorWithdrawalController.php` (6x duplikasi)
- `app/Http/Controllers/VendorWalletController.php` (7x duplikasi)
- `app/Http/Controllers/VendorBankAccountController.php` (7x duplikasi)
- `app/Http/Controllers/vendor/AuctionBidController.php` (5x duplikasi)
- `app/Http/Controllers/vendor/AbTestController.php` (1x duplikasi)
- `app/Http/Controllers/vendor/TemplateController.php` (1x duplikasi)
- `app/Http/Controllers/ShippingCalculatorController.php` (2x duplikasi)
- `app/Http/Controllers/OrderTrackingController.php` (3x duplikasi)
- `app/Http/Controllers/VendorAuditLogController.php` (4x duplikasi)

**Prioritas:** 🔴 KRITIS — DRY principle, maintainability, consistency
**Estimasi:** ~20 file (1 baru + 19 existing)

---

## 8. Flash Message Standardization

### Masalah
Terdapat **inconsistency** dalam penggunaan flash message keys di seluruh controllers:

| Key Pattern | Contoh Penggunaan | Jumlah |
|-------------|-------------------|--------|
| `toast_success` | Produk, Kategori, Spesifikasi, Bahan, Alat | ~60 |
| `success` | Linktree, Pengguna, Admin, OrderTracking | ~80 |
| `toast_error` | POS, Transaksi, Withdrawal, ThermalPrint | ~40 |
| `error` | Linktree, POS, Payment, Vendor | ~40 |
| `toast_info` | Transaksi (redirect prompts) | ~5 |
| `info` | VendorRating, AuctionBid | ~5 |

**Total: 245+ flash message calls dengan 6 variasi key berbeda.**

### Dampak
- View components harus handle semua variasi key
- Konsistensi UX terganggu (beberapa pakai toast, beberapa plain)
- Maintenance menjadi lebih sulit

### Implementasi

#### 8.1 Buat Flash Message Helper
```php
// app/Http/Responses/FlashMessage.php
namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;

class FlashMessage
{
    private static array $keys = [
        'success' => 'toast_success',
        'error'   => 'toast_error',
        'warning' => 'toast_warning',
        'info'    => 'toast_info',
    ];

    /**
     * Send a flash message with consistent key naming.
     */
    public static function send(
        RedirectResponse $redirect,
        string $message,
        string $type = 'success'
    ): RedirectResponse {
        $key = self::$keys[$type] ?? 'toast_success';
        return $redirect->with($key, $message);
    }

    public static function success(RedirectResponse $redirect, string $message): RedirectResponse
    {
        return self::send($redirect, $message, 'success');
    }

    public static function error(RedirectResponse $redirect, string $message): RedirectResponse
    {
        return self::send($redirect, $message, 'error');
    }

    public static function warning(RedirectResponse $redirect, string $message): RedirectResponse
    {
        return self::send($redirect, $message, 'warning');
    }

    public static function info(RedirectResponse $redirect, string $message): RedirectResponse
    {
        return self::send($redirect, $message, 'info');
    }
}
```

#### 8.2 Gunakan di Controller
```php
use App\Http\Responses\FlashMessage;

class ProdukController extends Controller
{
    public function store(StoreProdukRequest $request)
    {
        $produk = Produk::create($request->validated());
        
        // SEBELUM: return redirect()->route('...')->with('toast_success', '...');
        // SESUDAH:
        return FlashMessage::success(
            redirect()->route('vendor.products.index'),
            'Produk berhasil ditambahkan!'
        );
    }
}
```

#### 8.3 Update View Components
```blade
{{-- resources/views/components/notification.blade.php --}}
@if(session('toast_success'))
    <x-ui.alert type="success" :message="session('toast_success')" />
@endif

@if(session('toast_error'))
    <x-ui.alert type="error" :message="session('toast_error')" />
@endif

@if(session('toast_info'))
    <x-ui.alert type="info" :message="session('toast_info')" />
@endif

@if(session('toast_warning'))
    <x-ui.alert type="warning" :message="session('toast_warning')" />
@endif
```

### File yang Perlu Diubah
- `app/Http/Responses/FlashMessage.php` (BARU)
- Semua controllers (~25 file) — migrasi dari `->with()` manual ke `FlashMessage::`
- `resources/views/components/notification.blade.php`
- `resources/views/dev/components/notification.blade.php`
- `resources/views/user/components/notification.blade.php`

**Prioritas:** 🟡 PENTING — Consistency, maintainability
**Estimasi:** ~25 file (1 baru + 24 existing)

---

## 9. Controller Refactoring (Fat → Thin)

### Masalah
Beberapa controller sudah terlalu besar dan melakukan terlalu banyak hal:

| Controller | Baris | Masalah |
|-----------|-------|---------|
| `LinktreeController` | 920 | CRUD + media upload + products + analytics + A/B test |
| `TransaksiController` | 537 | CRUD + spec processing + PDF generation + inline validation |
| `ProdukController` | 461 | CRUD + spec processing + inline validation + bulk update |
| `UserDashboardController` | 347+ | Dashboard logic + lelang dashboard + stats |

### Implementasi — Action Classes Pattern

#### 9.1 Buat Base Action
```php
// app/Actions/BaseAction.php
namespace App\Actions;

abstract class BaseAction
{
    /**
     * Execute the action.
     */
    abstract public function handle(array $data): mixed;
    
    /**
     * Run the action with validation.
     */
    public function run(array $data): mixed
    {
        return $this->handle($data);
    }
}
```

#### 9.2 Contoh: Linktree Actions
```php
// app/Actions/Linktree/CreateLinktree.php
namespace App\Actions\Linktree;

use App\Actions\BaseAction;
use App\Models\Vendor\Linktree;
use App\Facades\Tenant;

class CreateLinktree extends BaseAction
{
    public function handle(array $data): Linktree
    {
        $vendorId = Tenant::getVendorId();
        
        $data['vendor_id'] = $vendorId;
        $data['is_active'] = false;
        $data['views_count'] = 0;
        $data['clicks_count'] = 0;
        
        // Set default colors
        $data['primary_color'] = $data['primary_color'] ?? $this->getDefaultColor($data['template'], 'primary');
        $data['secondary_color'] = $data['secondary_color'] ?? $this->getDefaultColor($data['template'], 'secondary');
        $data['bg_color'] = $data['bg_color'] ?? $this->getDefaultColor($data['template'], 'bg');
        $data['text_color'] = $data['text_color'] ?? $this->getDefaultColor($data['template'], 'text');
        
        return Linktree::create($data);
    }
    
    protected function getDefaultColor(string $template, string $type): string
    {
        $colors = [
            'minimal' => ['primary' => '#000000', 'secondary' => '#ffffff', 'bg' => '#ffffff', 'text' => '#000000'],
            'colorful' => ['primary' => '#6366f1', 'secondary' => '#8b5cf6', 'bg' => '#faf5ff', 'text' => '#1e1b4b'],
            // ... lainnya
        ];
        
        return $colors[$template][$type] ?? '#000000';
    }
}
```

```php
// app/Actions/Linktree/UploadMedia.php
namespace App\Actions\Linktree;

use App\Actions\BaseAction;
use App\Models\Vendor\Linktree;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadMedia extends BaseAction
{
    public function handle(array $data): string
    {
        /** @var Linktree $linktree */
        $linktree = $data['linktree'];
        /** @var UploadedFile $file */
        $file = $data['file'];
        $type = $data['type']; // 'avatar', 'banner', 'qris'
        
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = "linktree/{$type}/{$filename}";
        
        Storage::disk('public')->put($path, file_get_contents($file));
        
        // Delete old file if exists
        $oldPath = match($type) {
            'avatar' => $linktree->avatar_path,
            'banner' => $linktree->banner_path,
            'qris' => $linktree->qris_image_path,
            default => null,
        };
        
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
        
        return $path;
    }
}
```

#### 9.3 Refactored Controller
```php
// app/Http/Controllers/vendor/LinktreeController.php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Actions\Linktree\CreateLinktree;
use App\Actions\Linktree\UploadMedia;

class LinktreeController extends Controller
{
    public function __construct(
        protected CreateLinktree $createLinktree,
        protected UploadMedia $uploadMedia,
    ) {}
    
    public function store(StoreLinktreeRequest $request)
    {
        $linktree = $this->createLinktree->run($request->validated());
        
        return FlashMessage::success(
            redirect()->route('vendor.linktree.edit', $linktree),
            'Linktree berhasil dibuat!'
        );
    }
    
    public function uploadAvatar(UploadAvatarRequest $request, Linktree $linktree)
    {
        $path = $this->uploadMedia->run([
            'linktree' => $linktree,
            'file' => $request->file('avatar'),
            'type' => 'avatar',
        ]);
        
        $linktree->update(['avatar_path' => $path]);
        
        return FlashMessage::success(back(), 'Avatar berhasil diupload!');
    }
}
```

### File yang Perlu Diubah
- `app/Actions/` (BARU — direktori baru)
- `app/Actions/BaseAction.php` (BARU)
- `app/Actions/Linktree/CreateLinktree.php` (BARU)
- `app/Actions/Linktree/UploadMedia.php` (BARU)
- `app/Actions/Transaksi/CreateTransaksi.php` (BARU)
- `app/Actions/Produk/CreateProduk.php` (BARU)
- `app/Http/Controllers/vendor/LinktreeController.php` (920 → ~200 baris)
- `app/Http/Controllers/vendor/TransaksiController.php` (537 → ~200 baris)
- `app/Http/Controllers/vendor/ProdukController.php` (461 → ~180 baris)

**Prioritas:** 🟡 PENTING — Maintainability, testability
**Estimasi:** ~8 file (6 baru + 3 existing refactored)

---

## 10. Centralized File Upload Service

### Masalah
File upload dilakukan dengan cara yang berbeda-beda di berbagai controller:

```php
// Cara 1: move() — ProdukController
$file->move(public_path('produk_gambar'), $gambarName);

// Cara 2: Storage::disk('public')->put() — LinktreeController
Storage::disk('public')->put($path, file_get_contents($file));

// Cara 3: storeAs() — beberapa tempat
$file->storeAs('avatars', $filename, 'public');

// Tidak ada validasi ukuran tipe konsisten
// Tidak ada cleanup file lama yang konsisten
```

### Implementasi

#### 10.1 Buat FileUploadService
```php
// app/Services/FileUploadService.php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload file to disk with consistent naming and validation.
     */
    public static function upload(
        UploadedFile $file,
        string $directory,
        ?string $filename = null,
        string $disk = 'public'
    ): string {
        $filename = $filename ?: time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        
        $path = $file->storeAs($directory, $filename, $disk);
        
        return $path;
    }
    
    /**
     * Upload and replace old file.
     */
    public static function uploadReplace(
        UploadedFile $file,
        string $directory,
        ?string $oldPath = null,
        string $disk = 'public'
    ): string {
        // Delete old file
        if ($oldPath && self::exists($oldPath, $disk)) {
            self::delete($oldPath, $disk);
        }
        
        return self::upload($file, $directory, disk: $disk);
    }
    
    /**
     * Upload multiple files.
     */
    public static function uploadMultiple(
        array $files,
        string $directory,
        string $disk = 'public'
    ): array {
        return array_map(
            fn ($file) => self::upload($file, $directory, disk: $disk),
            $files
        );
    }
    
    /**
     * Check if file exists.
     */
    public static function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }
    
    /**
     * Delete file.
     */
    public static function delete(string $path, string $disk = 'public'): bool
    {
        if (self::exists($path, $disk)) {
            return Storage::disk($disk)->delete($path);
        }
        
        return false;
    }
    
    /**
     * Get URL for file.
     */
    public static function url(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }
    
    // =========================================================
    // Convenience methods for specific upload types
    // =========================================================
    
    public static function uploadProductImage(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::uploadReplace($file, 'produk_gambar', $oldPath);
    }
    
    public static function uploadVendorLogo(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::uploadReplace($file, 'vendors_logo', $oldPath);
    }
    
    public static function uploadLinktreeAvatar(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::uploadReplace($file, 'linktree/avatars', $oldPath);
    }
    
    public static function uploadLinktreeBanner(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::uploadReplace($file, 'linktree/banners', $oldPath);
    }
    
    public static function uploadLinktreeQris(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::uploadReplace($file, 'linktree/qris', $oldPath);
    }
    
    public static function uploadProofFile(UploadedFile $file): string
    {
        return self::upload($file, 'proofs');
    }
    
    public static function uploadAuctionFile(UploadedFile $file): string
    {
        return self::upload($file, 'auctions');
    }
}
```

#### 10.2 Gunakan di Controller
```php
use App\Services\FileUploadService;

class ProdukController extends Controller
{
    public function store(StoreProdukRequest $request)
    {
        // SEBELUM:
        // $gambarName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        // $file->move(public_path('produk_gambar'), $gambarName);
        // $gambars[] = 'produk_gambar/' . $gambarName;
        
        // SESUDAH:
        $gambars = [];
        if ($request->hasFile('gambar')) {
            $gambars = FileUploadService::uploadMultiple(
                $request->file('gambar'),
                'produk_gambar'
            );
        }
        
        // ... create produk
    }
}
```

### File yang Perlu Diubah
- `app/Services/FileUploadService.php` (BARU)
- `app/Http/Controllers/vendor/ProdukController.php`
- `app/Http/Controllers/vendor/LinktreeController.php` (3 upload methods)
- `app/Http/Controllers/vendor/pos/PosController.php`
- `app/Http/Controllers/vendor/pos/CheckoutController.php`
- `app/Http/Controllers/vendor/BahanController.php`
- `app/Http/Controllers/AuctionController.php`
- `app/Http/Controllers/ManualTransferController.php`
- `app/Http/Controllers/VendorBankAccountController.php`
- `app/Http/Controllers/VendorController.php`

**Prioritas:** 🟡 PENTING — Consistency, security (file validation), maintainability
**Estimasi:** ~10 file (1 baru + 9 existing)

---

## 11. Laravel Policies & Gates

### Masalah
Authorization checks dilakukan secara inline di dalam controller methods, menciptakan code yang duplikat dan sulit di-maintain:

```php
// Duplikasi di banyak controller:
if ($auction->user_id !== Auth::id()) {
    abort(403);
}

if ($produk->vendor_id !== Tenant::getVendorId()) {
    abort(403);
}

$vendor = Auth::user()->vendorUser->first();
if (!$vendor || $transaksi->vendor_id !== $vendor->id) {
    // ...
}
```

### Implementasi

#### 11.1 Buat Policies

```php
// app/Policies/ProdukPolicy.php
namespace App\Policies;

use App\Models\Vendor\Produk;
use App\Models\User;
use App\Facades\Tenant;

class ProdukPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->usertype, ['dev', 'admin', 'vendor']);
    }

    public function view(User $user, Produk $produk): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }
        
        return $produk->vendor_id === Tenant::getVendorId();
    }

    public function create(User $user): bool
    {
        return in_array($user->usertype, ['dev', 'admin', 'vendor']);
    }

    public function update(User $user, Produk $produk): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }
        
        return $produk->vendor_id === Tenant::getVendorId();
    }

    public function delete(User $user, Produk $produk): bool
    {
        return $this->update($user, $produk);
    }
}
```

```php
// app/Policies/AuctionPolicy.php
namespace App\Policies;

use App\Models\Auction;
use App\Models\User;

class AuctionPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Public
    }

    public function view(User $user, Auction $auction): bool
    {
        // Owner, winning vendor, admin/dev can view
        if ($auction->user_id === $user->id) return true;
        if (in_array($user->usertype, ['dev', 'admin'])) return true;
        
        // Winning vendor can view
        if ($user->usertype === 'vendor' && $auction->winner_vendor_id) {
            $vendor = $user->vendorUser()->first();
            return $vendor && $auction->winner_vendor_id === $vendor->id;
        }
        
        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->usertype, ['user', 'dev']);
    }

    public function update(User $user, Auction $auction): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) return true;
        
        return $auction->user_id === $user->id 
            && in_array($auction->status, ['pending', 'draft']);
    }

    public function delete(User $user, Auction $auction): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) return true;
        
        return $auction->user_id === $user->id 
            && !in_array($auction->status, ['paid', 'in_production', 'completed']);
    }

    public function pay(User $user, Auction $auction): bool
    {
        return $auction->user_id === $user->id 
            && $auction->status === 'active';
    }

    public function bid(User $user, Auction $auction): bool
    {
        return $user->usertype === 'vendor' 
            && $auction->status === 'active'
            && $auction->deadline > now();
    }
}
```

```php
// app/Policies/TransaksiPolicy.php
namespace App\Policies;

use App\Models\Vendor\Transaksi;
use App\Models\User;
use App\Facades\Tenant;

class TransaksiPolicy
{
    public function view(User $user, Transaksi $transaksi): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) return true;
        
        return $transaksi->vendor_id === Tenant::getVendorId();
    }

    public function update(User $user, Transaksi $transaksi): bool
    {
        return $this->view($user, $transaksi);
    }

    public function delete(User $user, Transaksi $transaksi): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) return true;
        
        return $transaksi->vendor_id === Tenant::getVendorId()
            && in_array($transaksi->status, ['pending']);
    }
}
```

#### 11.2 Register Policies
```php
// app/Providers/AuthServiceProvider.php (jika ada)
// atau bootstrap/app.php
use App\Policies\ProdukPolicy;
use App\Policies\AuctionPolicy;
use App\Policies\TransaksiPolicy;

Gate::policy(\App\Models\Vendor\Produk::class, ProdukPolicy::class);
Gate::policy(\App\Models\Auction::class, AuctionPolicy::class);
Gate::policy(\App\Models\Vendor\Transaksi::class, TransaksiPolicy::class);
```

#### 11.3 Gunakan di Controller
```php
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProdukController extends Controller
{
    use AuthorizesRequests;
    
    public function show(Produk $produk)
    {
        $this->authorize('view', $produk);
        // ... logic tanpa manual check
    }
    
    public function update(UpdateProdukRequest $request, Produk $produk)
    {
        $this->authorize('update', $produk);
        // ... logic
    }
    
    public function destroy(Produk $produk)
    {
        $this->authorize('delete', $produk);
        $produk->delete();
        // ...
    }
}
```

### File yang Perlu Diubah
- `app/Policies/ProdukPolicy.php` (BARU)
- `app/Policies/AuctionPolicy.php` (BARU)
- `app/Policies/TransaksiPolicy.php` (BARU)
- `app/Policies/LinktreePolicy.php` (BARU)
- `app/Policies/UserPolicy.php` (BARU)
- `app/Providers/AuthServiceProvider.php` atau `bootstrap/app.php`
- `app/Http/Controllers/vendor/ProdukController.php`
- `app/Http/Controllers/vendor/TransaksiController.php`
- `app/Http/Controllers/vendor/LinktreeController.php`
- `app/Http/Controllers/AuctionController.php`
- `app/Http/Controllers/vendor/AuctionBidController.php`
- `app/Http/Controllers/OrderTrackingController.php`
- `app/Http/Controllers/DeliveryConfirmationController.php`

**Prioritas:** 🟡 PENTING — Security, maintainability, Laravel best practice
**Estimasi:** ~15 file (5 baru + 10 existing)

---

## 12. Soft Deletes & Model Conventions

### Masalah
Tidak ada model yang menggunakan `SoftDeletes`. Ketika data dihapus, hilang permanen tanpa audit trail. Untuk platform bisnis percetakan, ini berisiko tinggi.

Selain itu, beberapa model tidak memiliki timestamp management yang konsisten.

### Implementasi

#### 12.1 Tambah SoftDeletes ke Model Penting
```php
// app/Models/Vendor/Produk.php
namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends TenantModel
{
    use SoftDeletes;
    
    // ... existing code
}

// app/Models/Vendor/Transaksi.php
class Transaksi extends TenantModel
{
    use SoftDeletes;
    
    // ... existing code
}

// app/Models/Vendor/Pelanggan.php
class Pelanggan extends TenantModel
{
    use SoftDeletes;
    
    // ... existing code
}

// app/Models/Auction.php
class Auction extends UserTenantModel
{
    use SoftDeletes;
    
    // ... existing code
}

// app/Models/User.php
class User extends Authenticatable
{
    use SoftDeletes;
    
    // ... existing code
}
```

#### 12.2 Migrations untuk SoftDeletes
```php
// database/migrations/xxxx_add_soft_deletes_to_produks_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('transaksis', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('auctions', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
    
    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
```

#### 12.3 Force Delete Only for Admin
```php
// Di controller admin:
class AuctionManagementController extends Controller
{
    public function destroy(Auction $auction)
    {
        $this->authorize('delete', $auction);
        
        // Gunakan force delete hanya untuk admin
        $auction->forceDelete();
        
        return redirect()->route('admin.auctions.index')
            ->with('success', 'Lelang berhasil dihapus permanen!');
    }
}

// Di controller vendor/user:
class AuctionController extends Controller
{
    public function destroy(Auction $auction)
    {
        $this->authorize('delete', $auction);
        
        // Soft delete — bisa dipulihkan oleh admin
        $auction->delete();
        
        return redirect()->route('user.auctions.index')
            ->with('success', 'Lelang berhasil dihapus.');
    }
}
```

### File yang Perlu Diubah
- `app/Models/Vendor/Produk.php`
- `app/Models/Vendor/Transaksi.php`
- `app/Models/Vendor/Pelanggan.php`
- `app/Models/Auction.php`
- `app/Models/User.php`
- `database/migrations/xxxx_add_soft_deletes.php` (BARU)
- `app/Http/Controllers/Admin/AuctionManagementController.php` (force delete)
- `app/Http/Controllers/vendor/ProdukController.php` (restore capabilities)
- `app/Http/Controllers/vendor/TransaksiController.php`
- `app/Http/Controllers/vendor/PelangganController.php`

**Prioritas:** 🟢 NORMAL — Data safety, audit trail
**Estimasi:** ~20 file (1 migration baru + 10 model + 9 controllers)

---

## Prioritas Implementasi

### Phase 1 (Segera) — Security & DRY
1. **Vendor Context Trait (#7)** — Eliminasi 60+ duplikasi kode
2. **Middleware Authorization Check (#2)** — Cegah unauthorized access
3. **Rate Limiting (#5)** — Cegah abuse

### Phase 2 (Minggu Ini) — Code Quality
4. **Flash Message Standardization (#8)** — Consistent UX
5. **Request Validation Classes (#3)** — Centralize validation logic
6. **Laravel Policies & Gates (#11)** — Authorization yang terstruktur

### Phase 3 (Minggu Depan) — Architecture
7. **Centralized File Upload Service (#10)** — Consistent file handling
8. **Controller Refactoring (#9)** — Fat → Thin controllers
9. **API Response Standardization (#4)** — Consistent responses

### Phase 4 (Bulan Depan) — Enhancement
10. **Activity Log Enhancement (#6)** — Better audit trail
11. **Soft Deletes & Model Conventions (#12)** — Data safety
12. **Extract Confirm Dialog Helper (#1)** — Optional refactor

---

## Checklist Implementasi

- [ ] Buat `HasVendorContext` trait dan apply ke semua vendor controllers
- [ ] Buat `AuthorizationService` dan register di provider
- [ ] Tambah authorization check di semua vendor controllers
- [ ] Buat 10+ Request validation classes
- [ ] Buat `ApiResponse` helper
- [ ] Update controllers untuk gunakan ApiResponse
- [ ] Tambah rate limiting di AppServiceProvider
- [ ] Apply rate limiting ke public routes
- [ ] Buat `FlashMessage` helper dan standarisasi semua flash messages
- [ ] Buat Action classes untuk Linktree, Transaksi, Produk
- [ ] Buat `FileUploadService` dan konsolidasi semua file upload
- [ ] Buat Laravel Policies (Produk, Auction, Transaksi, Linktree, User)
- [ ] Register Policies di AuthServiceProvider
- [ ] Enhance `AuditLogService`
- [ ] Gunakan `AuditLogService` di controllers
- [ ] Tambah SoftDeletes ke model-model penting
- [ ] Buat migration untuk SoftDeletes
- [ ] Update documentation (FEATURES.md, ROADMAP.md, AGENT.md)

---

## Statistik Impact

| Metrik | Sebelum | Sesudah (Estimasi) |
|--------|---------|---------------------|
| Duplikasi vendor context | 60+ | 0 (centralized) |
| Flash message keys | 6 variasi | 1 standar |
| Inline auth checks | 30+ | 0 (policies) |
| Inline validation | 25+ | 0 (request classes) |
| File upload patterns | 4 cara | 1 service |
| Controller terbesar | 920 baris | ~200 baris |
| Request classes | 2 | 15+ |
| Policies | 0 | 5 |

---

*Document created: 7 Agustus 2026*
*Extended: 11 Agustus 2026*
*Based on: Comprehensive Audit III findings + Codebase Deep Analysis*
*Next review: Setelah Phase 1 selesai*
