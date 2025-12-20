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
            // Nueva validación: Debe ser imagen (jpg, png, etc) y pesar máximo 2MB
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // 2. CAPTURA DE DATOS
        $data = $request->all();

        // --- ZONA DE DEBUG ---
        // Si esto imprime "No hay archivo", el problema es Postman/Thunder.
        if (!$request->hasFile('image')) {
            return response()->json([
                'error' => 'Laravel dice: No recibí ningún archivo llamado image.',
                'debug_headers' => $request->headers->all(),
                'debug_all' => $request->all()
            ], 400);
        }
        // ---------------------

        // 3. PROCESAMIENTO DE IMAGEN
        if ($request->hasFile('image')) {
            // Guardar en: storage/app/public/products
            // Devuelve la ruta relativa (ej: "products/foto1.jpg")
            $path = $request->file('image')->store('products', 'public');
            
            // Guardamos esa ruta en el campo 'image_url' de la base de datos
            $request->merge(['image_url' => $path]);
            $product = Product::create($request->all());
        }

        // 4. CREACIÓN
        $product = Product::create($data);

        return response()->json([
            'message' => 'Producto creado con imagen',
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