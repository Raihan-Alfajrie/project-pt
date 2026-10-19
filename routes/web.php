<?php

use App\Http\Controllers\AlatController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumentasiController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

// Authenticated internal routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Modul Alat (CRUD)
    Route::get('/alat', [AlatController::class, 'index'])->name('alat.index');
    Route::post('/alat', [AlatController::class, 'store'])->name('alat.store');
    Route::put('/alat/{alat}', [AlatController::class, 'update'])->name('alat.update');
    Route::delete('/alat/{alat}', [AlatController::class, 'destroy'])->name('alat.destroy');

    // Modul Lokasi (CRUD)
    Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
    Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
    Route::put('/lokasi/{lokasi}', [LokasiController::class, 'update'])->name('lokasi.update');
    Route::delete('/lokasi/{lokasi}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');
    // Modul Jadwal (CRUD)
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::put('/jadwal/{jadwal}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    // Modul Import / Export (Khusus Administrator)
    Route::get('/import-export', [ImportExportController::class, 'index'])->name('import-export.index');
    Route::post('/import-export/count', [ImportExportController::class, 'count'])->name('import-export.count');
    Route::get('/import-export/export', [ImportExportController::class, 'export'])->name('import-export.export');
    Route::post('/import-export/export', [ImportExportController::class, 'export'])->name('import-export.export.post');
    Route::get('/import-export/template', [ImportExportController::class, 'template'])->name('import-export.template');
    Route::post('/import-export/upload-preview', [ImportExportController::class, 'uploadPreview'])->name('import-export.upload-preview');
    Route::post('/import-export/error-report', [ImportExportController::class, 'errorReport'])->name('import-export.error-report');
    Route::get('/import-export/error-report', [ImportExportController::class, 'errorReport'])->name('import-export.error-report.get');
    Route::post('/import-export/confirm', [ImportExportController::class, 'confirm'])->name('import-export.confirm');
    // Modul Manajemen User (CRUD & Hak Akses)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    // Modul Audit Log (Read-only)
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

    // Modul Dokumentasi (Legend CRUD)
    Route::get('/dokumentasi', [DokumentasiController::class, 'index'])->name('dokumentasi.index');
    Route::post('/dokumentasi/panels', [DokumentasiController::class, 'storePanel'])->name('dokumentasi.panels.store');
    Route::put('/dokumentasi/panels/{panel}', [DokumentasiController::class, 'updatePanel'])->name('dokumentasi.panels.update');
    Route::delete('/dokumentasi/panels/{panel}', [DokumentasiController::class, 'destroyPanel'])->name('dokumentasi.panels.destroy');

    Route::post('/dokumentasi/panels/{panel}/items', [DokumentasiController::class, 'storeItem'])->name('dokumentasi.items.store');
    Route::put('/dokumentasi/items/{item}', [DokumentasiController::class, 'updateItem'])->name('dokumentasi.items.update');
    Route::delete('/dokumentasi/items/{item}', [DokumentasiController::class, 'destroyItem'])->name('dokumentasi.items.destroy');
    // Modul FAQ (Dinamis & Interaktif)
    Route::get('/faq', [FaqController::class, 'index'])->name('faq');
    Route::post('/faq', [FaqController::class, 'store'])->name('faq.store');
    Route::put('/faq/{faq}', [FaqController::class, 'update'])->name('faq.update');
    Route::delete('/faq/{faq}', [FaqController::class, 'destroy'])->name('faq.destroy');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
