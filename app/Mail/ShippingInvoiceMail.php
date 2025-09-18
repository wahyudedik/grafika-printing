<?php

namespace App\Mail;

use App\Models\Vendor\Transaksi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShippingInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaksi;
    public $paymentLink;

    /**
     * Create a new message instance.
     */
    public function __construct(Transaksi $transaksi, array $paymentLink)
    {
        $this->transaksi = $transaksi;
        $this->paymentLink = $paymentLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Pengiriman - ' . $this->transaksi->kode,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.shipping-invoice',
            with: [
                'transaksi' => $this->transaksi,
                'paymentLink' => $this->paymentLink
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
