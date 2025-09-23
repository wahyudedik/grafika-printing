# 🏪 POS System dengan Xendit Integration - Dokumentasi Lengkap

## 📋 **OVERVIEW**

Sistem POS (Point of Sale) yang terintegrasi dengan Xendit payment gateway untuk mendukung dual payment system: **Cash (Langsung)** dan **Online Payment (Xendit)**.

## 🚀 **FITUR UTAMA**

### **1. Dual Payment System**
- **💵 Cash Payment**: Pembayaran tunai langsung dengan kalkulasi kembalian otomatis
- **🌐 Online Payment**: Pembayaran via Xendit (Bank Transfer, E-Wallet, QRIS, Retail Outlet)
- **🔄 Payment Options**: Pilihan metode pembayaran yang fleksibel

### **2. Thermal Printing Integration**
- **🖨️ Direct Printing**: Cetak langsung ke thermal printer tanpa download PDF
- **📄 Receipt Generation**: Generate receipt otomatis setelah pembayaran
- **⚡ Real-time Printing**: Cetak real-time dengan JavaScript integration

### **3. Xendit Payment Gateway**
- **🏦 Bank Transfer**: Virtual Account (BCA, BNI, BRI, BSI, Mandiri, Permata)
- **📱 E-Wallet**: OVO, DANA, LinkAja, ShopeePay
- **🏪 Retail Outlet**: Alfamart, Indomaret
- **📱 QRIS**: QR Code payment

## 🔄 **COMPLETE POS FLOW**

### **1. Product Selection**
```
User → POS Interface → Add to Cart → Cart Management
```

### **2. Checkout Process**
```
Cart → Customer Selection → Payment Options → Payment Processing
```

### **3. Payment Processing**
```
Payment Options → Cash/Online → Payment Success → Receipt Printing
```

## 🏗️ **SYSTEM ARCHITECTURE**

### **Controllers**
- `PosController`: Main POS interface
- `CheckoutController`: Checkout process
- `PaymentController`: Payment processing (NEW)
- `InvoiceController`: Invoice generation
- `ThermalPrintController`: Thermal printing

### **Models**
- `Transaksi`: Enhanced with payment fields
- `Pelanggan`: Customer management
- `Produk`: Product catalog

### **Services**
- `XenditService`: Xendit payment gateway integration
- `AdminFeeService`: Admin fee calculation

## 📊 **DATABASE STRUCTURE**

### **Enhanced Transaksi Table**
```sql
-- Payment Fields
payment_method VARCHAR(255) NULL
payment_amount DECIMAL(15,2) NULL
change_amount DECIMAL(15,2) NULL
admin_fee DECIMAL(15,2) NULL
paid_at TIMESTAMP NULL

-- Xendit Fields
xendit_payment_id VARCHAR(255) NULL
xendit_external_id VARCHAR(255) NULL
customer_email VARCHAR(255) NULL
customer_phone VARCHAR(255) NULL
payment_status ENUM('pending','paid','failed','cancelled') DEFAULT 'pending'
```

## 🛣️ **ROUTES STRUCTURE**

### **POS Routes**
```php
// Main POS
Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
Route::get('/pos/cart', [PosController::class, 'cart'])->name('pos.cart');
Route::post('/pos/checkout', [CheckoutController::class, 'processCheckout'])->name('pos.checkout');

// Payment System
Route::prefix('pos/payment')->name('pos.payment.')->group(function () {
    Route::get('/{transaksi}/options', [PaymentController::class, 'showPaymentOptions'])->name('options');
    Route::get('/{transaksi}/cash', [PaymentController::class, 'showPaymentOptions'])->name('cash');
    Route::post('/{transaksi}/cash', [PaymentController::class, 'processCashPayment'])->name('cash.process');
    Route::get('/{transaksi}/online', [PaymentController::class, 'showPaymentOptions'])->name('online');
    Route::post('/{transaksi}/online', [PaymentController::class, 'processXenditPayment'])->name('online.process');
    Route::get('/{transaksi}/success', [PaymentController::class, 'paymentSuccess'])->name('success');
    Route::get('/{transaksi}/failure', [PaymentController::class, 'paymentFailure'])->name('failure');
});

// Thermal Printing
Route::get('/pos/thermal/{transaksi}/print', [ThermalPrintController::class, 'printDirect'])->name('pos.thermal.print');
```

