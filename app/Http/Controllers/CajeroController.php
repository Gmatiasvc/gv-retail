<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

// POS dashboard requirements verified
class CajeroController extends Controller
{
    public function index()
    {
        return view('cajero.dashboard');
    }

    public function searchProduct(Request $request)
    {
        $barcode = $request->get('barcode');
        $producto = Producto::where('barcode', $barcode)->first();

        if ($producto) {
            return response()->json([
                'success' => true,
                'producto' => $producto
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado'
        ], 404);
    }

    public function processSale(Request $request)
    {
        $request->validate([
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Buscar o crear cliente "Público en General" (DNI: 00000000)
            $cliente = Cliente::firstOrCreate(
                ['num_documento' => '00000000'],
                [
                    'tipo_documento' => 'DNI',
                    'razon_social' => 'Público en General',
                ]
            );

            // Obtener el siguiente número de comprobante
            $ultimoComprobante = Venta::where('tipo_comprobante', 'BOLETA')
                ->where('serie_comprobante', 'B001')
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

            // Crear Venta
            $venta = Venta::create([
                'tipo_comprobante' => 'BOLETA',
                'serie_comprobante' => 'B001',
                'numero_comprobante' => $siguienteNumero,
                'cliente_id' => $cliente->id,
                'total' => $total,
            ]);

            // Crear VentaDetalle
            foreach ($detalles as &$detalle) {
                $detalle['venta_id'] = $venta->id;
                VentaDetalle::create($detalle);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'venta_id' => $venta->id,
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
        $venta = Venta::with(['cliente', 'detalles.producto'])->findOrFail($id);

        $pdf = Pdf::loadView('cajero.receipt', compact('venta'));

        return $pdf->download("comprobante_{$venta->serie_comprobante}-{$venta->numero_comprobante}.pdf");
    }
}
