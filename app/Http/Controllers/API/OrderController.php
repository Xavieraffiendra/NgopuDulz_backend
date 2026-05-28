<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    // Fungsi Checkout Pesanan dari Android
    // Fungsi Checkout Pesanan dari Android
    public function store(Request $request)
    {
        $request->validate([
            'total_price' => 'required|integer',
            'items' => 'required|array',
            'items.*.product_id' => 'required', 
            'items.*.qty' => 'required|integer|min:1',
            'items.*.subtotal' => 'required|integer',
        ]);

        // 1. TRANSACTION DIMATIKAN
        // DB::beginTransaction();

        try {
            $order = Order::create([
                // 👇 TYPO SUDAH DIPERBAIKI 👇
                'user_id' => $request->user()?->id,
                'total_price' => $request->total_price,
                'status' => 'pending',
                'payment_method' => 'Midtrans',
                'payment_status' => 'pending'
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            // ==========================================
            // 🔥 KONEKSI KE MIDTRANS DIMULAI DI SINI 🔥
            // ==========================================
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // 👇 TAMBAHKAN 2 BARIS AJAIB INI BUAT BYPASS SSL DI WINDOWS 👇
            Config::$curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;
            Config::$curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
            Config::$curlOptions[CURLOPT_HTTPHEADER] = [];
            $params = [
                'transaction_details' => [
                    'order_id' => 'NGOPU-' . $order->id . '-' . time(), 
                    'gross_amount' => $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => $request->user()?->name ?? 'Pelanggan',
                    'email' => $request->user()?->email ?? 'pelanggan@ngopidulz.com',
                ],
            ];

            $snapUrl = Snap::createTransaction($params)->redirect_url;

            // 2. COMMIT DIMATIKAN (Wajib ditambahkan //)
            // DB::commit();

            return response()->json([
                'message' => 'Pesanan berhasil dibuat!',
                'order_id' => $order->id,
                'total_price' => $order->total_price,
                'payment_url' => $snapUrl 
            ], 201);

        } catch (\Exception $e) {
            // 3. ROLLBACK DIMATIKAN (Wajib ditambahkan //)
            // DB::rollback();

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
    public function callback(Request $request)
    {
        // 1. Verifikasi kecocokan kunci keamanan (biar nggak di-hack)
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            
            // 2. Jika statusnya sukses dibayar (settlement / capture)
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                
                // 3. Pecah ID pesanan (Dari 'NGOPU-12-123456' jadi '12' saja)
                $orderIdArray = explode('-', $request->order_id);
                $realOrderId = $orderIdArray[1];

                // 4. Cari pesanannya di database dan ubah statusnya
                $order = Order::find($realOrderId);
                if ($order) {
                    $order->update([
                        'payment_status' => 'dibayar',
                        'status' => 'diproses' // Langsung masuk antrean biar kasir bisa bikin kopinya
                    ]);
                }
            }
        }
        
        // Wajib balas Midtrans dengan 200 OK biar dia berhenti ngirim notifikasi
        return response()->json(['message' => 'Callback diterima']);
    }
}