## 💰 **PAYMENT FLOW**

### **Cash Payment Flow**
```
1. User selects "Cash Payment"
2. Enter payment amount received
3. System calculates change automatically
4. Process payment and update transaction
5. Generate receipt and print
6. Redirect to success page
```

### **Online Payment Flow**
```
1. User selects "Online Payment"
2. Choose payment method (Bank Transfer, E-Wallet, QRIS, Retail)
3. Enter customer email and phone
4. Create Xendit payment link
5. Redirect customer to payment page
6. Handle payment callback
7. Update transaction status
8. Generate receipt and print
```

## 🎯 **IMPLEMENTATION DETAILS**

### **1. PaymentController**
```php
class PaymentController extends Controller
{
    // Show payment options
    public function showPaymentOptions(Transaksi $transaksi)
    
    // Process cash payment
    public function processCashPayment(Request $request, Transaksi $transaksi)
    
    // Process Xendit payment
    public function processXenditPayment(Request $request, Transaksi $transaksi)
    
    // Payment success
    public function paymentSuccess(Transaksi $transaksi)
    
    // Payment failure
    public function paymentFailure(Transaksi $transaksi)
}
```

### **2. Enhanced Transaksi Model**
```php
protected $fillable = [
    // ... existing fields ...
    'payment_method',
    'payment_amount',
    'change_amount',
    'admin_fee',
    'paid_at',
    'xendit_payment_id',
    'xendit_external_id',
    'customer_email',
    'customer_phone',
    'payment_status'
];

protected $casts = [
    'payment_amount' => 'decimal:2',
    'change_amount' => 'decimal:2',
    'admin_fee' => 'decimal:2',
    'paid_at' => 'datetime'
];
```

### **3. Xendit Integration**
```php
// Payment data structure
$paymentData = [
    'external_id' => 'pos_' . $transaksi->id . '_' . time(),
    'amount' => $totalAmount,
    'description' => 'POS Payment: ' . $transaksi->kode,
    'customer' => [
        'given_names' => $transaksi->pelanggan->nama,
        'email' => $request->customer_email,
        'mobile_number' => $request->customer_phone
    ],
    'items' => [
        [
            'name' => 'POS Transaction #' . $transaksi->kode,
            'quantity' => 1,
            'price' => $transaksi->total_harga,
            'category' => 'Printing Service'
        ]
    ],
    'success_redirect_url' => route('vendor.pos.payment.success', $transaksi),
    'failure_redirect_url' => route('vendor.pos.payment.failure', $transaksi)
];
```

## 🖨️ **THERMAL PRINTING**

### **Direct Printing**
```javascript
function printReceipt() {
    const style = document.createElement('style');
    style.innerHTML = `
        @page {
            size: 80mm auto;
            margin: 0;
        }
    `;
    document.head.appendChild(style);
    
    setTimeout(function() {
        window.print();
        setTimeout(function() {
            document.head.removeChild(style);
        }, 1000);
    }, 100);
}
```

### **Thermal Print Controller**
```php
class ThermalPrintController extends Controller
{
    public function printDirect(Transaksi $transaksi)
    {
        // Generate thermal-optimized view
        return view('pos.thermal-receipt', compact('transaksi'));
    }
    
    public function printViaJS(Transaksi $transaksi)
    {
        // JavaScript-based printing
        return view('pos.thermal-js-print', compact('transaksi'));
    }
}
```

## 📱 **USER INTERFACE**

### **Payment Options Page**
- **Cash Payment**: Direct cash processing with change calculation
- **Online Payment**: Xendit integration with multiple payment methods
- **Transaction Summary**: Clear display of amounts and fees

### **Cash Payment Form**
- Payment amount input with validation
- Automatic change calculation
- Real-time summary display
- Notes field for additional information

### **Online Payment Form**
- Payment method selection (Bank Transfer, E-Wallet, QRIS, Retail)
- Customer information (Email, Phone)
- Payment instructions
- Secure payment link generation

## 🔒 **SECURITY FEATURES**

### **Payment Security**
- **CSRF Protection**: All forms protected with CSRF tokens
- **Input Validation**: Comprehensive validation for all payment data
- **Amount Validation**: Minimum payment amount validation
- **User Authorization**: Vendor-only access to transactions

