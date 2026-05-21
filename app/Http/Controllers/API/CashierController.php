<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    // Menampilkan daftar semua pesanan yang belum diproses (antrean dapur/bar)
    public function getPendingOrders()
    {
        // Menarik data order bertatus pending beserta nama user pemesan dan item kopinya
        $orders = Order::with(['user:id,name', 'items.product:id,name'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil antrean pesanan kasir',
            'data' => $orders
        ]);
    }

    // Mengubah status pesanan (pending -> diproses -> selesai / dibatalkan)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai,dibatalkan',
            'payment_status' => 'string|in:pending,dibayar,gagal'
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        // Update data status operasional dan status pembayaran
        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status ?? $order->payment_status
        ]);

        return response()->json([
            'message' => 'Status pesanan berhasil diperbarui!',
            'data' => $order
        ]);
    }
}