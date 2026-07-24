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
                            <div class="flex items-center gap-2 mt-1">
                                <x-text-input id="barcode" class="block w-full" type="text" name="barcode" :value="old('barcode')" required autofocus />
                                <button type="button" id="start-scanner-btn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-scan-barcode"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><path d="M8 7v10"/><path d="M12 7v10"/><path d="M17 7v10"/></svg>
                                </button>
                            </div>
                            <div id="reader-container" class="hidden mt-2 border rounded p-2">
                                <div id="reader"></div>
                                <button type="button" id="stop-scanner-btn" class="mt-2 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 w-full">Cerrar Escáner</button>
                            </div>
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

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const barcodeInput = document.getElementById('barcode');
            const nombreInput = document.getElementById('nombre');
            const startScannerBtn = document.getElementById('start-scanner-btn');
            const stopScannerBtn = document.getElementById('stop-scanner-btn');
            const readerContainer = document.getElementById('reader-container');

            let html5QrcodeScanner = null;

            startScannerBtn.addEventListener('click', function() {
                readerContainer.classList.remove('hidden');

                if (!html5QrcodeScanner) {
                    html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader",
                        {
                            fps: 30,
                            qrbox: {width: 250, height: 250},
                            useBarCodeDetectorIfSupported: true,
                            showTorchButtonIfSupported: true,
                            rememberLastUsedCamera: true
                        },
                        /* verbose= */ false);

                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                }
            });

            stopScannerBtn.addEventListener('click', function() {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear().then(() => {
                        html5QrcodeScanner = null;
                        readerContainer.classList.add('hidden');
                    }).catch(error => {
                        console.error("Failed to clear html5QrcodeScanner. ", error);
                    });
                } else {
                    readerContainer.classList.add('hidden');
                }
            });

            function onScanSuccess(decodedText, decodedResult) {
                // handle the scanned code as you like, for example:
                barcodeInput.value = decodedText;
                fetchProductName(decodedText);

                // Stop scanning after successful scan
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear().then(() => {
                        html5QrcodeScanner = null;
                        readerContainer.classList.add('hidden');
                    }).catch(error => {
                        console.error("Failed to clear html5QrcodeScanner. ", error);
                    });
                }
            }

            function onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning.
                // console.warn(`Code scan error = ${error}`);
            }

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