### **Data Protection**
- **Encrypted Storage**: Sensitive payment data encrypted
- **Secure Communication**: HTTPS for all payment communications
- **Audit Trail**: Complete payment logging

## 📊 **ADMIN FEE INTEGRATION**

### **Fee Calculation**
```php
// Calculate admin fees for POS transaction
$feeCalculation = $this->adminFeeService->calculateFees(
    $transaksi->total_harga, 
    'pos_transaction'
);

$totalAmount = $transaksi->total_harga + $feeCalculation['admin_fee'];
```

### **Fee Breakdown**
- **Transaction Amount**: Base transaction value
- **Admin Fee**: Configurable percentage or fixed amount
- **Payment Gateway Fee**: Xendit processing fee
- **Total Amount**: Final amount to be paid

## 🧪 **TESTING**

### **Payment Testing**
```bash
# Test cash payment
php artisan test --filter=PaymentController

# Test Xendit integration
php artisan test --filter=XenditPayment

# Test thermal printing
php artisan test --filter=ThermalPrint
```

### **Manual Testing**
1. **Cash Payment**: Test with various amounts and change calculations
2. **Online Payment**: Test Xendit payment link generation
3. **Thermal Printing**: Test direct printing functionality
4. **Payment Success/Failure**: Test callback handling

## 🚀 **DEPLOYMENT**

### **Environment Setup**
```env
# Xendit Configuration
XENDIT_SECRET_KEY=your_secret_key
XENDIT_PUBLIC_KEY=your_public_key
XENDIT_WEBHOOK_TOKEN=your_webhook_token

# Payment Settings
PAYMENT_GATEWAY_ENABLED=true
THERMAL_PRINTER_ENABLED=true
```

### **Database Migration**
```bash
# Run payment fields migration
php artisan migrate

# Verify table structure
php artisan tinker
>>> Schema::getColumnListing('transaksis')
```

## 📈 **MONITORING & ANALYTICS**

### **Payment Analytics**
- **Cash vs Online**: Payment method distribution
- **Success Rate**: Payment success/failure rates
- **Revenue Tracking**: Daily/monthly revenue reports
- **Customer Analytics**: Payment preferences

### **Performance Metrics**
- **Transaction Speed**: Average processing time
- **Print Speed**: Thermal printer performance
- **Error Rate**: Payment failure analysis

## 🎉 **BENEFITS**

### **For Vendors**
- **Dual Payment Options**: Cash and online payments
- **Real-time Processing**: Instant payment confirmation
- **Thermal Printing**: Professional receipt generation
- **Admin Fee Tracking**: Automatic fee calculation

### **For Customers**
- **Multiple Payment Methods**: Bank transfer, e-wallet, QRIS
- **Secure Payments**: Xendit security standards
- **Instant Receipts**: Digital and printed receipts
- **Payment Flexibility**: Choose preferred payment method

## 🔧 **MAINTENANCE**

### **Regular Tasks**
- **Payment Reconciliation**: Daily payment verification
- **Thermal Printer Maintenance**: Regular cleaning and testing
- **Xendit Balance Monitoring**: Ensure sufficient balance
- **Error Log Review**: Monitor payment failures

### **Troubleshooting**
- **Payment Failures**: Check Xendit configuration
- **Print Issues**: Verify thermal printer connection
- **Database Issues**: Monitor transaction integrity
- **Performance Issues**: Optimize payment processing

## 🎯 **CONCLUSION**

POS System dengan Xendit Integration memberikan solusi lengkap untuk:

1. **💵 Cash Payments**: Direct cash processing dengan kembalian otomatis
2. **🌐 Online Payments**: Xendit integration dengan multiple payment methods
3. **🖨️ Thermal Printing**: Professional receipt generation
4. **📊 Analytics**: Comprehensive tracking dan monitoring
5. **🔒 Security**: Enterprise-grade security features

**Sistem ini siap untuk production dengan fitur lengkap dan keamanan yang optimal!** 🚀✨

## 📞 **SUPPORT**

Untuk dukungan teknis atau pertanyaan tentang implementasi:
- **Documentation**: Lihat dokumentasi lengkap di file ini
- **Testing**: Jalankan test suite untuk verifikasi
- **Monitoring**: Gunakan dashboard untuk monitoring real-time
- **Logs**: Periksa log files untuk troubleshooting

**POS System dengan Xendit Integration - Production Ready!** 🎉🏪💳
