<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'qty',
        'selected_variants',
        'subtotal'
    ];

    // KUNCI PENTING: Mengubah otomatis data JSON string di DB menjadi Array di Laravel
    protected $casts = [
        'selected_variants' => 'array',
    ];

    // Relasi: OrderItem ini milik Order nomor berapa?
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi: OrderItem ini beli Produk apa?
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}