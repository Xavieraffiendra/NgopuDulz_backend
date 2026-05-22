<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Fungsi Checkout Pesanan dari Android
    public function store(Request $request)
    {
        $request->validate([
            'total_price' => 'required|integer',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.subtotal' => 'required|integer',
        ]);

        // Mengaktifkan Database Transaction demi integritas data relasional
        DB::beginTransaction();

        try {
            // 1. Simpan data ke tabel induk (orders)
            $order = Order::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(), // Mengambil ID pengguna dari Bearer Token
                'total_price' => $request->total_price,
                'status' => 'pending',
                'payment_method' => $request->payment_method ?? 'Cash',
                'payment_status' => 'pending'
            ]);

            // 2. Looping array items untuk disimpan ke tabel anak (order_items)
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id, // Menghubungkan ID dari order yang baru dibuat
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'selected_variants' => $item['selected_variants'] ?? null, // Otomatis dicast jadi array oleh model
                    'subtotal' => $item['subtotal']
                ]);
            }

            // Jika semua proses insert sukses, kunci data di MySQL
            DB::commit();

            return response()->json([
                'message' => 'Pesanan berhasil dibuat!',
                'order_id' => $order->id,
                'total_price' => $order->total_price
            ], 201);

        } catch (\Exception $e) {
            // Jika ada satu saja yang gagal/error, batalkan semua perubahan di DB
            DB::rollback();

            return response()->json([
                'message' => 'Gagal memproses transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Riwayat Pesanan untuk sisi Pelanggan di Android
    public function myOrders()
    {
        // Mengambil data order milik user yang sedang login beserta detail item yang dibeli
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil riwayat pesanan',
            'data' => $orders
        ]);
    }
    public function indexCashier()
    {
        $orders = Order::with(['user:id,name', 'items.product:id,name'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil antrean pesanan kasir',
            'data' => $orders
        ]);
    }

    // 2. Kasir mengubah status pesanan
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