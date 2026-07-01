<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CajeroController extends Controller
{
    public function index()
    {
        $cajeros = User::orderBy('name')->get();
        return view('cajero.dashboard', compact('cajeros'));
    }

    public function searchClient(Request $request)
    {
        $tipo_documento = $request->get('tipo_documento');
        $num_documento = $request->get('num_documento');

        if (!$tipo_documento || !$num_documento) {
            return response()->json([
                'success' => false,
                'message' => 'Faltan parámetros'
            ], 400);
        }

        // Buscar en DB primero
        $cliente = Cliente::where('num_documento', $num_documento)->first();
        if ($cliente) {
            return response()->json([
                'success' => true,
                'cliente' => $cliente
            ]);
        }

        // Buscar en API json.pe si no existe en DB
        $token = env('JSONPE_TOKEN');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token de API no configurado'
            ], 500);
        }

        $endpoint = $tipo_documento === 'DNI' ? 'dni' : 'ruc';

        $response = Http::withToken($token)->post("https://api.json.pe/api/{$endpoint}", [
            $endpoint => $num_documento
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                $nombre_o_razon_social = '';
                if ($tipo_documento === 'DNI') {
                    $nombre_o_razon_social = $data['data']['nombre_completo'] ?? '';
                } else {
                    $nombre_o_razon_social = $data['data']['nombre_o_razon_social'] ?? '';
                }

                // Concatenar el número de documento como se pidió "ambos que contengan el numero de documento"
                $razon_social_final = trim($nombre_o_razon_social) . ' - ' . $num_documento;

                $cliente = Cliente::create([
                    'tipo_documento' => $tipo_documento,
                    'num_documento' => $num_documento,
                    'razon_social' => $razon_social_final
                ]);

                return response()->json([
                    'success' => true,
                    'cliente' => $cliente
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Cliente no encontrado en API'
        ], 404);
    }

    public function searchProduct(Request $request)
    {
        $barcode = $request->get('barcode');
        $producto = Producto::where('barcode', $barcode)->first();

        if ($producto) {
            $producto->append('imagen_url');
            return response()->json([
                'success' => true,
                'producto' => $producto->toArray()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado'
        ], 404);
    }

    public function listCajeros()
    {
        $cajeros = User::orderBy('name')->get(['id', 'name', 'email']);
        return response()->json(['success' => true, 'cajeros' => $cajeros]);
    }

    public function createCajero(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:6',
        ]);

        $data['password'] = $data['password'] ? bcrypt($data['password']) : bcrypt('password');

        $cajero = User::create($data);

        return response()->json([
            'success' => true,
            'cajero' => $cajero
        ], 201);
    }

    public function createClient(Request $request)
    {
        $data = $request->validate([
            'tipo_documento' => 'required|string',
            'num_documento' => 'nullable|string',
            'razon_social' => 'required|string',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string',
        ]);

        // Normalize SIN_DNI and missing document to default public
        if (isset($data['tipo_documento']) && $data['tipo_documento'] === 'SIN_DNI') {
            $data['tipo_documento'] = 'DNI';
            $data['num_documento'] = $data['num_documento'] ?: '00000000';
        }

        if (empty($data['num_documento'])) {
            return response()->json(['success' => false, 'message' => 'El número de documento es requerido'], 400);
        }

        // Ensure uniqueness of document
        if (Cliente::where('num_documento', $data['num_documento'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Ya existe un cliente con ese número de documento'], 409);
        }

        $cliente = Cliente::create(array_merge($data, ['puntos' => 0]));

        return response()->json([
            'success' => true,
            'cliente' => $cliente
        ], 201);
    }

    public function updateClient(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $data = $request->validate([
            'tipo_documento' => 'required|string',
            'num_documento' => "required|string|unique:clientes,num_documento,{$id}",
            'razon_social' => 'required|string',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string',
            'puntos' => 'nullable|integer|min:0',
        ]);

        if ($data['tipo_documento'] === 'SIN_DNI') {
            $data['tipo_documento'] = 'DNI';
            $data['num_documento'] = $data['num_documento'] ?: '00000000';
        }

        $cliente->update($data);

        return response()->json([
            'success' => true,
            'cliente' => $cliente
        ]);
    }

    public function processSale(Request $request)
    {
        $request->validate([
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'cliente_id' => 'nullable|exists:clientes,id',
            'cajero_id' => 'required|exists:users,id',
            'pago_recibido' => 'nullable|numeric|min:0',
            'puntos_usados' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            if ($request->filled('cliente_id')) {
                $cliente = Cliente::findOrFail($request->cliente_id);
            } else {
                // Buscar o crear cliente "Público en General" (DNI: 00000000)
                $cliente = Cliente::firstOrCreate(
                    ['num_documento' => '00000000'],
                    [
                        'tipo_documento' => 'DNI',
                        'razon_social' => 'Público en General',
                    ]
                );
            }

            $tipo_comprobante = 'BOLETA';
            $serie_comprobante = 'B001';

            if ($cliente->tipo_documento === 'RUC') {
                $tipo_comprobante = 'FACTURA';
                $serie_comprobante = 'F001';
            }

            // Obtener el siguiente número de comprobante
            $ultimoComprobante = Venta::where('tipo_comprobante', $tipo_comprobante)
                ->where('serie_comprobante', $serie_comprobante)
                ->max('numero_comprobante');

            $siguienteNumero = $ultimoComprobante ? $ultimoComprobante + 1 : 1;

            // Calcular el total
            $total = 0;
            $detalles = [];
            foreach ($request->productos as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item['id']);

                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para el producto: {$producto->nombre}");
                }

                $subtotal = $producto->precio * $item['cantidad'];
                $total += $subtotal;

                $detalles[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                ];

                // Deduct stock
                $producto->stock -= $item['cantidad'];
                $producto->save();
            }

            $puntosUsados = (int) $request->input('puntos_usados', 0);
            $descuento = 0;
            $totalSinDescuento = $total;

            if ($puntosUsados > 0) {
                if (! $request->filled('cliente_id')) {
                    throw new \Exception('Debe seleccionar un cliente para usar puntos.');
                }

                if ($cliente->num_documento === '00000000') {
                    throw new \Exception('El cliente público no puede usar puntos.');
                }

                if ($puntosUsados > $cliente->puntos) {
                    throw new \Exception('El cliente no tiene suficientes puntos.');
                }

                $maxPuntosPorTotal = (int) floor($total * 10);
                if ($maxPuntosPorTotal <= 0) {
                    throw new \Exception('No puede usar puntos en una venta tan baja.');
                }

                if ($puntosUsados > $maxPuntosPorTotal) {
                    throw new \Exception("La cantidad de puntos a usar no puede exceder {$maxPuntosPorTotal} para cubrir el total de la venta.");
                }

                // 1 punto = S/ 0.10
                $descuento = round($puntosUsados * 0.10, 2);
                $total = max($total - $descuento, 0);
            }

            $pagoRecibido = $request->input('pago_recibido');
            if ($total > 0 && ($pagoRecibido === null || floatval($pagoRecibido) < floatval($total))) {
                throw new \Exception('El monto recibido debe ser al menos el total a pagar.');
            }

            $cambio = null;
            if ($pagoRecibido !== null) {
                $cambio = floatval($pagoRecibido) - floatval($total);
            }

            // Reducir puntos usados antes de guardar la venta
            if ($puntosUsados > 0 && $cliente && $cliente->num_documento !== '00000000') {
                $cliente->puntos = max(($cliente->puntos ?? 0) - $puntosUsados, 0);
            }

            // Crear Venta
            $venta = Venta::create([
                'tipo_comprobante' => $tipo_comprobante,
                'serie_comprobante' => $serie_comprobante,
                'numero_comprobante' => $siguienteNumero,
                'cliente_id' => $cliente->id,
                'cajero_id' => $request->input('cajero_id'),
                'total' => $total,
                'descuento' => $descuento,
                'puntos_usados' => $puntosUsados,
                'pago_recibido' => $pagoRecibido,
                'cambio' => $cambio,
            ]);

            // Crear VentaDetalle
            foreach ($detalles as &$detalle) {
                $detalle['venta_id'] = $venta->id;
                VentaDetalle::create($detalle);
            }

            // Manejar puntos: otorgar 1 punto por cada sol entero gastado sobre el total neto
            if ($cliente && $cliente->num_documento !== '00000000') {
                $puntosGanados = (int) floor($total);
                $cliente->puntos = ($cliente->puntos ?? 0) + $puntosGanados;
                $cliente->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'venta_id' => $venta->id,
                'cambio' => $cambio,
                'message' => 'Venta registrada exitosamente',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadReceipt($id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'cajero'])->findOrFail($id);

        $pdf = Pdf::loadView('cajero.receipt', compact('venta'));

        return $pdf->download("comprobante_{$venta->serie_comprobante}-{$venta->numero_comprobante}.pdf");
    }

    // Render HTML receipt for on-screen preview (not PDF)
    public function viewReceiptHtml($id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'cajero'])->findOrFail($id);
        return view('cajero.receipt', compact('venta'));
    }
}
