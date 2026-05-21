<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Kolom apa saja yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'category',
        'is_available'
    ];

    // Relasi: 1 Produk bisa ada di banyak OrderItem
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}