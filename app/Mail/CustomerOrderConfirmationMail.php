<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\WebsiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerOrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {
        $this->order->loadMissing([
            'items.product',
            'items.variant',
            'country',
            'state',
            'pickupLocation.state',
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation - '.$this->order->order_no,
        );
    }

    public function content(): Content
    {
        $setting = WebsiteSetting::query()->first();

        return new Content(
            view: 'emails.orders.customer-order-confirmation',
            with: [
                'order' => $this->order,
                'setting' => $setting,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
