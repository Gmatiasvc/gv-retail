<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.productos.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Imagen -->
                        <div>
                            <x-input-label for="imagen" :value="__('Imagen del producto')" />
                            <input id="imagen" class="block mt-1 w-full" type="file" name="imagen" accept="image/*">
                            <x-input-error :messages="$errors->get('imagen')" class="mt-2" />
                        </div>

                        <!-- Barcode -->
                        <div>
                            <x-input-label for="barcode" :value="__('Código de Barras')" />
                            <x-text-input id="barcode" class="block mt-1 w-full" type="text" name="barcode" :value="old('barcode')" required autofocus />
                            <x-input-error :messages="$errors->get('barcode')" class="mt-2" />
                        </div>

                        <!-- Nombre -->
                        <div class="mt-4">
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <!-- Precio -->
                        <div class="mt-4">
                            <x-input-label for="precio" :value="__('Precio')" />
                            <x-text-input id="precio" class="block mt-1 w-full" type="number" step="0.01" name="precio" :value="old('precio')" required />
                            <x-input-error :messages="$errors->get('precio')" class="mt-2" />
                        </div>

                        <!-- Stock -->
                        <div class="mt-4">
                            <x-input-label for="stock" :value="__('Stock')" />
                            <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock" :value="old('stock')" required />
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.productos.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                Cancelar
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Guardar Producto') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const barcodeInput = document.getElementById('barcode');
            const nombreInput = document.getElementById('nombre');

            // Handle Enter key in barcode input (common for barcode scanners)
            barcodeInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent form submission
                    fetchProductName(this.value);
                }
            });

            // Handle blur event (when user clicks away or tabs out)
            barcodeInput.addEventListener('blur', function() {
                fetchProductName(this.value);
            });

            function fetchProductName(barcode) {
                barcode = barcode.trim();
                if (!barcode) return;

                // Call OpenFoodFacts API
                fetch(`https://world.openfoodfacts.org/api/v3/product/${barcode}.json`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success' && data.product && data.product.product_name) {
                            nombreInput.value = data.product.product_name;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching product from OpenFoodFacts:', error);
                    });
            }
        });
    </script>
</x-app-layout>
