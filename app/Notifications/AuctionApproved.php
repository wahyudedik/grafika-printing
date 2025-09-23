<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionApproved extends Notification
{
    use Queueable;

    protected $auction;

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
            ->subject('Lelang Anda Disetujui - ' . $this->auction->title)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Selamat! Lelang Anda telah disetujui dan sekarang aktif.')
            ->line('**Detail Lelang:**')
            ->line('Judul: ' . $this->auction->title)
            ->line('Budget: Rp ' . number_format((float) $this->auction->budget, 0, ',', '.'))
            ->line('Deadline: ' . \Carbon\Carbon::parse($this->auction->deadline)->format('d M Y H:i'))
            ->line('Lelang Anda sekarang dapat dilihat oleh vendor dan mereka dapat memberikan penawaran.')
            ->action('Lihat Lelang', route('user.auctions.show', $this->auction))
            ->line('Terima kasih telah menggunakan layanan kami.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'auction_title' => $this->auction->title,
            'message' => 'Lelang "' . $this->auction->title . '" telah disetujui dan sekarang aktif.',
            'type' => 'auction_approved'
        ];
    }
}
