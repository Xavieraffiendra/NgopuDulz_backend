<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;

// RUTE PUBLIK (Tidak butuh Token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// RUTE TERLINDUNGI (Wajib kirim Token dari hasil Login)
Route::middleware('auth:sanctum')->group(function () {
    
    // Rute Produk
    Route::get('/products', [ProductController::class, 'index']); // Semua bisa lihat menu
    Route::post('/products', [ProductController::class, 'store']); // Tambah menu (Nanti kita batasi khusus Admin di Android)

    // Rute Order untuk USER
    Route::post('/orders', [OrderController::class, 'store']); // Bikin pesanan
    Route::get('/my-orders', [OrderController::class, 'myOrders']); // Lihat pesanan sendiri

    // Rute Order untuk KASIR
    Route::get('/cashier/orders', [OrderController::class, 'indexCashier']); // Lihat antrean
    Route::put('/cashier/orders/{id}/status', [OrderController::class, 'updateStatus']); // Ubah status pesanan

});