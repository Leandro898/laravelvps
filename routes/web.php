<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackupController;


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



require __DIR__.'/auth.php';
