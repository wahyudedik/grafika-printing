<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCompletedNotification extends Notification
{
    use Queueable;

    public $auction;

    /**
     * Create a new notification instance.
     */
    public function __construct(Auction $auction)
    {
        $this->auction = $auction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Pesanan Anda Telah Selesai!')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('🎊 Selamat! Pesanan Anda telah selesai dan siap untuk diambil.')
            ->line('**Detail Pesanan:**')
            ->line('• Lelang: ' . $this->auction->title)
            ->line('• Vendor: ' . $this->auction->winnerVendor->name)
            ->line('• Status: Selesai')
            ->line('')
            ->line('**Sekarang Anda dapat:**')
            ->line('⭐ Memberikan rating dan review untuk vendor')
            ->line('📝 Berbagi pengalaman Anda dengan pengguna lain')
            ->line('🔍 Melihat detail pesanan di dashboard')
            ->line('')
            ->action('Beri Rating & Review', route('vendor.ratings.create', $this->auction))
            ->line('Terima kasih telah menggunakan layanan kami!')
            ->salutation('Salam, Tim Grafika Printing');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_completed',
            'auction_id' => $this->auction->id,
            'auction_title' => $this->auction->title,
            'vendor_name' => $this->auction->winnerVendor->name,
            'message' => 'Pesanan Anda telah selesai! Berikan rating untuk vendor.',
            'action_url' => route('vendor.ratings.create', $this->auction),
            'action_text' => 'Beri Rating'
        ];
    }
}
