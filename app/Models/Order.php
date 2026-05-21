<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'payment_method',
        'payment_status'
    ];

    // Relasi: 1 Order dimiliki oleh 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: 1 Order memiliki BANYAK OrderItem (Pengganti Embedded Mongo)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}