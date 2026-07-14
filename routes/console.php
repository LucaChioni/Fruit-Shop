<?php

use App\Mail\OrderPickupReminder;
use App\Models\Order;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('orders:send-pickup-reminders', function () {
    Order::query()
        ->with('user')
        ->whereNotNull('pickup_at')
        ->whereNull('pickup_reminder_sent_at')
        ->where('pickup_at', '>', now())
        ->where('pickup_at', '<=', now()->addHour())
        ->where('status', '!=', 'cancelled')
        ->get()
        ->each(function (Order $order) {
            if ($order->user?->email) {
                Mail::to($order->user->email)->send(new OrderPickupReminder($order));
            }

            $order->forceFill(['pickup_reminder_sent_at' => now()])->save();
        });
})->purpose('Send pickup reminder emails one hour before pickup time');

Schedule::command('orders:send-pickup-reminders')->everyMinute();
