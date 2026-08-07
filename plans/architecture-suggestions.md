# Saran Perbaikan Arsitektur — Grafika-Printing

> Dokumen ini berisi rekomendasi arsitektur berdasarkan hasil **Comprehensive Audit III** (7 Agustus 2026).
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

## Prioritas Implementasi

### Phase 1 (Segera) — Security
1. **Middleware Authorization Check** — Cegah unauthorized access
2. **Rate Limiting** — Cegah abuse

### Phase 2 (Minggu Depan) — Code Quality
3. **Request Validation Classes** — Centralize validation logic
4. **API Response Standardization** — Consistent responses

### Phase 3 (Bulan Depan) — Enhancement
5. **Activity Log Enhancement** — Better audit trail
6. **Extract Confirm Dialog Helper** — Optional refactor

---

## Checklist Implementasi

- [ ] Buat `AuthorizationService` dan register di provider
- [ ] Tambah authorization check di semua vendor controllers
- [ ] Buat 10+ Request validation classes
- [ ] Buat `ApiResponse` helper
- [ ] Update controllers untuk gunakan ApiResponse
- [ ] Tambah rate limiting di AppServiceProvider
- [ ] Apply rate limiting ke public routes
- [ ] Enhance `AuditLogService`
- [ ] Gunakan `AuditLogService` di controllers
- [ ] Update documentation (FEATURES.md, ROADMAP.md)

---

*Document created: 7 Agustus 2026*
*Based on: Comprehensive Audit III findings*
*Next review: Setelah implementasi selesai*
