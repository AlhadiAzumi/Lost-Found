<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ItemReportController;
use App\Http\Controllers\UserHistoryController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

// --- Publik / Katalog ---
Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/barang/{item}', [CatalogController::class, 'show'])->name('items.show');

// --- Autentikasi Tamu ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// --- Pengguna Terautentikasi (User & Admin) ---
Route::middleware('auth')->group(function () {
    // Pelaporan Barang
    Route::get('/lost/create', [ItemReportController::class, 'createLost'])->name('lost.create');
    Route::get('/found/create', [ItemReportController::class, 'createFound'])->name('found.create');
    Route::post('/reports', [ItemReportController::class, 'store'])->name('reports.store');

    // Pengajuan Klaim
    Route::get('/barang/{item}/claim', [ClaimController::class, 'create'])->name('claims.create');
    Route::post('/barang/{item}/claim', [ClaimController::class, 'store'])->name('claims.store');

    // Riwayat Saya
    Route::get('/my/history', [UserHistoryController::class, 'index'])->name('my.history');
});

// --- Panel Admin & Petugas Satpam ---
Route::middleware(['auth', EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::post('/reports/{item}/status', [AdminController::class, 'updateReportStatus'])->name('reports.status');
    Route::get('/claims', [AdminController::class, 'claims'])->name('claims');
    Route::post('/claims/{claim}/verify', [AdminController::class, 'verifyClaim'])->name('claims.verify');
    Route::get('/handovers', [AdminController::class, 'handovers'])->name('handovers');
    Route::post('/handovers/{claim}', [AdminController::class, 'processHandover'])->name('handovers.process');
});
