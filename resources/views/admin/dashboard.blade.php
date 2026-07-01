<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tarjetas de resumen -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-lg font-medium text-gray-500">Total Productos</div>
                        <div class="text-3xl font-bold">{{ $totalProductos }}</div>
                        <div class="mt-4">
                            <a href="{{ route('admin.productos.index') }}" class="text-blue-500 hover:underline">Gestionar Productos →</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-lg font-medium text-gray-500">Total Ventas</div>
                        <div class="text-3xl font-bold">{{ $totalVentas }}</div>
                        <div class="mt-4">
                            <a href="{{ route('admin.reportes.ventas') }}" class="text-blue-500 hover:underline">Ver Reportes de Ventas →</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-lg font-medium text-gray-500">Ingresos Totales</div>
                        <div class="text-3xl font-bold">${{ number_format($ingresosTotales, 2) }}</div>
                        <div class="mt-4">
                            <a href="{{ route('admin.reportes.clientes') }}" class="text-blue-500 hover:underline">Ver Top Clientes →</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Clientes Registrados</h3>
                            <p class="text-sm text-gray-500">Lista de clientes creados desde el POS o admin.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre / Razón</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puntos</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($clientes as $cliente)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cliente->id }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cliente->tipo_documento }} {{ $cliente->num_documento }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cliente->razon_social }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cliente->email ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cliente->puntos }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Registrar cliente</h4>
                        <form action="{{ route('admin.clientes.store') }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <select name="tipo_documento" class="w-full rounded border-gray-300 px-3 py-2">
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="SIN_DNI">Sin DNI</option>
                                </select>
                                <input name="num_documento" type="text" placeholder="Número de documento" class="w-full rounded border-gray-300 px-3 py-2" required>
                            </div>
                            <input name="razon_social" type="text" placeholder="Nombre o razón social" class="w-full rounded border-gray-300 px-3 py-2" required>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <input name="email" type="email" placeholder="Email" class="w-full rounded border-gray-300 px-3 py-2">
                                <input name="telefono" type="text" placeholder="Teléfono" class="w-full rounded border-gray-300 px-3 py-2">
                            </div>
                            <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Registrar cliente</button>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Cajeros Registrados</h3>
                            <p class="text-sm text-gray-500">Administra los cajeros que pueden procesar ventas.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cajeros as $cajero)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cajero->id }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cajero->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cajero->email }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Registrar cajero</h4>
                        <form action="{{ route('admin.cajeros.store') }}" method="POST" class="space-y-3">
                            @csrf
                            <input name="name" type="text" placeholder="Nombre completo" class="w-full rounded border-gray-300 px-3 py-2" required>
                            <input name="email" type="email" placeholder="Email" class="w-full rounded border-gray-300 px-3 py-2" required>
                            <input name="password" type="password" placeholder="Contraseña" class="w-full rounded border-gray-300 px-3 py-2" required>
                            <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Registrar cajero</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
