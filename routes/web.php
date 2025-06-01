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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
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
    // Profile routes - accessible by all authenticated users
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update-photo');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');

    // Routes for admin
    Route::middleware(['auth', LoginMiddleware::class . ':admin'])->group(function () {
        // Admin Dashboard
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/manage-accounts', [AdminController::class, 'manageAccounts'])->name('admin.manage-accounts');
        Route::post('/admin/users', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::put('/admin/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.users.reset-password');
        Route::put('/admin/users/{user}/unassign', [AdminController::class, 'unassignCompany'])->name('admin.users.unassign');
        
        // Company Management
        Route::get('/admin/companies', [AdminController::class, 'listCompanies'])->name('admin.companies');
        
        // Company Assignment
        Route::get('/admin/assign-company', [AdminController::class, 'assignCompany'])->name('admin.assign-company');
        Route::post('/admin/assign-company', [AdminController::class, 'storeAssignment'])->name('admin.assign-company.store');
        Route::get('/admin/companies/{company}/periods', [AdminController::class, 'getCompanyPeriods'])->name('admin.company-periods');
    });

    // Routes for staff
    Route::middleware(['auth', LoginMiddleware::class . ':staff'])->group(function () {
        Route::get('/listP', [CompanyController::class, 'index'])->name('listP');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
        Route::post('/companies/{company}/set-active', [CompanyController::class, 'setActive'])->name('companies.setActive');
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
        Route::post('/periods', [CompanyController::class, 'storePeriod'])->name('periods.store');

        Route::middleware(CheckActiveCompany::class)->group(function () {
            //Dashboard
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Kode Akun
            Route::get('/kodeakun', [KodeAkunController::class, 'index'])->name('kodeakun');
            Route::post('/kodeakun', [KodeAkunController::class, 'store'])->name('kodeakun.store');
            Route::put('/kodeakun/{kodeAkun}', [KodeAkunController::class, 'update'])->name('kodeakun.update');
            Route::delete('/kodeakun/{kodeAkun}', [KodeAkunController::class, 'destroy'])->name('kodeakun.destroy');
            
            // Kode Bantu
            Route::get('/kodebantu', [KodeBantuController::class, 'index'])->name('kodebantu');
            Route::post('/kodebantu', [KodeBantuController::class, 'store'])->name('kodebantu.store');
            Route::put('/kodebantu/{kodeBantu}', [KodeBantuController::class, 'update'])->name('kodebantu.update');
            Route::delete('/kodebantu/{kodeBantu}', [KodeBantuController::class, 'destroy'])->name('kodebantu.destroy');
            
            // Jurnal Umum
            Route::get('/jurnalumum', [JurnalUmumController::class, 'index'])->name('jurnalumum');
            Route::post('/jurnalumum', [JurnalUmumController::class, 'store'])->name('jurnalumum.store');
            Route::put('/jurnalumum/{jurnalUmum}', [JurnalUmumController::class, 'update'])->name('jurnalumum.update');
            Route::delete('/jurnalumum/{jurnalUmum}', [JurnalUmumController::class, 'destroy'])->name('jurnalumum.destroy');
            
            // Buku Besar
            Route::get('/bukubesar', [BukuBesarController::class, 'index'])->name('bukubesar.index');
            Route::get('/bukubesar/transactions', [BukuBesarController::class, 'getTransactions'])->name('bukubesar.transactions');
            
            // Buku Besar Pembantu
            Route::get('/bukubesarpembantu', [BukuBesarPembantuController::class, 'index'])->name('bukubesarpembantu.index');
            Route::get('/bukubesarpembantu/transactions', [BukuBesarPembantuController::class, 'getTransactions'])->name('bukubesarpembantu.transactions');
            
            // Laba Rugi Routes
            Route::get('/labarugi', [LabaRugiController::class, 'index'])->name('labarugi.index');
            Route::post('/labarugi', [LabaRugiController::class, 'store'])->name('labarugi.store');
            Route::put('/labarugi/{type}/{id}', [LabaRugiController::class, 'update'])->name('labarugi.update');
            Route::delete('/labarugi/{type}/{id}', [LabaRugiController::class, 'destroy'])->name('labarugi.destroy');
            Route::get('/labarugi/account/{account_id}', [LabaRugiController::class, 'getDataByAccount'])->name('labarugi.getDataByAccount');
            Route::post('/labarugi/refresh-balances', [LabaRugiController::class, 'refreshBalances'])->name('labarugi.refreshBalances');
            Route::get('/labarugi/get-balance/{accountId}', [LabaRugiController::class, 'getBalance']);
            
            // Neraca Routes
            Route::get('/neraca', [NeracaController::class, 'index'])->name('neraca');
            Route::post('/neraca', [NeracaController::class, 'store']);
            Route::put('/neraca/{type}/{id}', [NeracaController::class, 'update']);
            Route::delete('/neraca/{type}/{id}', [NeracaController::class, 'destroy']);
            Route::get('/neraca/get-balance/{accountId}', [NeracaController::class, 'getBalance']);

            // PDF ROUTES - Tambah ini
            Route::get('/kode-akun', [App\Http\Controllers\PdfController::class, 'downloadKodeAkunPDF'])->name('pdf.kode-akun');
            Route::get('/kode-bantu', [App\Http\Controllers\PdfController::class, 'downloadKodeBantuPDF'])->name('pdf.kode-bantu');
            Route::get('/jurnal-umum', [App\Http\Controllers\PdfController::class, 'downloadJurnalUmumPDF'])->name('pdf.jurnal-umum');
            Route::get('/buku-besar', [App\Http\Controllers\PdfController::class, 'downloadBukuBesarPDF'])->name('pdf.buku-besar');
            Route::get('/buku-besar-pembantu', [App\Http\Controllers\PdfController::class, 'downloadBukuBesarPembantuPDF'])->name('pdf.buku-besar-pembantu');
            Route::get('/laba-rugi', [App\Http\Controllers\PdfController::class, 'downloadLabaRugiPDF'])->name('pdf.laba-rugi');
            Route::get('/neraca', [App\Http\Controllers\PdfController::class, 'downloadNeracaPDF'])->name('pdf.neraca');
        });
    });

    // Routes for viewer
    Route::middleware(['auth', LoginMiddleware::class . ':viewer'])->group(function () {
        Route::middleware(CheckActiveCompany::class)->group(function () {
            Route::get('/listPeriods', [ViewerController::class, 'listPeriods'])->name('listPeriods');
            Route::post('/periods/set', [ViewerController::class, 'setPeriod'])->name('setPeriod');

            Route::get('/vdashboard', [ViewerController::class, 'dashboard'])->name('vdashboard');

            Route::get('/vkodeakun', [ViewerController::class, 'kodeakun'])->name('vkodeakun');
            Route::get('/vkodeakun/download-pdf', [ViewerController::class, 'downloadPDF'])->name('vkodeakun.download-pdf');

            Route::get('/vkodebantu', [ViewerController::class, 'kodebantu'])->name('vkodebantu');
            Route::get('/vkodebantu/download-pdf', [ViewerController::class, 'downloadPDF'])->name('vkodebantu.download-pdf');

            Route::get('/vjurnalumum', [ViewerController::class, 'jurnalumum'])->name('vjurnalumum');
            Route::get('/vjurnalumum/download-pdf', [ViewerController::class, 'downloadPDF'])->name('vjurnalumum.download-pdf');

            Route::get('/vbukubesar', [ViewerController::class, 'bukubesar'])->name('vbukubesar');
            Route::get('/vbukubesar/transactions', [ViewerController::class, 'getTransactions'])->name('vbukubesar.transactions');
            Route::get('/vbukubesar/pdf', [ViewerController::class, 'downloadPDF'])->name('vbukubesar.pdf');

            Route::get('/vbukubesarpembantu', [ViewerController::class, 'bukubesarpembantu'])->name('vbukubesarpembantu');
            Route::get('/vbukubesarpembantu/transactions', [ViewerController::class, 'getTransactionsHelper']);
            Route::get('/vbukubesarpembantu/pdf', [ViewerController::class, 'downloadPDFHelper']);
            
            Route::get('/vlabarugi', [ViewerController::class, 'labarugi'])->name('vlabarugi');
            Route::get('/vlabarugi/pdf', [ViewerController::class, 'generatePDF']);

            Route::get('/vneraca', [ViewerController::class, 'neraca'])->name('vneraca');
            Route::get('/vneraca/pdf', [ViewerController::class, 'generatePDF']);

            Route::prefix('pdf')->name('vpdf.')->group(function () {
                Route::get('/kode-akun', [ViewerController::class, 'downloadKodeAkunPDF'])->name('kode-akun');
                Route::get('/kode-bantu', [ViewerController::class, 'downloadKodeBantuPDF'])->name('kode-bantu');
                Route::get('/jurnal-umum', [ViewerController::class, 'downloadJurnalUmumPDF'])->name('jurnal-umum');
                Route::get('/buku-besar', [ViewerController::class, 'downloadBukuBesarPDF'])->name('buku-besar');
                Route::get('/buku-besar-pembantu', [ViewerController::class, 'downloadBukuBesarPembantuPDF'])->name('buku-besar-pembantu');
                Route::get('/laba-rugi', [ViewerController::class, 'downloadLabaRugiPDF'])->name('laba-rugi');
                Route::get('/neraca', [ViewerController::class, 'downloadNeracaPDF'])->name('neraca');
            });
        });
    });
});