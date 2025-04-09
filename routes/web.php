<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\MercadoPagoOAuthController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas para crear BACKUP

Route::get('/backup/create', [BackupController::class, 'create'])->name('backup.create');
Route::get('/backup/download/{file}', [BackupController::class, 'download'])->name('backup.download');

// Ruta para panel de backup
Route::get('/admin/backup', function () {
    return view('admin.backup');
});


// Rutas para restaurar backups
Route::get('/admin/restore', [BackupController::class, 'showRestoreForm']);
Route::post('/admin/restore', [BackupController::class, 'processRestore']);

// Rutas Oauth Mercado Pago
Route::middleware(['auth'])->group(function () {
    Route::get('/oauth/mercado-pago', [MercadoPagoOAuthController::class, 'redirectToMercadoPago'])->name('oauth.redirect');
    Route::get('/oauth/callback', [MercadoPagoOAuthController::class, 'handleCallback'])->name('oauth.callback');
});

// Rutas para boton de vinculacion de cuenta
Route::middleware(['auth'])->group(function () {
    Route::get('/mercadopago/connect', [MercadoPagoOAuthController::class, 'redirectToMercadoPago'])->name('mercadopago.connect');
    Route::get('/mercadopago/callback', [MercadoPagoOAuthController::class, 'handleCallback'])->name('mercadopago.callback');
    Route::post('/mercadopago/disconnect', [MercadoPagoOAuthController::class, 'disconnect'])->name('mercadopago.disconnect');
});

require __DIR__.'/auth.php';
