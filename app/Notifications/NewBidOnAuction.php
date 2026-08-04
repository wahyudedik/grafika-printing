<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Vendor;

class NewBidOnAuction extends Notification
{
    use Queueable;

    /**
     * The auction instance.
     */
    protected Auction $auction;

    /**
     * The bid instance.
     */
    protected AuctionBid $bid;

    /**
     * The vendor instance.
     */
    protected Vendor $vendor;

    /**
     * Create a new notification instance.
     */
    public function __construct(Auction $auction, AuctionBid $bid, Vendor $vendor)
    {
        $this->auction = $auction;
        $this->bid = $bid;
        $this->vendor = $vendor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
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
            'bid_id' => $this->bid->id,
            'bid_amount' => $this->bid->bid_amount,
            'vendor_id' => $this->vendor->id,
            'vendor_name' => $this->vendor->name ?? $this->vendor->nama_vendor ?? 'Vendor',
            'message' => $this->bid->message,
            'type' => 'new_bid',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $vendorName = $this->vendor->name ?? $this->vendor->nama_vendor ?? 'Vendor';
        $bidAmount = number_format($this->bid->bid_amount, 0, ',', '.');

        return (new MailMessage)
            ->subject('Penawaran Baru di Lelang: ' . $this->auction->title)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line("Ada penawaran baru untuk lelang **{$this->auction->title}** dari **{$vendorName}**.")
            ->line("**Jumlah Penawaran:** Rp {$bidAmount}")
            ->line($this->bid->message ? "**Pesan:** {$this->bid->message}" : '')
            ->line('Silakan login untuk melihat detail penawaran.')
            ->action('Lihat Lelang', route('auctions.show', $this->auction))
            ->line('Terima kasih telah menggunakan Grafika Printing!');
    }

    /**
     * Get the array representation for database storage.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
