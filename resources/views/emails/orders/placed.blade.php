<h1>Nuovo ordine {{ $order->order_number }}</h1>

<p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
<p><strong>Stato:</strong> {{ $order->status }}</p>
@if ($order->pickup_at)
    <p><strong>Ritiro:</strong> {{ $order->pickup_at->format('d/m/Y H:i') }}</p>
@endif
@if (! empty($shop['address']))
    <p><strong>Indirizzo negozio:</strong> {{ $shop['address'] }}</p>
    @if (! empty($shop['mapsUrl']))
        <p><a href="{{ $shop['mapsUrl'] }}">Apri la destinazione su Google Maps</a></p>
    @endif
@endif
<p><strong>Totale:</strong> {{ $order->total_amount }} €</p>

@if ($order->notes)
    <p><strong>Note:</strong> {{ $order->notes }}</p>
@endif

<h2>Prodotti</h2>

<ul>
    @foreach ($order->items as $item)
        <li>
            {{ $item->product_name }}: {{ $item->quantity }} {{ $item->unit_type }} × {{ $item->unit_price }} € = {{ $item->line_total }} €
        </li>
    @endforeach
</ul>
