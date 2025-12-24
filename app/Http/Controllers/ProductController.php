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
        // 1. VALIDACIÓN
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // 2. PREPARAR EL PAQUETE DE DATOS
        $data = $request->all();

        // 3. PROCESAMIENTO DE IMAGEN (Solo modificamos el array $data)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            // Agregamos la ruta al array de datos que vamos a guardar
            $data['image_url'] = $path;
        }

        // 4. CREACIÓN (UNA SOLA VEZ)
        // Usamos $data porque ya contiene la image_url si correspondía
        $product = Product::create($data);

        return response()->json([
            'message' => 'Producto creado con éxito',
            'data' => $product
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
        $product = Product::findOrFail($id);

        // 1. Validamos (Nota: 'image' es nullable porque quizás no quieren cambiarla)
        $request->validate([
            'name' => 'string|max:255',
            'price' => 'numeric|min:0',
            'stock' => 'integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // 2. Capturamos los datos
        $data = $request->all();

        // 3. Lógica de Imagen (Igual que en store, pero para update)
        if ($request->hasFile('image')) {
            // Opcional: Aquí podríamos borrar la imagen vieja del disco para no acumular basura
            // Storage::disk('public')->delete($product->image_url);

            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = $path;
        }

        // 4. Actualizamos
        $product->update($data);

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