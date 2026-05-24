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

        // 3. Admin mengedit data user / pegawai
    public function updateUser(Request $request, $id)
    {
        // Cek apakah yang login admin
        if (!$this->isAdmin()) {
            return response()->json([
                'message' => 'Akses Ditolak! Anda bukan Admin.'
            ], 403);
        }

        // Cari user
        $user = User::find($id);

        // Kalau user tidak ditemukan
        if (!$user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        // Validasi input
        $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'role' => 'sometimes|in:customer,cashier,admin',
            'status' => 'sometimes|in:active,resigned,suspended'
        ]);

        // Update nama
        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        // Update email
        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        // Update password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Update role
        if ($request->filled('role')) {
            $user->role = $request->role;
        }

        // Update status
        if ($request->filled('status')) {
            $user->status = $request->status;
        }

        // Simpan perubahan
        $user->save();

        return response()->json([
            'message' => 'Data pengguna berhasil diperbarui!',
            'data' => $user
        ]);
    }

    // 4. Admin menghapus user / pegawai
    public function deleteUser($id)
    {
        // Cek apakah yang login beneran admin
        if (!$this->isAdmin()) {
            return response()->json([
                'message' => 'Akses Ditolak! Anda bukan Admin.'
            ], 403);
        }

        // Cari usernya berdasarkan ID
        $user = User::find($id);

        // Kalau user tidak ditemukan di database
        if (!$user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        // Proses hapus dari database
        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus!'
        ]);
    }
}