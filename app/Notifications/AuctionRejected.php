<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionRejected extends Notification
{
    use Queueable;

    protected $auction;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Auction $auction, string $reason)
    {
        $this->auction = $auction;
        $this->reason = $reason;
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
            ->subject('Lelang Anda Ditolak - ' . $this->auction->title)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Kami menyesal menginformasikan bahwa lelang Anda telah ditolak.')
            ->line('**Detail Lelang:**')
            ->line('Judul: ' . $this->auction->title)
            ->line('Budget: Rp ' . number_format((float) $this->auction->budget, 0, ',', '.'))
            ->line('**Alasan Penolakan:**')
            ->line($this->reason)
            ->line('Silakan buat lelang baru dengan memperhatikan alasan penolakan di atas.')
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
            'reason' => $this->reason,
            'message' => 'Lelang "' . $this->auction->title . '" telah ditolak. Alasan: ' . $this->reason,
            'type' => 'auction_rejected'
        ];
    }
}
