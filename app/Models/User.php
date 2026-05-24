<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Menentukan hak akses ('customer', 'cashier', 'admin')
        'status',
    ];

    /**
     * Kolom yang harus disembunyikan saat data diubah menjadi JSON (Keamanan).
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Mengubah format tipe data secara otomatis (Casting).
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Otomatis mengamankan password baru saat di-input
    ];

    /**
     * Relasi Relasional: 1 User bisa memiliki BANYAK Order (Transaksi).
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}