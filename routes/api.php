<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;

// --- RUTAS PÚBLICAS (Cualquiera entra) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Para ver productos, decidamos: ¿Quieres que sea público o privado?
// En una tienda real, VER productos es público. EDITAR es privado.
// Vamos a dejar el INDEX y SHOW públicos.
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);


// --- RUTAS PROTEGIDAS (Solo con Token) ---
// Todo lo que esté dentro de este grupo requiere autenticación
Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas de administración de productos (Crear, Editar, Borrar)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    
    // Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Ruta para saber "quién soy" (útil para el frontend luego)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});