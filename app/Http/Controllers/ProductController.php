<?php

namespace App\Http\Controllers;

use App\Models\Product; // Importamos tu modelo de ayer
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Muestra una lista de todos los productos.
     * GET /api/products
     */
    public function index()
    {
        // SELECT * FROM products
        $productos = Product::all(); 
        
        // Retornamos JSON con código 200 (OK)
        return response()->json($productos, 200);
    }

    /**
     * Guarda un nuevo producto en la base de datos.
     * POST /api/products
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN (El filtro de seguridad)
        // Si no cumple esto, Laravel devuelve error 422 automáticamente
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'integer|min:0',
            'description' => 'nullable|string'
        ]);

        // 2. CREACIÓN
        // INSERT INTO products (...) VALUES (...)
        $producto = Product::create($request->all());

        // 3. RESPUESTA
        // Retornamos el objeto creado con código 201 (Created)
        return response()->json([
            'message' => 'Producto creado con éxito',
            'data' => $producto
        ], 201);
    }

     /**
     * Muestra un producto específico.
     * GET /api/products/{id}
     */
    public function show($id)
    {
        // findOrFail: Si encuentra el ID, lo guarda en $product.
        // Si NO lo encuentra, lanza un error 404 automático y detiene la ejecución.
        $product = Product::findOrFail($id);

        return response()->json($product, 200);
    }

        /**
     * Actualiza un producto existente.
     * PUT /api/products/{id}
     */
    public function update(Request $request, $id)
    {
        // 1. Buscamos el producto (o fallamos con 404)
        $product = Product::findOrFail($id);

        // 2. Validamos los datos entrantes
        // Nota: A veces no envían todos los campos, así que 'sometimes' es útil,
        // pero por ahora usaremos la misma validación estricta para simplificar.
        $request->validate([
            'name' => 'string|max:255',
            'price' => 'numeric|min:0',
            'stock' => 'integer|min:0',
            'description' => 'nullable|string'
        ]);

        // 3. Actualizamos masivamente (gracias al $fillable que pusimos ayer)
        $product->update($request->all());

        // 4. Retornamos el producto actualizado
        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'data' => $product
        ], 200);
    }

    /**
     * Elimina un producto.
     * DELETE /api/products/{id}
     */
    public function destroy($id)
    {
        // 1. Buscamos
        $product = Product::findOrFail($id);

        // 2. Eliminamos
        $product->delete();

        // 3. Respuesta (204 significa: "Éxito, pero no tengo nada que mostrarte porque ya no existe")
        return response()->json(null, 204);
    }
}