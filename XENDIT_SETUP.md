# Setup Xendit Integration

## Environment Variables

Tambahkan konfigurasi berikut ke file `.env`:

```env
# Xendit Configuration
XENDIT_API_KEY=your_xendit_api_key_here
XENDIT_PUBLIC_KEY=your_xendit_public_key_here
XENDIT_WEBHOOK_TOKEN=your_xendit_webhook_token_here
XENDIT_BASE_URL=https://api.xendit.co
XENDIT_CALLBACK_URL=https://yourdomain.com/xendit/webhook
```

## Setup Steps

### 1. Daftar Akun Xendit
1. Kunjungi [Xendit Dashboard](https://dashboard.xendit.co/)
2. Daftar akun atau login
3. Verifikasi akun Anda

### 2. Dapatkan API Keys
1. Di dashboard Xendit, buka **Settings** > **API Keys**
2. Copy **Secret Key** untuk `XENDIT_API_KEY`
3. Copy **Public Key** untuk `XENDIT_PUBLIC_KEY`
4. Generate **Webhook Token** untuk `XENDIT_WEBHOOK_TOKEN`

### 3. Setup Webhook
1. Di dashboard Xendit, buka **Settings** > **Webhooks**
2. Tambahkan webhook URL: `https://yourdomain.com/xendit/webhook`
3. Pilih events yang ingin diterima:
   - `payment_link.paid`
   - `payment_link.expired`
   - `xenpayment.paid`
   - `xenpayment.expired`

### 4. Run Migration
```bash
php artisan migrate
```

### 5. Test Integration
```bash
# Test payment methods endpoint
curl -X GET http://localhost:8000/xendit/payment-methods

# Test webhook (dengan ngrok untuk development)
ngrok http 8000
# Update XENDIT_CALLBACK_URL dengan ngrok URL
```

## Features

### Payment Link
- Membuat link pembayaran yang bisa dibagikan
- Support berbagai metode pembayaran
- Auto expire setelah 24 jam

### XenPayment Widget
- Widget pembayaran yang embedded
- Real-time payment status
- Support QR Code, E-Wallet, Bank Transfer

### Webhook Integration
- Otomatis update status pembayaran
- Integrasi dengan sistem wallet vendor
- Logging untuk debugging

## API Endpoints

### Create Payment
```http
POST /xendit/auctions/{auction}/payment
Content-Type: application/json

{
    "amount": 100000,
    "payment_type": "payment_link",
    "customer": {
        "given_names": "John Doe",
        "email": "john@example.com"
    }
}
```

### Check Payment Status
```http
GET /xendit/payments/{payment}/status
```

### Get Payment Methods
```http
GET /xendit/payment-methods
```

## Payment Flow

1. **User memilih vendor pemenang lelang**
2. **System create payment link/widget**
3. **User melakukan pembayaran**
4. **Xendit mengirim webhook ke sistem**
5. **System update status pembayaran**
6. **Dana masuk ke wallet vendor**
7. **Order otomatis masuk ke POS vendor**

## Troubleshooting

### Webhook tidak diterima
1. Pastikan URL webhook benar
2. Check firewall/security settings
3. Gunakan ngrok untuk development
4. Check logs di `storage/logs/laravel.log`

### Payment tidak terupdate
1. Check webhook signature verification
2. Pastikan XENDIT_WEBHOOK_TOKEN benar
3. Check database connection
4. Verify auction dan bid data

### API Error
1. Check XENDIT_API_KEY
2. Verify request format
3. Check Xendit dashboard untuk error details
4. Review logs untuk debugging

## Security Notes

1. **Jangan commit API keys ke repository**
2. **Gunakan HTTPS untuk webhook**
3. **Verify webhook signature**
4. **Log semua webhook events**
5. **Implement rate limiting**

## Support

- [Xendit Documentation](https://docs.xendit.co/)
- [Xendit API Reference](https://developers.xendit.co/api-reference/)
- [Xendit Support](https://support.xendit.co/)
