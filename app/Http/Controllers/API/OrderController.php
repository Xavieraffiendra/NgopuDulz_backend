<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // ---------------------------------------------------
    // FITUR UNTUK USER (CUSTOMER)
    // ---------------------------------------------------

    // 1. User membuat pesanan baru
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array', // Harus berupa array (daftar belanjaan)
            'total_price' => 'required|numeric'
        ]);

        $order = Order::create([
            'user_id' => $request->user()->_id, // Mengambil ID user yang sedang login
            'customer_name' => $request->user()->name,
            'items' => $request->items,
            'total_price' => $request->total_price,
            'status' => 'pending' // Status awal pesanan masuk
        ]);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat, silakan tunggu!',
            'data' => $order
        ], 201);
    }

    // 2. User melihat riwayat pesanannya sendiri
    public function myOrders(Request $request)
    {
        // Cari pesanan yang user_id nya sama dengan user yang login
        $orders = Order::where('user_id', $request->user()->_id)->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $orders]);
    }


    // ---------------------------------------------------
    // FITUR UNTUK KASIR
    // ---------------------------------------------------

    // 3. Kasir melihat semua pesanan yang masuk (Pending / Diproses)
    public function indexCashier()
    {
        // Kasir hanya perlu melihat pesanan yang belum selesai
        $orders = Order::whereIn('status', ['pending', 'diproses'])->orderBy('created_at', 'asc')->get();
        return response()->json(['data' => $orders]);
    }

    // 4. Kasir mengubah status pesanan (Terima Pesanan / Selesai)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai'
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Status pesanan berhasil diubah menjadi: ' . $request->status,
            'data' => $order
        ]);
    }
}