<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante {{ $venta->serie_comprobante }}-{{ str_pad($venta->numero_comprobante, 6, '0', STR_PAD_LEFT) }}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 8px;
        }
        .ticket {
            width: 320px; /* approximate receipt width */
            margin: 0 auto;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .small { font-size: 11px; }
        .bold { font-weight: 700; }
        .sep { border-top: 1px dotted #000; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; }
        .item-name { font-size: 11px; }
        .totals { font-weight: 700; font-size: 13px; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="center">
            <div class="bold">{{ env('APP_NAME', 'GigaMarket') }}</div>
            <div class="small">RUC: {{ env('APP_RUC', '20748195032') }}</div>
            <div class="small">{{ env('APP_ADDRESS', 'Av. Los Laureles Nro. 452, Urb. El Palmar, Trujillo, La Libertad.') }}</div>
            <div class="small">Tlf: {{ env('APP_PHONE', '997 123 456') }}</div>
        </div>

        <div class="sep"></div>

        <div>
            <div><strong>Comprobante:</strong> {{ $venta->tipo_comprobante }} {{ $venta->serie_comprobante }}-{{ str_pad($venta->numero_comprobante, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="small">Fecha: {{ \Carbon\Carbon::parse($venta->fecha_emision)->format('d/m/Y H:i') }}</div>
            <div class="small">Cajero: {{ $venta->cajero->name ?? 'No asignado' }}</div>
            <div class="small">Cliente: {{ $venta->cliente->razon_social }}</div>
            <div class="small">Doc: {{ $venta->cliente->tipo_documento }} {{ $venta->cliente->num_documento }}</div>
        </div>

        <div class="sep"></div>

        <table>
            <tbody>
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td class="item-name">{{ \Illuminate\Support\Str::limit($detalle->producto->nombre, 30) }}</td>
                    <td class="right">{{ $detalle->cantidad }}</td>
                </tr>
                <tr>
                    <td class="small">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="right">S/ {{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="sep"></div>

        <table>
            <tr>
                <td class="small">Subtotal</td>
                <td class="right small">S/ {{ number_format($venta->total + ($venta->descuento ?? 0), 2) }}</td>
            </tr>
            @if(!empty($venta->descuento) && $venta->descuento > 0)
            <tr>
                <td class="small">Descuento</td>
                <td class="right small">- S/ {{ number_format($venta->descuento, 2) }}</td>
            </tr>
            <tr>
                <td class="small">Puntos usados</td>
                <td class="right small">{{ $venta->puntos_usados ?? 0 }}</td>
            </tr>
            @endif
            <tr>
                <td class="right totals">TOTAL</td>
                <td class="right totals">S/ {{ number_format($venta->total, 2) }}</td>
            </tr>
        </table>

        <div class="sep"></div>

        <table>
            <tr>
                <td class="small">Recibido:</td>
                <td class="right small">S/ {{ number_format($venta->pago_recibido ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="small">Cambio:</td>
                <td class="right small">S/ {{ number_format($venta->cambio ?? 0, 2) }}</td>
            </tr>
        </table>

        <div class="sep"></div>

        <div class="center small">
            Puntos cliente: {{ $venta->cliente->puntos ?? 0 }}
        </div>

        <div class="center small">
            Gracias por su compra
            <div class="small">Representante: {{ env('APP_OWNER', '') }}</div>
        </div>
    </div>
</body>
</html>
