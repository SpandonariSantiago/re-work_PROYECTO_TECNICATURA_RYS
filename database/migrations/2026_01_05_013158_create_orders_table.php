<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // 1. ¿QUIÉN?
            // constrained() asume que la tabla se llama 'users'
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // 2. ¿DÓNDE? (Snapshot de datos)
            // Guardamos la dirección completa como texto o JSON.
            // NO la relacionamos con ID, porque si el usuario se muda, 
            // no queremos que cambie la dirección de un pedido viejo.
            $table->text('address'); 
            $table->string('phone')->nullable(); // Teléfono de contacto para el delivery
            $table->text('notes')->nullable();   // "El timbre no funciona"

            // 3. ¿CUÁNTO?
            $table->decimal('total', 10, 2); // 10 dígitos, 2 decimales (Estándar dinero)
            $table->decimal('shipping_cost', 10, 2)->default(0); 
            
            // 4. ¿ESTADO?
            // Usamos un ENUM para evitar estados fantasmas
            $table->enum('status', [
                'PENDING',      // Creado, sin pagar
                'PAID',         // Pagado, esperando acción
                'PREPARING',    // Cocina/Depósito armando paquete
                'SHIPPED',      // En moto/camión
                'DELIVERED',    // Entregado
                'CANCELLED'     // Muerto
            ])->default('PENDING');

            $table->string('payment_method')->default('CASH'); // CASH, CARD, STRIPE

            $table->timestamps(); // Created_at es la Order Date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
