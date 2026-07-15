<?php

namespace App\Mail;

use App\Models\Order;
use App\Support\ShopLocation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('items', 'user');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuovo ordine '.$this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.placed',
            with: [
                'shop' => ShopLocation::inertia(),
            ],
        );
    }
}
