<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemReportController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserHistoryController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

// --- Beranda dan Katalog Publik ---
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/barang/{item}', [CatalogController::class, 'show'])->name('items.show');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::view('/tentang', 'pages.about')->name('pages.about');
Route::view('/panduan', 'pages.guide')->name('pages.guide');
Route::view('/keamanan', 'pages.safety')->name('pages.safety');
Route::view('/privasi', 'pages.privacy')->name('pages.privacy');
Route::view('/bantuan', 'pages.contact')->name('pages.contact');
Route::view('/ketentuan', 'pages.terms')->name('pages.terms');

// --- Autentikasi Tamu ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
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

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('/verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);
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
