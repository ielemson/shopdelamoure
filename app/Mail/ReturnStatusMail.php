<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\WebsiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $returnEvent;

    public function __construct(
        Order $order,
        string $returnEvent
    ) {
        $this->order = $order;
        $this->returnEvent = $returnEvent;

        $this->order->loadMissing([
            'user',
            'items',
            'items.product',
            'items.variant',
            'country',
            'state',
            'pickupLocation.state',
        ]);
    }

    public function build()
    {
        $setting = WebsiteSetting::query()->first();

        $subject = match ($this->returnEvent) {
            'requested' =>
                'Return / Refund Process Started - ' . $this->order->order_no,

            'approved' =>
                'Return Approved - ' . $this->order->order_no,

            'no_return_required' =>
                'Refund Approved - No Return Required - ' . $this->order->order_no,

            'received' =>
                'Returned Item Received - ' . $this->order->order_no,

            'rejected' =>
                'Return / Refund Request Update - ' . $this->order->order_no,

            default =>
                'Return / Refund Update - ' . $this->order->order_no,
        };

        return $this
            ->subject($subject)
            ->view('emails.orders.return-status')
            ->with([
                'order' => $this->order,
                'returnEvent' => $this->returnEvent,
                'setting' => $setting,
            ]);
    }
}