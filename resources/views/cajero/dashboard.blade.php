<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="dashboard-title">
            {{ __('Punto de Venta - Buscando...') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col space-y-6">
                    <!-- Datos del Cliente -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Datos del Cajero y Cliente</h3>

                        <div class="mb-4">
                            <div class="flex justify-between items-center">
                                <label for="cajero-select" class="block text-sm font-medium text-gray-700 mb-1">Cajero</label>
                                <button id="btn-add-cajero" type="button" class="text-sm text-indigo-600 hover:underline">Registrar cajero</button>
                            </div>
                            <select id="cajero-select" class="mt-1 block w-full rounded border-gray-300 px-2 py-2">
                                <option value="">Seleccione un cajero</option>
                                @foreach($cajeros as $cajero)
                                    <option value="{{ $cajero->id }}">{{ $cajero->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="cajero-register-form" class="hidden mt-3 border rounded p-3 bg-gray-50">
                            <label class="block text-sm font-medium text-gray-700">Nombre del cajero</label>
                            <input id="cajero-name" class="mt-1 mb-2 block w-full rounded border-gray-300 px-2 py-1" placeholder="Nombre completo">
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="cajero-email" class="mt-1 mb-2 block w-full rounded border-gray-300 px-2 py-1" placeholder="correo@ejemplo.com">
                            <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input id="cajero-password" type="password" class="mt-1 mb-2 block w-full rounded border-gray-300 px-2 py-1" placeholder="Mínimo 6 caracteres">
                            <div class="flex justify-end">
                                <button id="btn-register-cajero" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded text-sm">Guardar cajero</button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="tipo_documento_radio" value="SIN_DNI" class="form-radio" checked>
                                <span class="ml-2">Sin DNI</span>
                            </label>
                            <label class="inline-flex items-center ml-4">
                                <input type="radio" name="tipo_documento_radio" value="DNI" class="form-radio">
                                <span class="ml-2">Con DNI</span>
                            </label>
                            <label class="inline-flex items-center ml-4">
                                <input type="radio" name="tipo_documento_radio" value="RUC" class="form-radio">
                                <span class="ml-2">Con RUC</span>
                            </label>
                        </div>

                        <div id="client-search-container" class="hidden">
                            <label for="num_documento" class="block text-sm font-medium text-gray-700 mb-1" id="label-num-doc">Número de Documento</label>
                            <div class="flex rounded-md shadow-sm mb-2">
                                <input type="text" id="num_documento" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300">
                                <button type="button" id="btn-search-client" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                                    Buscar
                                </button>
                            </div>
                        </div>

                        <div id="client-info" class="text-sm font-medium text-green-700 bg-green-50 p-2 rounded hidden mt-2">
                            <!-- Nombre/Razón social del cliente encontrado -->
                        </div>
                        <div class="mt-3">
                            <button id="btn-toggle-register" type="button" class="text-sm text-indigo-600 hover:underline">Registrar cliente manualmente</button>
                        </div>

                        <div id="client-register-form" class="hidden mt-3 border rounded p-3 bg-gray-50">
                            <label class="block text-sm font-medium text-gray-700">Tipo de documento</label>
                            <select id="client-tipo_documento" class="mt-1 mb-2 block w-full rounded border-gray-300 px-2 py-1">
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="SIN_DNI">Sin DNI</option>
                            </select>
                            <label class="block text-sm font-medium text-gray-700">Número de documento</label>
                            <input id="client-num_documento" class="mt-1 mb-2 block w-full rounded border-gray-300 px-2 py-1" placeholder="Ej: 12345678">
                            <label class="block text-sm font-medium text-gray-700">Nombre / Razón social</label>
                            <input id="client-razon_social" class="mt-1 mb-2 block w-full rounded border-gray-300 px-2 py-1" placeholder="Nombre completo o razón social">
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="client-email" class="mt-1 mb-2 block w-full rounded border-gray-300 px-2 py-1" placeholder="correo@ejemplo.com">
                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input id="client-phone" class="mt-1 mb-2 block w-full rounded border-gray-300 px-2 py-1" placeholder="999999999">
                            <div class="flex justify-end">
                                <button id="btn-register-client" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded text-sm">Registrar</button>
                            </div>
                        </div>
                    </div>

                    <!-- Escáner y Búsqueda -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Lector de Código de Barras</h3>

                    <div id="reader" width="100%" class="mb-4 bg-gray-100 rounded"></div>

                    <div class="flex flex-col space-y-4">
                        <div>
                            <label for="manual-barcode" class="block text-sm font-medium text-gray-700">Ingresar código manualmente</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" id="manual-barcode" name="manual-barcode" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300" placeholder="Ej: 123456789">
                                <button type="button" id="btn-search" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Carrito de Compras -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col h-full">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Carrito de Compras</h3>

                    <div class="flex-grow overflow-auto mb-4 border rounded" style="min-height: 200px;">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cant</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-items" class="bg-white divide-y divide-gray-200">
                                <!-- Items will be inserted here -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-auto">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xl font-bold">Total antes de descuento:</span>
                            <span class="text-2xl font-bold text-gray-800">S/ <span id="cart-subtotal">0.00</span></span>
                        </div>
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700">Descuento por puntos</label>
                            <div class="mt-1 flex items-center justify-between rounded border border-gray-300 bg-gray-50 px-3 py-2">
                                <span id="cart-discount">S/ 0.00</span>
                                <span class="text-xs text-gray-500">Usando <span id="cart-points-used">0</span> puntos</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xl font-bold">Total a pagar:</span>
                            <span class="text-2xl font-bold text-green-600">S/ <span id="cart-total">0.00</span></span>
                        </div>
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700">Puntos a usar</label>
                            <input id="puntos-usados" type="number" min="0" step="1" class="w-full rounded border-gray-300 px-3 py-2" placeholder="0">
                            <p class="text-xs text-gray-500 mt-1">1 punto = S/ 0.10</p>
                        </div>
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700">Monto recibido</label>
                            <div class="mt-1 flex">
                                <input id="monto-recibido" type="number" step="0.01" min="0.01" class="flex-1 block w-full rounded-l-md border-gray-300 px-3 py-2" placeholder="0.00" required>
                                <div class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-white">
                                    <span class="text-sm">S/</span>
                                </div>
                            </div>
                            <div class="mt-2 text-sm">Cambio: <span id="cambio-amount">S/ 0.00</span></div>
                        </div>

                        <button type="button" id="btn-checkout" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50" disabled>
                            Cobrar e Imprimir Comprobante
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Receipt preview modal -->
        <div id="receipt-preview-modal" class="fixed inset-0 z-50 hidden bg-black/50">
            <div class="w-[360px] max-w-full bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-4 py-2 border-b">
                    <h4 class="font-semibold">Vista previa del comprobante</h4>
                    <button id="receipt-preview-close" class="text-sm text-slate-600 hover:text-slate-900">Cerrar</button>
                </div>
                <div class="p-2 bg-slate-100">
                    <iframe id="receipt-preview-iframe" src="about:blank" class="w-full h-[520px] border-0 bg-white"></iframe>
                </div>
            </div>
        </div>

        <!-- html5-qrcode CDN -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let cart = [];
            let currentClienteId = null;
            let currentClientePuntos = 0;
            let currentCajeroId = null;
            const titleEl = document.getElementById('dashboard-title');
            const cartItemsEl = document.getElementById('cart-items');
            const cartSubtotalEl = document.getElementById('cart-subtotal');
            const cartDiscountEl = document.getElementById('cart-discount');
            const cartPointsUsedEl = document.getElementById('cart-points-used');
            const cartTotalEl = document.getElementById('cart-total');
            const btnCheckout = document.getElementById('btn-checkout');
            const manualBarcodeInput = document.getElementById('manual-barcode');
            const btnSearch = document.getElementById('btn-search');

            const radioBtns = document.querySelectorAll('input[name="tipo_documento_radio"]');
            const searchContainer = document.getElementById('client-search-container');
            const numDocumentoInput = document.getElementById('num_documento');
            const btnSearchClient = document.getElementById('btn-search-client');
            const clientInfoDiv = document.getElementById('client-info');
            const labelNumDoc = document.getElementById('label-num-doc');
            const btnToggleRegister = document.getElementById('btn-toggle-register');
            const clientRegisterForm = document.getElementById('client-register-form');
            const btnRegisterClient = document.getElementById('btn-register-client');
            const clientTipoInput = document.getElementById('client-tipo_documento');
            const clientNumInput = document.getElementById('client-num_documento');
            const clientRazonInput = document.getElementById('client-razon_social');
            const clientEmailInput = document.getElementById('client-email');
            const clientPhoneInput = document.getElementById('client-phone');
            const btnAddCajero = document.getElementById('btn-add-cajero');
            const cajeroRegisterForm = document.getElementById('cajero-register-form');
            const btnRegisterCajero = document.getElementById('btn-register-cajero');
            const cajeroNameInput = document.getElementById('cajero-name');
            const cajeroEmailInput = document.getElementById('cajero-email');
            const cajeroPasswordInput = document.getElementById('cajero-password');
            const cajeroSelect = document.getElementById('cajero-select');
            const montoRecibidoInput = document.getElementById('monto-recibido');
            const puntosUsadosInput = document.getElementById('puntos-usados');
            const cambioAmountEl = document.getElementById('cambio-amount');

            puntosUsadosInput.addEventListener('input', renderCart);

            // --- Lógica del Cliente ---
            radioBtns.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    const val = e.target.value;
                    currentClienteId = null;
                    currentClientePuntos = 0;
                    clientInfoDiv.classList.add('hidden');
                    numDocumentoInput.value = '';
                    puntosUsadosInput.value = 0;
                    renderCart();

                    if (val === 'SIN_DNI') {
                        searchContainer.classList.add('hidden');
                    } else {
                        searchContainer.classList.remove('hidden');
                        labelNumDoc.textContent = val === 'DNI' ? 'Número de DNI' : 'Número de RUC';
                    }
                });
            });

            cajeroSelect.addEventListener('change', () => {
                currentCajeroId = cajeroSelect.value || null;
                renderCart();
            });

            if (btnAddCajero) {
                btnAddCajero.addEventListener('click', () => {
                    cajeroRegisterForm.classList.toggle('hidden');
                });
            }

            if (btnRegisterCajero) {
                btnRegisterCajero.addEventListener('click', async () => {
                    const name = cajeroNameInput.value.trim();
                    const email = cajeroEmailInput.value.trim();
                    const password = cajeroPasswordInput.value.trim();

                    if (!name || !email || password.length < 6) {
                        alert('Complete nombre, email y contraseña mínima de 6 caracteres.');
                        return;
                    }

                    btnRegisterCajero.disabled = true;
                    btnRegisterCajero.textContent = 'Guardando...';

                    try {
                        const response = await axios.post('/cajero/api/cajeros', {
                            name,
                            email,
                            password,
                        });

                        if (response.data.success) {
                            const option = document.createElement('option');
                            option.value = response.data.cajero.id;
                            option.textContent = response.data.cajero.name;
                            cajeroSelect.appendChild(option);
                            cajeroSelect.value = response.data.cajero.id;
                            currentCajeroId = response.data.cajero.id;
                            renderCart();
                            cajeroRegisterForm.classList.add('hidden');
                            cajeroNameInput.value = '';
                            cajeroEmailInput.value = '';
                            cajeroPasswordInput.value = '';
                        }
                    } catch (error) {
                        alert(error.response?.data?.message || 'Error al registrar cajero');
                    } finally {
                        btnRegisterCajero.disabled = false;
                        btnRegisterCajero.textContent = 'Guardar cajero';
                    }
                });
            }

            btnSearchClient.addEventListener('click', async () => {
                const tipo_documento = document.querySelector('input[name="tipo_documento_radio"]:checked').value;
                const num_documento = numDocumentoInput.value.trim();

                if (!num_documento) {
                    alert('Por favor ingrese el número de documento');
                    return;
                }

                btnSearchClient.disabled = true;
                btnSearchClient.textContent = '...';

                try {
                    const response = await axios.get(`/cajero/api/search-client?tipo_documento=${tipo_documento}&num_documento=${num_documento}`);
                    if (response.data.success) {
                        currentClienteId = response.data.cliente.id;
                        currentClientePuntos = response.data.cliente.puntos || 0;
                        clientInfoDiv.textContent = `Cliente: ${response.data.cliente.razon_social} (Puntos: ${currentClientePuntos})`;
                        clientInfoDiv.classList.remove('hidden');
                        renderCart();
                    }
                } catch (error) {
                    currentClienteId = null;
                    clientInfoDiv.classList.add('hidden');
                    alert(error.response?.data?.message || 'Error al buscar el cliente');
                } finally {
                    btnSearchClient.disabled = false;
                    btnSearchClient.textContent = 'Buscar';
                }
            });

            // Toggle manual register form
            if (btnToggleRegister) {
                btnToggleRegister.addEventListener('click', () => {
                    if (clientRegisterForm.classList.contains('hidden')) {
                        clientRegisterForm.classList.remove('hidden');
                    } else {
                        clientRegisterForm.classList.add('hidden');
                    }
                });
            }

            // Register client manually
            if (btnRegisterClient) {
                btnRegisterClient.addEventListener('click', async () => {
                    const tipo_documento = clientTipoInput.value || document.querySelector('input[name="tipo_documento_radio"]:checked').value;
                    let num_documento = clientNumInput.value.trim();
                    const razon_social = clientRazonInput.value.trim();
                    const email = clientEmailInput.value.trim();
                    const telefono = clientPhoneInput.value.trim();

                    if (tipo_documento !== 'SIN_DNI' && !num_documento) {
                        alert('Por favor ingrese el número de documento');
                        return;
                    }

                    if (!razon_social) {
                        alert('Por favor ingrese el nombre o razón social');
                        return;
                    }

                    try {
                        btnRegisterClient.disabled = true;
                        btnRegisterClient.textContent = 'Registrando...';
                        const payload = { tipo_documento, num_documento, razon_social, email, telefono };
                        const res = await axios.post('/cajero/api/clients', payload);
                        if (res.data.success) {
                            currentClienteId = res.data.cliente.id;
                            currentClientePuntos = res.data.cliente.puntos || 0;
                            clientInfoDiv.textContent = `Cliente: ${res.data.cliente.razon_social} (Puntos: ${currentClientePuntos})`;
                            clientInfoDiv.classList.remove('hidden');
                            clientRegisterForm.classList.add('hidden');
                            renderCart();
                        }
                    } catch (err) {
                        alert(err.response?.data?.message || 'Error al registrar cliente');
                    } finally {
                        btnRegisterClient.disabled = false;
                        btnRegisterClient.textContent = 'Registrar';
                    }
                });
            }

            // --- Escáner de Código de Barras ---
            function onScanSuccess(decodedText, decodedResult) {
                // Detener escaneos múltiples rápidos
                html5QrcodeScanner.pause(true);
                searchProduct(decodedText).finally(() => {
                    setTimeout(() => html5QrcodeScanner.resume(), 1500); // Reanudar después de 1.5s
                });
            }

            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: {width: 250, height: 150} },
                /* verbose= */ false);
            html5QrcodeScanner.render(onScanSuccess);

            // --- Búsqueda Manual ---
            btnSearch.addEventListener('click', () => {
                const barcode = manualBarcodeInput.value.trim();
                if(barcode) {
                    searchProduct(barcode);
                    manualBarcodeInput.value = '';
                }
            });

            manualBarcodeInput.addEventListener('keypress', (e) => {
                if(e.key === 'Enter') {
                    e.preventDefault();
                    btnSearch.click();
                }
            });

            // --- Lógica del POS ---
            async function searchProduct(barcode) {
                titleEl.textContent = `Punto de Venta - Buscando ${barcode}...`;
                try {
                    const response = await axios.get(`/cajero/api/search-product?barcode=${barcode}`);
                    const producto = response.data.producto;
                    titleEl.textContent = `Punto de Venta - Último: ${producto.nombre} (Stock: ${producto.stock})`;
                    addProductToCart(producto);
                } catch (error) {
                    titleEl.textContent = `Punto de Venta - Producto no encontrado`;
                    alert(error.response?.data?.message || 'Error al buscar el producto');
                }
            }

            function addProductToCart(producto) {
                const existingItem = cart.find(item => item.id === producto.id);

                if (existingItem) {
                    if (existingItem.cantidad + 1 > producto.stock) {
                        alert(`Stock insuficiente. Solo quedan ${producto.stock} unidades de ${producto.nombre}.`);
                        return;
                    }
                    existingItem.cantidad++;
                } else {
                    if (producto.stock < 1) {
                        alert(`Stock agotado para ${producto.nombre}.`);
                        return;
                    }
                    cart.push({
                        ...producto,
                        cantidad: 1
                    });
                }

                renderCart();
            }

            function removeFromCart(id) {
                cart = cart.filter(item => item.id !== id);
                renderCart();
            }

            function renderCart() {
                cartItemsEl.innerHTML = '';
                let total = 0;

                cart.forEach(item => {
                    const subtotal = item.precio * item.cantidad;
                    total += subtotal;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.nombre}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                ${item.cantidad}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">S/ ${parseFloat(item.precio).toFixed(2)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold">S/ ${subtotal.toFixed(2)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="window.removeFromCart(${item.id})" class="text-red-600 hover:text-red-900">Quitar</button>
                        </td>
                    `;
                    cartItemsEl.appendChild(tr);
                });

                const puntosUsadosRaw = Math.max(parseInt(puntosUsadosInput.value) || 0, 0);
                const maxPuntosPorTotal = Math.floor(total * 10);
                const puntosUsables = Math.min(currentClientePuntos, maxPuntosPorTotal);
                const puntosUsados = Math.min(puntosUsadosRaw, puntosUsables);
                if (puntosUsadosRaw !== puntosUsados) {
                    puntosUsadosInput.value = puntosUsados;
                }

                const descuento = parseFloat((puntosUsados * 0.10).toFixed(2));
                const totalNeto = Math.max(total - descuento, 0);

                cartSubtotalEl.textContent = total.toFixed(2);
                cartDiscountEl.textContent = `S/ ${descuento.toFixed(2)}`;
                cartPointsUsedEl.textContent = puntosUsados;
                cartTotalEl.textContent = totalNeto.toFixed(2);

                btnCheckout.disabled = cart.length === 0 || !currentCajeroId;
            }

            // Exponer al scope global para el onclick del boton Quitar
            window.removeFromCart = removeFromCart;

            // --- Cobrar ---
            btnCheckout.addEventListener('click', async () => {
                if(cart.length === 0) return;

                if (!currentCajeroId) {
                    alert('Seleccione un cajero antes de cobrar.');
                    return;
                }

                const tipoDocSeleccionado = document.querySelector('input[name="tipo_documento_radio"]:checked').value;
                if (tipoDocSeleccionado !== 'SIN_DNI' && !currentClienteId) {
                    alert('Debe buscar y seleccionar un cliente si ha marcado "Con DNI" o "Con RUC"');
                    return;
                }

                btnCheckout.disabled = true;
                btnCheckout.textContent = 'Procesando...';

                try {
                        const payload = {
                        productos: cart.map(item => ({ id: item.id, cantidad: item.cantidad })),
                        cajero_id: currentCajeroId,
                    };

                    if (currentClienteId) {
                        payload.cliente_id = currentClienteId;
                    }

                    const puntosUsados = parseInt(puntosUsadosInput.value) || 0;
                    if (puntosUsados > 0) {
                        payload.puntos_usados = puntosUsados;
                    }

                    const totalVenta = cart.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
                    const totalNeto = Math.max(totalVenta - (puntosUsados * 0.10), 0);
                    const montoRecibido = parseFloat(montoRecibidoInput.value);

                    if (totalNeto > 0) {
                        if (isNaN(montoRecibido) || montoRecibido < totalNeto) {
                            alert(`Ingrese un monto recibido igual o mayor a S/ ${totalNeto.toFixed(2)}.`);
                            return;
                        }
                        payload.pago_recibido = montoRecibido;
                    } else {
                        payload.pago_recibido = 0;
                    }

                    const response = await axios.post('/cajero/api/process-sale', payload);

                    if(response.data.success) {
                        alert('Venta registrada correctamente. Mostrando comprobante...');

                        // Mostrar vista previa en modal
                        const previewIframe = document.getElementById('receipt-preview-iframe');
                        previewIframe.src = `/cajero/receipt/html/${response.data.venta_id}`;
                        const modal = document.getElementById('receipt-preview-modal');
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');

                        // Abrir PDF en pestaña nueva para imprimir/descargar
                        window.open(`/cajero/receipt/${response.data.venta_id}`, '_blank');

                        // Mostrar cambio si existe
                        if (response.data.cambio !== null && typeof response.data.cambio !== 'undefined') {
                            const cambio = parseFloat(response.data.cambio) || 0;
                            cambioAmountEl.textContent = `S/ ${cambio.toFixed(2)}`;
                        }

                        // Limpiar carrito y campos de pago
                        cart = [];
                        renderCart();
                        montoRecibidoInput.value = '';
                        titleEl.textContent = 'Punto de Venta - Listo para nueva venta';
                    }
                } catch (error) {
                    alert(error.response?.data?.message || 'Error al procesar la venta');
                } finally {
                    btnCheckout.disabled = cart.length === 0;
                    btnCheckout.textContent = 'Cobrar e Imprimir Comprobante';
                }
            });

            // Cerrar modal de vista previa
            const previewCloseBtn = document.getElementById('receipt-preview-close');
            if (previewCloseBtn) {
                previewCloseBtn.addEventListener('click', () => {
                    const modal = document.getElementById('receipt-preview-modal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.getElementById('receipt-preview-iframe').src = 'about:blank';
                });
            }
        });
    </script>
</x-app-layout>
