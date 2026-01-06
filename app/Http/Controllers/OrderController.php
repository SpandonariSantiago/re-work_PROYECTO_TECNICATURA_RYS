<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <--- NECESARIO PARA TRANSACCIONES
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
public function store(Request $request)
    {
        // 1. VALIDACIÓN
        // El cliente nos manda la dirección y un array de objetos con ID y Cantidad
        $request->validate([
            'address' => 'required|string',
            'items' => 'required|array|min:1', // Al menos un producto
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // 2. TRANSACCIÓN (ACID)
        // Todo lo que ocurra dentro de este bloque es "Todo o Nada".
        try {
            return DB::transaction(function () use ($request) {
                
                $user = $request->user(); // El usuario autenticado (gracias a Sanctum)
                $total = 0;

                // A. CREAR LA CABECERA (ORDER)
                // Primero la creamos con total 0, luego actualizamos
                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => 'PENDING',
                    'shipping_address' => $request->address, // Aquí guardamos el texto snapshot
                    'address' => $request->address, // (Nota: Asegúrate de usar el nombre de columna correcto de tu migración, pusimos 'address' o 'shipping_address'?)
                    'total' => 0, 
                    'shipping_cost' => 50, // Ejemplo: Costo fijo o calculado
                    'notes' => $request->notes ?? null
                ]);

                // B. PROCESAR LOS ITEMS
                foreach ($request->items as $itemData) {
                    // Buscamos el producto REAL en la base de datos
                    $product = Product::findOrFail($itemData['product_id']);

                    // Chequeo de Stock (Opcional pero recomendado)
                    if ($product->stock < $itemData['quantity']) {
                        throw new \Exception("No hay suficiente stock de " . $product->name);
                    }

                    // Calculamos precio (Precio de la DB * Cantidad solicitada)
                    // NUNCA confíes en el precio que manda el frontend.
                    $price = $product->price; 
                    $subtotal = $price * $itemData['quantity'];
                    
                    // Sumamos al total general
                    $total += $subtotal;

                    // Creamos el Item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $itemData['quantity'],
                        'price' => $price, // PRECIO CONGELADO
                        'product_name_snapshot' => $product->name
                    ]);

                    // Descontamos stock
                    $product->decrement('stock', $itemData['quantity']);
                }

                // C. ACTUALIZAR TOTAL
                // Sumamos el costo de envío
                $order->update(['total' => $total + 50]);

                return response()->json([
                    'message' => 'Pedido creado exitosamente',
                    'order_id' => $order->id,
                    'total' => $order->total
                ], 201);

            }); // Fin de la transacción

        } catch (\Exception $e) {
            // Si algo falla (stock insuficiente, error de DB), 
            // Laravel hace ROLLBACK automático y cae aquí.
            return response()->json([
                'message' => 'Error al procesar el pedido',
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
    // VER MIS PEDIDOS
    public function index(Request $request)
    {
        // Magia de relaciones: Traeme mis pedidos CON sus items Y los productos
        $orders = $request->user()->orders()->with('items.product')->latest()->get();
        
        return response()->json($orders);
    }
}
