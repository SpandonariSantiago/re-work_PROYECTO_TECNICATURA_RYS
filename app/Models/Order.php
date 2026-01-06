<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // PROTECCIÓN MASS ASSIGNMENT (Vital)
    protected $fillable = [
        'user_id',
        'address',
        'phone',
        'notes',
        'total',
        'shipping_cost',
        'status',
        'payment_method'
    ];

    // RELACIÓN 1: Un Pedido PERTENECE a un Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // RELACIÓN 2: Un Pedido TIENE MUCHOS Items (Detalles)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
