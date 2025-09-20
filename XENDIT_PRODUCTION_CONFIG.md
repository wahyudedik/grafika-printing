# Xendit Production Configuration - grafika.noteds.com

## 🎯 **Overview**
Konfigurasi Xendit untuk production dengan domain `grafika.noteds.com`.

## 🔧 **Environment Configuration**

### 1. **Production .env Variables**
```env
# Xendit Production Configuration
XENDIT_API_KEY=xnd_public_production_your_production_api_key
XENDIT_PUBLIC_KEY=xnd_public_production_your_public_key
XENDIT_WEBHOOK_TOKEN=your_production_webhook_token
XENDIT_BASE_URL=https://api.xendit.co
XENDIT_CALLBACK_URL=https://grafika.noteds.com/api/xendit/webhook

# App Configuration
APP_URL=https://grafika.noteds.com
APP_ENV=production
APP_DEBUG=false

# Security
SESSION_DOMAIN=.noteds.com
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
```

### 2. **Update config/services.php**
```php
'xendit' => [
    'api_key' => env('XENDIT_API_KEY'),
    'public_key' => env('XENDIT_PUBLIC_KEY'),
    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
    'base_url' => env('XENDIT_BASE_URL', 'https://api.xendit.co'),
    'redirect_url' => env('APP_URL', 'https://grafika.noteds.com'),
    'webhook_url' => env('XENDIT_CALLBACK_URL', 'https://grafika.noteds.com/api/xendit/webhook'),
],
```

## 🌐 **Xendit Dashboard Configuration**

### 1. **Webhook Settings**
- **URL**: `https://grafika.noteds.com/api/xendit/webhook`
- **Environment**: Production
- **Events**: 
  - `payment.paid`
  - `payment.expired`
  - `payment.failed`
  - `invoice.paid`
  - `invoice.expired`

### 2. **API Keys**
- **Public Key**: Untuk frontend integration
- **Secret Key**: Untuk backend API calls
- **Webhook Token**: Untuk webhook verification

## 🔒 **Security Configuration**

### 1. **HTTPS Enforcement**
```php
// app/Http/Middleware/TrustProxies.php
protected $proxies = [
    '127.0.0.1',
    '::1',
    // Add your VPS IP if needed
];

protected $headers = Request::HEADER_X_FORWARDED_FOR |
    Request::HEADER_X_FORWARDED_HOST |
    Request::HEADER_X_FORWARDED_PORT |
    Request::HEADER_X_FORWARDED_PROTO |
    Request::HEADER_X_FORWARDED_AWS_ELB;
```

### 2. **Webhook Verification**
```php
// app/Http/Controllers/XenditWebhookController.php
public function handleWebhook(Request $request)
{
    // Verify webhook token
    $webhookToken = $request->header('x-callback-token');
    $expectedToken = config('services.xendit.webhook_token');
    
    if ($webhookToken !== $expectedToken) {
        Log::warning('Invalid webhook token', [
            'received' => $webhookToken,
            'expected' => $expectedToken
        ]);
        return response()->json(['error' => 'Invalid token'], 401);
    }
    
    // Process webhook...
}
```

## 📱 **Frontend Configuration**

### 1. **Update JavaScript untuk Production**
```javascript
// resources/js/xendit-payment.js
class XenditPayment {
    constructor() {
        this.apiUrl = window.location.origin + '/api/xendit';
        this.isProduction = window.location.hostname === 'grafika.noteds.com';
    }
    
    async createPayment(data) {
        const response = await fetch(`${this.apiUrl}/auctions/${data.auctionId}/payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        return await response.json();
    }
}
```

### 2. **Update Blade Templates**
```php
// resources/views/payments/xendit.blade.php
<script>
    // Use secure URLs for production
    const xenditConfig = {
        apiUrl: '{{ secure_url('/api/xendit') }}',
        webhookUrl: '{{ secure_url('/api/xendit/webhook') }}',
        isProduction: {{ app()->environment('production') ? 'true' : 'false' }}
    };
</script>
```

## 🔄 **Database Configuration**

### 1. **Update XenditPayment Model**
```php
// app/Models/XenditPayment.php
protected $fillable = [
    'external_id',
    'xendit_id',
    'amount',
    'description',
    'customer',
    'status',
    'checkout_url',
    'expires_at',
    'paid_at',
    'webhook_data'
];

protected $casts = [
    'customer' => 'array',
    'webhook_data' => 'array',
    'expires_at' => 'datetime',
    'paid_at' => 'datetime',
];
```

## 📊 **Monitoring & Logging**

### 1. **Xendit Webhook Logging**
```php
// app/Http/Controllers/XenditWebhookController.php
public function handleWebhook(Request $request)
{
    Log::info('Xendit webhook received', [
        'headers' => $request->headers->all(),
        'body' => $request->all(),
        'ip' => $request->ip()
    ]);
    
    // Process webhook...
}
```

### 2. **Payment Status Tracking**
```php
// app/Http/Controllers/XenditPaymentController.php
public function updatePaymentStatus($paymentId, $status)
{
    $payment = XenditPayment::find($paymentId);
    
    if ($payment) {
        $payment->update([
            'status' => $status,
            'paid_at' => $status === 'paid' ? now() : null
        ]);
        
        Log::info('Payment status updated', [
            'payment_id' => $paymentId,
            'status' => $status,
            'external_id' => $payment->external_id
        ]);
    }
}
```

## 🧪 **Testing Production Configuration**

### 1. **Test Webhook Endpoint**
```bash
# Test webhook accessibility
curl -X GET https://grafika.noteds.com/api/xendit/webhook/test

# Test webhook with valid token
curl -X POST https://grafika.noteds.com/api/xendit/webhook \
  -H "Content-Type: application/json" \
  -H "x-callback-token: your_webhook_token" \
  -d '{"test": "webhook"}'
```

### 2. **Test Payment Creation**
```bash
# Test payment creation
curl -X POST https://grafika.noteds.com/api/xendit/auctions/1/payment \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_token" \
  -d '{"amount": 100000, "description": "Test Payment"}'
```

## 🔧 **Production Optimizations**

### 1. **Cache Configuration**
```php
// config/cache.php
'default' => env('CACHE_STORE', 'redis'),

'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
    ],
],
```

### 2. **Queue Configuration**
```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'redis'),

'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
],
```

## 📋 **Deployment Checklist**

### Pre-Deployment:
- [ ] Xendit production API keys obtained
- [ ] Webhook URL configured in Xendit dashboard
- [ ] SSL certificate installed
- [ ] Database configured
- [ ] Environment variables set

### Post-Deployment:
- [ ] Webhook endpoint accessible
- [ ] Payment creation working
- [ ] Webhook processing working
- [ ] Logs being written
- [ ] Error handling working
- [ ] Security headers set

## 🆘 **Troubleshooting**

### Common Issues:
1. **Webhook not receiving**: Check URL and token
2. **Payment creation failing**: Check API key
3. **HTTPS issues**: Check SSL configuration
4. **Database errors**: Check connection and permissions

### Debug Commands:
```bash
# Check Xendit configuration
php artisan tinker --execute="echo config('services.xendit.api_key');"

# Test webhook endpoint
curl -I https://grafika.noteds.com/api/xendit/webhook

# Check logs
tail -f storage/logs/laravel.log
```

## 📞 **Support**

- **Xendit Support**: https://support.xendit.co/
- **Documentation**: https://docs.xendit.co/
- **Status Page**: https://status.xendit.co/

---

**Xendit Production Configuration selesai!** 🎉
