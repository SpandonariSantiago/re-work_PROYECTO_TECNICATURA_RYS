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
        'quantity',
        'price', // Recuerda: Precio congelado
        'product_name_snapshot'
    ];

    // RELACIÓN 1: Pertenece a una Orden
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // RELACIÓN 2: Pertenece a un Producto (Para saber el nombre, foto, etc)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
