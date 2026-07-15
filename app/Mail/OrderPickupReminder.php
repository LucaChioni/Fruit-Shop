<?php

namespace App\Mail;

use App\Models\Order;
use App\Support\ShopLocation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPickupReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('items');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.pickup_reminder.subject', ['order' => $this->order->order_number]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.pickup-reminder',
            with: [
                'shop' => ShopLocation::inertia(),
            ],
        );
    }
}
