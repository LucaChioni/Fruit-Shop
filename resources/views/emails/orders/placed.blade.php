<h1>{{ __('emails.order_placed.heading', ['order' => $order->order_number]) }}</h1>

<p><strong>{{ __('emails.order_placed.customer') }}:</strong> {{ $order->user->name }}</p>
<p><strong>{{ __('emails.order_placed.status') }}:</strong> {{ __('ui.orders.'.$order->status) }}</p>
@if ($order->pickup_at)
    <p><strong>{{ __('emails.order_placed.pickup') }}:</strong> {{ $order->pickup_at->format('d/m/Y H:i') }}</p>
@endif
@if (! empty($shop['address']))
    <p><strong>{{ __('emails.order_placed.shop_address') }}:</strong> {{ $shop['address'] }}</p>
    @if (! empty($shop['mapsUrl']))
        <p><a href="{{ $shop['mapsUrl'] }}">{{ __('emails.order_placed.maps_link') }}</a></p>
    @endif
@endif
<p><strong>{{ __('emails.order_placed.total') }}:</strong> {{ $order->total_amount }} €</p>

@if ($order->notes)
    <p><strong>{{ __('emails.order_placed.notes') }}:</strong> {{ $order->notes }}</p>
@endif

<h2>{{ __('emails.order_placed.products') }}</h2>

<ul>
    @foreach ($order->items as $item)
        <li>
            {{ \App\Data\ProductData::translatedName($item->product_name, $item->product_name_en ?? $item->product?->name_en) }}: {{ \App\Data\ProductData::displayQuantity($item->quantity, $item->unit_type) }} {{ \App\Data\ProductData::translatedUnitType($item->unit_type, $item->quantity) }} × {{ $item->unit_price }} € = {{ $item->line_total }} €
        </li>
    @endforeach
</ul>
