<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'category', // Contoh: Kopi, Non-Kopi, Snack
        'image_url', 
        'is_available' // true jika stok ada, false jika habis
    ];
}