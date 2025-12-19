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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nombre del producto
        $table->text('description')->nullable(); // Descripción (puede estar vacía)
        $table->decimal('price', 8, 2); // Precio (ej: 99999.99)
        $table->integer('stock')->default(0); // Cantidad disponible
        $table->string('image_url')->nullable(); // URL de la foto (opcional por ahora)
        $table->timestamps(); // Crea automáticamente 'created_at' y 'updated_at'
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
