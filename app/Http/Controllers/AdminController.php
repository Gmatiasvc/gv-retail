<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalProductos = Producto::count();
        $totalVentas = Venta::count();
        $ingresosTotales = Venta::sum('total');
        $clientes = Cliente::orderBy('razon_social')->get();
        $cajeros = User::orderBy('name')->get();

        return view('admin.dashboard', compact('totalProductos', 'totalVentas', 'ingresosTotales', 'clientes', 'cajeros'));
    }

    public function index()
    {
        $productos = Producto::paginate(10);
        return view('admin.productos.index', compact('productos'));
    }

    public function create()
    {
        return view('admin.productos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barcode' => 'required|unique:productos',
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($data);

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Producto $producto)
    {
        return view('admin.productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'barcode' => 'required|unique:productos,barcode,' . $producto->id,
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado exitosamente.');
    }

    public function storeCliente(Request $request)
    {
        $data = $request->validate([
            'tipo_documento' => 'required|string',
            'num_documento' => 'required|string|unique:clientes,num_documento',
            'razon_social' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
        ]);

        Cliente::create(array_merge($data, ['puntos' => 0]));

        return redirect()->route('admin.dashboard')->with('success', 'Cliente registrado correctamente.');
    }

    public function storeCajero(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Cajero registrado correctamente.');
    }

    public function reportesVentas(Request $request)
    {
        $filtro = $request->get('filtro', 'hoy'); // hoy, semana, mes, todo

        $query = Venta::query();

        if ($filtro === 'hoy') {
            $query->whereDate('created_at', today());
        } elseif ($filtro === 'semana') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filtro === 'mes') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

        $totalVentas = $query->sum('total');
        $ventas = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.reportes.ventas', compact('ventas', 'totalVentas', 'filtro'));
    }

    public function reportesClientes()
    {
        $clientes = Cliente::withSum('ventas as total_compras', 'total')
            ->orderByDesc('total_compras')
            ->paginate(15);

        return view('admin.reportes.clientes', compact('clientes'));
    }
}
