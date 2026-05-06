<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'user_id', 
        'customer_name', 
        'items',        // Akan berisi array daftar kopi yang dibeli
        'total_price', 
        'status'        // pending, diproses, selesai
    ];
}