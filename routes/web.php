<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Master\ApdTypeController;
use App\Http\Controllers\Master\InstrumentController;
use App\Http\Controllers\Master\ProfessionController;
use App\Http\Controllers\Master\UnitController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\WasteTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PPI Check - Aplikasi Audit & Surveilans PPI
|--------------------------------------------------------------------------
*/

// ---- Landing Page ----
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('landing');
})->name('landing');

// ---- Auth ----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---- Captcha (SVG) ----
Route::get('/captcha', CaptchaController::class)->name('captcha');

// ---- Aplikasi (wajib login) ----
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/unit/{unit}', [DashboardController::class, 'unitDetail'])->name('dashboard.unit');

    // Audit PPI (Cuci Tangan, APD, Pemilahan Sampah)
    Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');
    Route::get('/audits/create', [AuditController::class, 'create'])->name('audits.create');
    Route::post('/audits', [AuditController::class, 'store'])->name('audits.store');
    Route::get('/audits/{audit}', [AuditController::class, 'show'])->name('audits.show');
    Route::get('/audits/{audit}/pdf', [AuditController::class, 'pdf'])->name('audits.pdf');
    Route::delete('/audits/{audit}', [AuditController::class, 'destroy'])->name('audits.destroy');

    // Surveilans - Monitoring Temuan
    Route::get('/findings', [FindingController::class, 'index'])->name('findings.index');
    Route::get('/findings/{finding}', [FindingController::class, 'show'])->name('findings.show');

    // Surveilans - Tindak Lanjut (role Unit terutama)
    Route::get('/follow-ups', [FollowUpController::class, 'index'])->name('followups.index');
    Route::post('/findings/{finding}/follow-up', [FollowUpController::class, 'store'])->name('followups.store');

    // Verifikasi (Auditor / Admin)
    Route::post('/findings/{finding}/verify', [VerificationController::class, 'store'])
        ->middleware('can:verify-followup')->name('verifications.store');

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    // Profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ---- Master Data (Super Admin & Admin PPI) ----
    Route::middleware('can:manage-masters')->prefix('master')->name('masters.')->group(function () {
        Route::resource('units', UnitController::class)->except(['create', 'edit', 'show']);
        Route::resource('professions', ProfessionController::class)->except(['create', 'edit', 'show']);
        Route::resource('apd-types', ApdTypeController::class)->except(['create', 'edit', 'show']);
        Route::resource('waste-types', WasteTypeController::class)->except(['create', 'edit', 'show']);

        // Instrumen Audit (kategori + pertanyaan)
        Route::get('/instruments', [InstrumentController::class, 'index'])->name('instruments.index');
        Route::get('/instruments/{category}', [InstrumentController::class, 'show'])->name('instruments.show');
        Route::post('/instruments', [InstrumentController::class, 'storeCategory'])->name('instruments.store');
        Route::put('/instruments/{category}', [InstrumentController::class, 'updateCategory'])->name('instruments.update');
        Route::post('/instruments/{category}/questions', [InstrumentController::class, 'storeQuestion'])->name('instruments.questions.store');
        Route::put('/questions/{question}', [InstrumentController::class, 'updateQuestion'])->name('instruments.questions.update');
        Route::delete('/questions/{question}', [InstrumentController::class, 'destroyQuestion'])->name('instruments.questions.destroy');
    });

    // Kelola User (khusus Super Admin)
    Route::middleware('can:manage-users')->prefix('master/users')->name('masters.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Pengaturan (Super Admin & Admin PPI)
    Route::middleware('can:manage-masters')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

// Offline page (PWA)
Route::get('/offline', fn () => view('offline'))->name('offline');
