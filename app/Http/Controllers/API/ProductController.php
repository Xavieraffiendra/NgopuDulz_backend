<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Menampilkan semua produk yang tersedia ke Home Android
    public function index()
    {
        $products = Product::where('is_available', true)->get();
        
        return response()->json([
            'message' => 'Berhasil mengambil daftar menu',
            'data' => $products
        ]);
    }

    // Admin menambahkan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'category' => 'required|string'
        ]);

        $product = Product::create($request->all());

        return response()->json([
            'message' => 'Menu baru berhasil ditambahkan',
            'data' => $product
        ], 201);
    }
    
    // Admin mengubah data/harga menu
    public function update(Request $request, $id)
    {
        // Pengecekan keamanan sederhana: Pastikan hanya admin yang bisa edit
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses Ditolak!'], 403);
        }

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        $product->update($request->all());

        return response()->json([
            'message' => 'Data menu berhasil diperbarui',
            'data' => $product
        ]);
    }

    // Admin menghapus menu
    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses Ditolak!'], 403);
        }

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Menu berhasil dihapus dari katalog']);
    }
}