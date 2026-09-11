<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\WebsiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShippingStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;

        $this->order->loadMissing([
            'user',
            'items',
            'items.product',
            'items.variant',
            'country',
            'state',
        ]);
    }

    public function build()
    {
        $setting = WebsiteSetting::query()->first();

        $subject = match ($this->order->status) {

            'shipped' => 'Your Order Has Been Shipped - '
                .$this->order->order_no,

            'delivered' => 'Your Order Has Been Delivered - '
                .$this->order->order_no,

            default => 'Order Update - '
                .$this->order->order_no,
        };

        return $this
            ->subject($subject)
            ->view('emails.orders.shipping-status')
            ->with([
                'order' => $this->order,
                'setting' => $setting,
            ]);
    }
}
