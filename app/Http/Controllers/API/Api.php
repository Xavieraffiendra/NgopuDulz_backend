<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\CashierController;

// --- RUTE PUBLIK (Tidak butuh Token) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']); // Katalog bisa dilihat siapa saja

// --- RUTE TERLINDUNGI (Wajib pakai Bearer Token Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Profil & Logout
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Transaksi Customer
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'history']);

    // Admin: Tambah Produk
    Route::post('/products', [ProductController::class, 'store']);

    // Kasir: Manajemen Antrean
    Route::get('/cashier/orders', [CashierController::class, 'getPendingOrders']);
    Route::put('/cashier/orders/{id}/status', [CashierController::class, 'updateStatus']);
});