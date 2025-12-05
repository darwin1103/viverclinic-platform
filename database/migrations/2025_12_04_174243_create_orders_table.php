<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de Ordenes
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade'); // Para reporte por sucursal

            // Totales
            $table->decimal('total', 12, 2);
            $table->string('status')->default('PENDING'); // PENDING, PAID, DELIVERED, CANCELED

            // Detalles de Pago (Nullables como solicitado)
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_reference')->nullable(); // Referencia interna o de pasarela
            $table->string('bank_name')->nullable();
            $table->string('payment_source_id')->nullable();
            $table->integer('amount_in_cents')->nullable();
            $table->string('currency')->default('COP')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('acceptance_token')->nullable();
            $table->boolean('is_juridical')->default(0)->nullable(); // 0: Natural, 1: Juridica
            $table->string('document_type')->nullable(); // CC, NIT
            $table->string('document_number')->nullable();
            $table->string('financial_institution_code')->nullable();
            $table->string('payment_description', 30)->nullable();

            $table->timestamps();
        });

        // Tabla de Items de la Orden
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products'); // No cascade delete para mantener historial
            $table->string('product_name'); // Guardamos el nombre por si cambia el producto
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
