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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // RELACIONES
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained(); // Si borras el producto, esto da error (Integridad)

            // DATOS HISTÓRICOS
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // PRECIO UNITARIO CONGELADO AL MOMENTO DE COMPRA
            
            // Opcional: Si quieres guardar el nombre del producto también
            // por si borran el producto original de la base de datos
            $table->string('product_name_snapshot'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
