<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // --- LA LISTA BLANCA DE SEGURIDAD ---
    // Solo estos campos pueden ser llenados masivamente
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image_url'
    ];
}
