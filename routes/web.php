<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\KodeAkunController;
use App\Http\Controllers\KodeBantuController;
use App\Http\Controllers\JurnalUmumController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\BukuBesarPembantuController;
use App\Http\Controllers\LabaRugiController;
use App\Http\Controllers\NeracaController;
use App\Http\Controllers\ViewerController;
use App\Http\Middleware\CheckActiveCompany;
use App\Http\Middleware\LoginMiddleware;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('login');
})->name('welcome');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Routes for staff
    Route::middleware(['auth', LoginMiddleware::class . ':staff'])->group(function () {
        Route::get('/listP', [CompanyController::class, 'index'])->name('listP');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
        Route::post('/companies/{company}/set-active', [CompanyController::class, 'setActive'])->name('companies.setActive');
        Route::post('/periods', [CompanyController::class, 'storePeriod'])->name('periods.store');

        Route::middleware(CheckActiveCompany::class)->group(function () {
            //Dashboard
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
            Route::get('/labarugi', [LabaRugiController::class, 'index'])->name('labarugi.index');
            Route::post('/labarugi', [LabaRugiController::class, 'store'])->name('labarugi.store');
            Route::put('/labarugi/{type}/{id}', [LabaRugiController::class, 'update'])->name('labarugi.update');
            Route::delete('/labarugi/{type}/{id}', [LabaRugiController::class, 'destroy'])->name('labarugi.destroy');
            Route::get('/labarugi/pdf', [LabaRugiController::class, 'generatePDF'])->name('labarugi.pdf');
            Route::get('/labarugi/account/{account_id}', [LabaRugiController::class, 'getDataByAccount'])->name('labarugi.getDataByAccount');
            Route::post('/labarugi/refresh-balances', [LabaRugiController::class, 'refreshBalances'])->name('labarugi.refreshBalances');
            
            // Neraca Routes
            Route::get('/neraca', [NeracaController::class, 'index'])->name('neraca');
            Route::get('/neraca', [NeracaController::class, 'index'])->name('neraca');
            Route::post('/neraca', [NeracaController::class, 'store']);
            Route::put('/neraca/{type}/{id}', [NeracaController::class, 'update']);
            Route::delete('/neraca/{type}/{id}', [NeracaController::class, 'destroy']);
            Route::get('/neraca/pdf', [NeracaController::class, 'generatePDF']);
        });
    });

    // Routes for viewer
    Route::middleware(['auth', LoginMiddleware::class . ':viewer'])->group(function () {
        Route::middleware(CheckActiveCompany::class)->group(function () {
            Route::get('/listPeriods', [ViewerController::class, 'listPeriods'])->name('listPeriods');
            Route::post('/periods/set', [ViewerController::class, 'setPeriod'])->name('setPeriod');

            Route::get('/vdashboard', [ViewerController::class, 'dashboard'])->name('vdashboard');

            Route::get('/vkodeakun', [viewerController::class, 'kodeakun'])->name('vkodeakun');
            Route::get('/vkodeakun/download-pdf', [ViewerController::class, 'downloadPDF'])->name('vkodeakun.download-pdf');

            Route::get('/vkodebantu', [viewerController::class, 'kodebantu'])->name('vkodebantu');
            Route::get('/vkodebantu/download-pdf', [ViewerController::class, 'downloadPDF'])->name('vkodebantu.download-pdf');

            Route::get('/vjurnalumum', [viewerController::class, 'jurnalumum'])->name('vjurnalumum');
            Route::get('/vjurnalumum/download-pdf', [ViewerController::class, 'downloadPDF'])->name('vjurnalumum.download-pdf');

            Route::get('/vbukubesar', [viewerController::class, 'bukubesar'])->name('vbukubesar');
            Route::get('/vbukubesar/transactions', [viewerController::class, 'getTransactions'])->name('vbukubesar.transactions');
            Route::get('/vbukubesar/pdf', [viewerController::class, 'downloadPDF'])->name('vbukubesar.pdf');

            Route::get('/vbukubesarpembantu', [ViewerController::class, 'bukubesarpembantu'])->name('vbukubesarpembantu');
            Route::get('/vbukubesarpembantu/transactions', [ViewerController::class, 'getTransactionsHelper']);
            Route::get('/vbukubesarpembantu/pdf', [ViewerController::class, 'downloadPDFHelper']);
            
            Route::get('/vlabarugi', [ViewerController::class, 'labarugi'])->name('vlabarugi');
            Route::get('/vlabarugi/pdf', [ViewerController::class, 'generatePDF']);

            Route::get('/vneraca', [ViewerController::class, 'neraca'])->name('vneraca');
            Route::get('/vneraca/pdf', [ViewerController::class, 'generatePDF']);
        });
    });
});
