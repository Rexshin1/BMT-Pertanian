<?php

use App\Http\Controllers\LaporanLabaRugiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
Route::get('/pembelian/create', [PembelianController::class, 'create'])->name('pembelian.create');
Route::get('/pembelian/master-data', [PembelianController::class, 'index'])->name('pembelian.master-data');
Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
Route::get('/penjualan/master-data', [PenjualanController::class, 'index'])->name('penjualan.master-data');
Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
Route::get('/laporan/laba-rugi', [LaporanLabaRugiController::class, 'index'])->name('laporan.laba-rugi');
Route::view('/login', 'pages.authentication-login')->name('login');
Route::view('/register', 'pages.authentication-register')->name('register');
