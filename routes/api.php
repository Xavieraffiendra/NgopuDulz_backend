<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\AdminController;

// RUTE PUBLIK (Tidak butuh Token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// RUTE TERLINDUNGI (Wajib kirim Token dari hasil Login)
Route::middleware('auth:sanctum')->group(function () {

    // =========================
    // PRODUK
    // =========================
    
    // Semua user bisa lihat menu
    Route::get('/products', [ProductController::class, 'index']);

    // Tambah menu
    Route::post('/products', [ProductController::class, 'store']);



    // =========================
    // ORDER UNTUK CUSTOMER
    // =========================

    // Membuat pesanan
    Route::post('/orders', [OrderController::class, 'store']);

    // Melihat pesanan milik sendiri
    Route::get('/my-orders', [OrderController::class, 'myOrders']);



    // =========================
    // ORDER UNTUK KASIR
    // =========================

    // Melihat antrean pesanan
    Route::get('/cashier/orders', [OrderController::class, 'indexCashier']);

    // Mengubah status pesanan
    Route::put('/cashier/orders/{id}/status', [OrderController::class, 'updateStatus']);



    // =========================
    // FITUR KHUSUS ADMIN
    // =========================

    // Melihat semua user
    Route::get('/admin/users', [AdminController::class, 'getAllUsers']);

    // Menambahkan pegawai baru (cashier/admin)
    Route::post('/admin/employees', [AdminController::class, 'createEmployee']);

    // Menghapus user
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);

    Route::put('/admin/products/{id}', [ProductController::class, 'update']);
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

});