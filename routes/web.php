<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

use App\Http\Controllers\CajeroController;
use App\Http\Controllers\AdminController;

Route::get('/cajero/dashboard', [CajeroController::class, 'index'])->name('cajero.dashboard');
Route::get('/cajero/api/search-product', [CajeroController::class, 'searchProduct']);
Route::get('/cajero/api/search-client', [CajeroController::class, 'searchClient']);
Route::get('/cajero/api/cajeros', [CajeroController::class, 'listCajeros']);
Route::post('/cajero/api/cajeros', [CajeroController::class, 'createCajero']);
Route::post('/cajero/api/clients', [CajeroController::class, 'createClient']);
Route::put('/cajero/api/clients/{id}', [CajeroController::class, 'updateClient']);
Route::post('/cajero/api/process-sale', [CajeroController::class, 'processSale']);
Route::get('/cajero/receipt/{id}', [CajeroController::class, 'downloadReceipt'])->name('cajero.receipt');
// HTML preview of the receipt (used by the POS for on-screen preview)
Route::get('/cajero/receipt/html/{id}', [CajeroController::class, 'viewReceiptHtml'])->name('cajero.receipt.html');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('productos', AdminController::class)->except(['show']);
    Route::post('/clientes', [AdminController::class, 'storeCliente'])->name('clientes.store');
    Route::post('/cajeros', [AdminController::class, 'storeCajero'])->name('cajeros.store');
    Route::get('/reportes/ventas', [AdminController::class, 'reportesVentas'])->name('reportes.ventas');
    Route::get('/reportes/clientes', [AdminController::class, 'reportesClientes'])->name('reportes.clientes');
});
