<h1>Promemoria ritiro ordine {{ $order->order_number }}</h1>

<p>Il tuo ordine sarà pronto per il ritiro tra circa un'ora.</p>

@if ($order->pickup_at)
    <p><strong>Ritiro previsto:</strong> {{ $order->pickup_at->format('d/m/Y H:i') }}</p>
@endif

@if (! empty($shop['address']))
    <p><strong>Indirizzo negozio:</strong> {{ $shop['address'] }}</p>
    @if (! empty($shop['mapsUrl']))
        <p><a href="{{ $shop['mapsUrl'] }}">Apri la destinazione su Google Maps</a></p>
    @endif
@endif

<p><strong>Totale:</strong> {{ $order->total_amount }} €</p>
