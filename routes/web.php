<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StrukController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', [AdminController::class, 'index'])
    ->middleware(['admin', 'verified', 'can:access-admin-dashboard'])
    ->name('admin.index');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/pengaturan', [SettingsController::class, 'index'])
        ->name('settings.index');

    // Cetak invoice PDF
    Route::get('/pdf-cetak/{transaction?}', [PdfController::class, 'invoice'])
        ->name('pdf.cetak');

    // Daftar produk
    Route::resource('products', ProductController::class)
        ->middlewareFor('index', 'can:viewAny,'.Product::class)
        ->middlewareFor(['create', 'store'], 'can:create,'.Product::class)
        ->middlewareFor('show', 'can:view,product')
        ->middlewareFor(['edit', 'update'], 'can:update,product')
        ->middlewareFor('destroy', 'can:delete,product');

    // Halaman kasir
    Route::middleware(['kasir', 'can:access-cashier-dashboard'])->group(function () {

        Route::get('/kasir', [KasirController::class, 'index'])
            ->name('kasir.index');

        Route::get('/kasir/search', [KasirController::class, 'searchProduct'])
            ->name('kasir.search');

        Route::post('/kasir/transaksi', [KasirController::class, 'store'])
            ->name('kasir.store');

        Route::post('/kasir/transaksi/tahan', [KasirController::class, 'hold'])
            ->name('kasir.hold');

        Route::get('/kasir/ditahan', [KasirController::class, 'held'])
            ->name('kasir.ditahan');

        Route::delete('/kasir/ditahan/{transaction}', [KasirController::class, 'destroyHeld'])
            ->middleware('can:deleteHeld,transaction')
            ->name('kasir.held.destroy');

        // Halaman struk transaksi
        Route::get('/kasir/transaksi/{transaction}/struk', [KasirController::class, 'receipt'])
            ->middleware('can:viewReceipt,transaction')
            ->name('kasir.receipt');

        // Halaman invoice transaksi
        Route::get('/transactions/{transaction}/invoice', [PdfController::class, 'invoice'])
            ->middleware('can:viewReceipt,transaction')
            ->name('transactions.invoice');

        // Cetak struk
        Route::post('/kasir/transaksi/{transaction}/struk/cetak', [StrukController::class, 'struk'])
            ->middleware('can:viewReceipt,transaction')
            ->name('kasir.receipt.print');
    });

    // Halaman invoice Blade
    Route::view('/invoice', 'invoices.invoice')
        ->name('invoice.blade');
});

// Profil pengguna
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';