<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\TransaksiPenjualanController as KasirTransaksiPenjualanController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\KategoriBarangController as PetugasKategoriBarangController;
use App\Http\Controllers\Petugas\BarangController as PetugasBarangController;
use App\Http\Controllers\Petugas\PenerimaanBarangController as PetugasPenerimaanBarangController;
use App\Http\Controllers\Pengurus\DashboardController as PengurusDashboardController;
use App\Http\Controllers\Pengurus\KategoriBarangController as PengurusKategoriBarangController;
use App\Http\Controllers\Pengurus\BarangController as PengurusBarangController;
use App\Http\Controllers\Pengurus\PemasokController as PengurusPemasokController;
use App\Http\Controllers\Pengurus\Laporan\LaporanController as PengurusLaporanController;
use App\Http\Controllers\Pengurus\ActivityLogController as PengurusActivityLogController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kasir,petugas_persediaan,pengurus'])->group(function () {
    // Redirect root to login or dashboard based on auth
    Route::get('/', function () {
        if (auth()->check()) {
            return match (auth()->user()->role) {
                'kasir' => redirect()->route('kasir.dashboard'),
                'petugas_persediaan' => redirect()->route('petugas.dashboard'),
                'pengurus' => redirect()->route('pengurus.dashboard'),
                default => redirect()->route('login'),
            };
        }
        return redirect()->route('login');
    });
});

/*
|--------------------------------------------------------------------------
| Kasir Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', KasirDashboardController::class)->name('dashboard');

    // Transaksi Penjualan
    Route::resource('transaksi-penjualan', KasirTransaksiPenjualanController::class)
        ->parameters(['transaksi-penjualan' => 'transaksiPenjualan'])
        ->names('transaksi-penjualan')
        ->only(['index', 'create', 'store', 'show']);
});

/*
|--------------------------------------------------------------------------
| Petugas Persediaan Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:petugas_persediaan'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', PetugasDashboardController::class)->name('dashboard');

    // Kategori Barang
    Route::resource('kategori-barang', PetugasKategoriBarangController::class)
        ->parameters(['kategori-barang' => 'kategoriBarang'])
        ->names('kategori-barang');

    // Barang
    Route::resource('barang', PetugasBarangController::class)
        ->parameters(['barang' => 'barang'])
        ->names('barang');

    // Penerimaan Barang
    Route::resource('penerimaan-barang', PetugasPenerimaanBarangController::class)
        ->parameters(['penerimaan-barang' => 'penerimaanBarang'])
        ->names('penerimaan-barang')
        ->only(['index', 'create', 'store', 'show']);
});

/*
|--------------------------------------------------------------------------
| Pengurus Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pengurus'])->prefix('pengurus')->name('pengurus.')->group(function () {
    Route::get('/dashboard', PengurusDashboardController::class)->name('dashboard');

    // Kategori Barang
    Route::resource('kategori-barang', PengurusKategoriBarangController::class)
        ->parameters(['kategori-barang' => 'kategoriBarang'])
        ->names('kategori-barang');

    // Barang
    Route::resource('barang', PengurusBarangController::class)
        ->parameters(['barang' => 'barang'])
        ->names('barang');

    // Pemasok (hanya pengurus)
    Route::resource('pemasok', PengurusPemasokController::class)
        ->parameters(['pemasok' => 'pemasok'])
        ->names('pemasok');

    // Laporan
    Route::get('/laporan/penjualan', [PengurusLaporanController::class, 'penjualan'])->name('laporan.penjualan');
    Route::get('/laporan/persediaan', [PengurusLaporanController::class, 'persediaan'])->name('laporan.persediaan');

    // Activity Log (Audit Trail)
    Route::get('/activity-log', [PengurusActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/activity-log/detail', [PengurusActivityLogController::class, 'detail'])->name('activity-log.detail');
});