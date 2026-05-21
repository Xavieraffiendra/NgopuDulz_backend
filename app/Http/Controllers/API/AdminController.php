<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Fungsi bantuan untuk ngecek apakah yang login beneran admin
    private function isAdmin()
    {
        return auth()->user()->role === 'admin';
    }

    // 1. Admin melihat semua data user (Customer, Kasir, Admin lain)
    public function getAllUsers()
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Akses Ditolak! Anda bukan Admin.'], 403);
        }

        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Berhasil mengambil seluruh data pengguna',
            'data' => $users
        ]);
    }

    // 2. Admin mendaftarkan pegawai baru (Kasir / Admin)
    public function createEmployee(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Akses Ditolak! Anda bukan Admin.'], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:cashier,admin' // Hanya boleh daftarin kasir/admin
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return response()->json([
            'message' => 'Pegawai baru berhasil didaftarkan!',
            'data' => $user
        ], 201);
    }
}