<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\WebsiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPickedUpMail extends Mailable
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
            'pickupLocation.state',
        ]);
    }

    public function build()
    {
        $setting = WebsiteSetting::query()->first();

        return $this
            ->subject('Pickup Completed - '.$this->order->order_no)
            ->view('emails.orders.picked-up')
            ->with([
                'order' => $this->order,
                'setting' => $setting,
            ]);
    }
}
