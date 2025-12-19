<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Esto crea automáticamente las rutas para index, store, update, destroy, etc.
// Rutas generadas:
// GET  /api/products       -> index
// POST /api/products       -> store
Route::apiResource('products', ProductController::class);