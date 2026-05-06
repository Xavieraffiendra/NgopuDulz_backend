<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Fungsi untuk Mendaftar (Register)
    public function register(Request $request)
    {
        // 1. Validasi data yang dikirim
        $request->validate([
            'name' => 'required|string',
            // Tambahkan tulisan mongodb. di depan users
            'email' => 'required|email|unique:mongodb.users,email', 
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,kasir,user'
        ]);

        // 2. Simpan user baru ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password wajib di-hash (disandikan)
            'role' => $request->role
        ]);

        // 3. Buat Token untuk Android
        $token = $user->createToken('KopiKenanganToken')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // Fungsi untuk Masuk (Login)
    public function login(Request $request)
    {
        // 1. Validasi inputan email dan password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // 3. Cek apakah email ada dan password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        // 4. Buat Token baru
        $token = $user->createToken('KopiKenanganToken')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user, 
            'token' => $token
        ], 200);
    }
}