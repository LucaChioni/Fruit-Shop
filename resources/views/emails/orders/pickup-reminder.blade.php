<h1>{{ __('emails.pickup_reminder.heading', ['order' => $order->order_number]) }}</h1>

<p>{{ __('emails.pickup_reminder.message') }}</p>

@if ($order->pickup_at)
    <p><strong>{{ __('emails.pickup_reminder.pickup') }}:</strong> {{ $order->pickup_at->format('d/m/Y H:i') }}</p>
@endif

@if (! empty($shop['address']))
    <p><strong>{{ __('emails.pickup_reminder.shop_address') }}:</strong> {{ $shop['address'] }}</p>
    @if (! empty($shop['mapsUrl']))
        <p><a href="{{ $shop['mapsUrl'] }}">{{ __('emails.pickup_reminder.maps_link') }}</a></p>
    @endif
@endif

<p><strong>{{ __('emails.pickup_reminder.total') }}:</strong> {{ $order->total_amount }} €</p>
