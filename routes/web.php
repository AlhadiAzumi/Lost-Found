<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DashboardController;

// Rute Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

Route::get('/catalog', [ItemController::class, 'catalog'])->name('catalog');

// Rute Dashboard Admin & Satpam
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});

// Menyesuaikan rute /dashboard bawaan Breeze
Route::middleware(['auth'])->get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');

// Wajib ada di baris paling bawah untuk memuat login, logout, dan profile.edit
require __DIR__.'/auth.php';