<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Fungsi untuk User & Kasir melihat semua menu kopi
    public function index()
    {
        $products = Product::where('is_available', true)->get();
        return response()->json([
            'message' => 'Berhasil mengambil data produk',
            'data' => $products
        ]);
    }

    // Fungsi untuk Admin menambahkan menu baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price' => $request->price,
            'category' => $request->category,
            'image_url' => $request->image_url ?? '',
            'is_available' => true
        ]);

        return response()->json([
            'message' => 'Produk baru berhasil ditambahkan',
            'data' => $product
        ], 201);
    }
}