# Xendit Webhook Setup Guide

## Masalah: "URL not set" di Dashboard Xendit

Berdasarkan screenshot yang Anda berikan, webhook URL belum dikonfigurasi di dashboard Xendit. Berikut adalah langkah-langkah untuk mengkonfigurasinya:

## Langkah 1: Buka Webhook Configuration

1. **Login ke Xendit Dashboard** → https://dashboard.xendit.co
2. **Pilih menu "Developers"** di sidebar kiri
3. **Klik "Webhooks"** atau "Log Webhook"

## Langkah 2: Konfigurasi Webhook URL

### Untuk Development (Local):
```
http://127.0.0.1:8000/xendit/webhook
```

### Untuk Production:
```
https://yourdomain.com/xendit/webhook
```

## Langkah 3: Pilih Events

Pilih events berikut:
- ✅ `invoice.paid` - Pembayaran berhasil
- ✅ `invoice.expired` - Pembayaran expired  
- ✅ `invoice.failed` - Pembayaran gagal
- ✅ `payment_link.paid` - Payment link berhasil
- ✅ `payment_link.expired` - Payment link expired
- ✅ `xenpayment.paid` - XenPayment berhasil
- ✅ `xenpayment.failed` - XenPayment gagal

## Langkah 4: Verifikasi Konfigurasi

Setelah mengkonfigurasi, Anda akan melihat:
- ✅ Status webhook berubah dari "URL not set" menjadi "Active"
- ✅ Webhook URL terdaftar dengan benar
- ✅ Events yang dipilih muncul di dashboard

## Langkah 5: Test Webhook

1. **Buat payment link** melalui aplikasi
2. **Monitor webhook log** di dashboard Xendit
3. **Cek Laravel log** di `storage/logs/laravel.log`

## Troubleshooting

### Jika masih "URL not set":
1. Pastikan URL webhook sudah disimpan
2. Pastikan events sudah dipilih
3. Pastikan webhook dalam status "Active"

### Jika webhook tidak terkirim:
1. Cek apakah server Laravel berjalan
2. Cek apakah route `/xendit/webhook` dapat diakses
3. Cek firewall atau proxy settings

### Jika signature verification gagal:
1. Pastikan `XENDIT_WEBHOOK_TOKEN` sudah diset di `.env`
2. Pastikan webhook token di dashboard Xendit sama dengan di aplikasi

## Konfigurasi Environment

Pastikan di file `.env`:
```env
XENDIT_API_KEY=your_api_key
XENDIT_PUBLIC_KEY=your_public_key
XENDIT_WEBHOOK_TOKEN=your_webhook_token
XENDIT_BASE_URL=https://api.xendit.co
```

## Webhook Endpoint yang Sudah Siap

Aplikasi sudah memiliki endpoint webhook yang siap:
- **Route:** `POST /xendit/webhook`
- **Controller:** `XenditWebhookController@handleWebhook`
- **Middleware:** `XenditWebhookMiddleware`
- **Signature Verification:** ✅ Implemented
- **Event Handling:** ✅ Implemented

## Events yang Didukung

| Event | Description | Handler |
|-------|-------------|---------|
| `invoice.paid` | Pembayaran berhasil | `handlePaymentLinkPaid()` |
| `invoice.expired` | Pembayaran expired | `handlePaymentLinkExpired()` |
| `invoice.failed` | Pembayaran gagal | `handlePaymentLinkFailed()` |
| `xenpayment.paid` | XenPayment berhasil | `handleXenPaymentPaid()` |
| `xenpayment.failed` | XenPayment gagal | `handleXenPaymentFailed()` |

## Log Monitoring

Setelah webhook dikonfigurasi, monitor log di:
- **Xendit Dashboard:** Log Webhook section
- **Laravel Log:** `storage/logs/laravel.log`
- **Browser Console:** Untuk debugging frontend

## Next Steps

1. ✅ Konfigurasi webhook URL di dashboard Xendit
2. ✅ Pilih events yang diperlukan
3. ✅ Test dengan membuat payment link
4. ✅ Monitor webhook delivery
5. ✅ Verify payment status updates
