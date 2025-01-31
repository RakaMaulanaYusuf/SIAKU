<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\KodeAkunController;
use App\Http\Controllers\KodeBantuController;
use App\Http\Controllers\JurnalUmumController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\BukuBesarPembantuController;
use App\Http\Middleware\CheckActiveCompany;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication routes (untuk yang belum login)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (untuk yang sudah login)
Route::middleware('auth')->group(function () {
    // List Perusahaan (tidak perlu cek active company)
    Route::get('/listP', [CompanyController::class, 'index'])->name('listP');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::post('/companies/{company}/set-active', [CompanyController::class, 'setActive'])
        ->name('companies.setActive');

    // Routes yang membutuhkan company aktif
    Route::middleware(CheckActiveCompany::class)->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        
        // Kode Akun
        Route::get('/kodeakun', [KodeAkunController::class, 'index'])->name('kodeakun');
        Route::post('/kodeakun', [KodeAkunController::class, 'store'])->name('kodeakun.store');
        Route::put('/kodeakun/{kodeAkun}', [KodeAkunController::class, 'update'])->name('kodeakun.update');
        Route::delete('/kodeakun/{kodeAkun}', [KodeAkunController::class, 'destroy'])->name('kodeakun.destroy');
        Route::get('/kodeakun/download-pdf', [KodeAkunController::class, 'downloadPDF'])->name('kodeakun.download-pdf');
        
        // Kode Bantu
        Route::get('/kodebantu', [KodeBantuController::class, 'index'])->name('kodebantu');
        Route::post('/kodebantu', [KodeBantuController::class, 'store'])->name('kodebantu.store');
        Route::put('/kodebantu/{kodeBantu}', [KodeBantuController::class, 'update'])->name('kodebantu.update');
        Route::delete('/kodebantu/{kodeBantu}', [KodeBantuController::class, 'destroy'])->name('kodebantu.destroy');
        Route::get('/kodebantu/download-pdf', [KodeBantuController::class, 'downloadPDF'])->name('kodebantu.download-pdf');
        
        // Jurnal Umum
        Route::get('/jurnalumum', [JurnalUmumController::class, 'index'])->name('jurnalumum');
        Route::post('/jurnalumum', [JurnalUmumController::class, 'store'])->name('jurnalumum.store');
        Route::put('/jurnalumum/{jurnalUmum}', [JurnalUmumController::class, 'update'])->name('jurnalumum.update');
        Route::delete('/jurnalumum/{jurnalUmum}', [JurnalUmumController::class, 'destroy'])->name('jurnalumum.destroy');
        
        // Buku Besar
        Route::get('/bukubesar', [BukuBesarController::class, 'index'])->name('bukubesar.index');
        Route::get('/bukubesar/transactions', [BukuBesarController::class, 'getTransactions'])->name('bukubesar.transactions');
        Route::get('/bukubesar/pdf', [BukuBesarController::class, 'downloadPDF'])->name('bukubesar.pdf');
        
        // Buku Besar Pembantu
        Route::get('/bukubesarpembantu', [BukuBesarPembantuController::class, 'index'])->name('bukubesarpembantu.index');
        Route::get('/bukubesarpembantu/transactions', [BukuBesarPembantuController::class, 'getTransactions'])->name('bukubesarpembantu.transactions');
        Route::get('/bukubesarpembantu/pdf', [BukuBesarPembantuController::class, 'downloadPDF'])->name('bukubesarpembantu.pdf');
        
        // Laba Rugi Routes
        Route::get('/pendapatan', function () {
            return view('pendapatan');
        })->name('pendapatan');
        
        Route::get('/hpp', function () {
            return view('hpp');
        })->name('hpp');
        
        Route::get('/biayaoperasional', function () {
            return view('biayaoperasional');
        })->name('biayaoperasional');
        
        // Neraca Routes
        Route::get('/aktivalancar', function () {
            return view('aktivalancar');
        })->name('aktivalancar');
        
        Route::get('/aktivatetap', function () {
            return view('aktivatetap');
        })->name('aktivatetap');
        
        Route::get('/kewajiban', function () {
            return view('kewajiban');
        })->name('kewajiban');
        
        Route::get('/ekuitas', function () {
            return view('ekuitas');
        })->name('ekuitas');
        
        // Lainnya
        Route::get('/lainnya', function () {
            return view('lainnya');
        })->name('lainnya');
    });
});