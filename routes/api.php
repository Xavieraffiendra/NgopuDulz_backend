<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\AdminController;
use Illuminate\Http\Request;

// RUTE PUBLIK (Tidak butuh Token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// RUTE TERLINDUNGI (Wajib kirim Token dari hasil Login)
Route::middleware('auth:sanctum')->group(function () {

    // =========================
    // OTENTIKASI (AUTH)
    // =========================
    // 🔥 BARU TERCANTUM: Penting untuk menghapus token saat user keluar dari aplikasi
    Route::post('/logout', [AuthController::class, 'logout']);


    // =========================
    // PRODUK
    // =========================
    // Semua user bisa lihat semua menu
    Route::get('/products', [ProductController::class, 'index']);

    // 🔥 BARU TERCANTUM: Melihat detail satu produk berdasarkan ID
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Tambah menu (Biasanya nanti di controller divalidasi harus Admin/Kasir)
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

    // 🔥 BARU TERCANTUM: Mengedit data / status pegawai (Solusi error 405 tadi)
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);

    // Menghapus user
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);

    // Mengedit & Menghapus Produk (Sisi Admin)
    Route::put('/admin/products/{id}', [ProductController::class, 'update']);
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});