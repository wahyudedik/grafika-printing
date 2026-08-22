<?php

namespace App\Notifications;

use App\Models\Vendor;
use App\Models\Vendor\Transaksi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorNewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The transaction instance.
     */
    public Transaksi $transaksi;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaksi $transaksi)
    {
        $this->transaksi = $transaksi;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(Vendor $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Vendor $notifiable): MailMessage
    {
        $pelangganName = $this->transaksi->pelanggan->nama ?? 'Umum';

        return (new MailMessage)
            ->subject("Pesanan Baru #{$this->transaksi->kode}")
            ->greeting("Halo {$notifiable->name}!")
            ->line("Anda memiliki pesanan baru dengan kode #{$this->transaksi->kode}")
            ->line('Total: Rp ' . number_format($this->transaksi->total_harga, 0, ',', '.'))
            ->line("Pelanggan: {$pelangganName}")
            ->action('Lihat Pesanan', route('vendor.transactions.show', $this->transaksi->id))
            ->salutation('Terima kasih, [Grafika Printing](' . route('vendor.dashboard') . ')');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(Vendor $notifiable): array
    {
        return [
            'type' => 'new_order',
            'transaksi_id' => $this->transaksi->id,
            'kode' => $this->transaksi->kode,
            'total_harga' => $this->transaksi->total_harga,
            'url' => route('vendor.transactions.show', $this->transaksi->id),
            'message' => 'Pesanan baru #' . $this->transaksi->kode . ' dari ' . ($this->transaksi->pelanggan->nama ?? 'Umum'),
        ];
    }
}
