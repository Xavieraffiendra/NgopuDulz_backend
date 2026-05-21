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
}