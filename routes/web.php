<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/cek-koneksi', function () {
    try {
        // Coba ping ke database
        DB::connection('mongodb')->command(['ping' => 1]);
        return "BERHASIL! Laravel sudah nyambung ke MongoDB Atlas 🎉";
    } catch (\Exception $e) {
        return "Yahh Gagal... Error-nya: " . $e->getMessage();
    }
});