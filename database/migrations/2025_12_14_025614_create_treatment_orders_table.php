<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('contracted_treatment_id')->constrained()->onDelete('cascade');

            // Totales
            $table->decimal('total', 12, 2);
            $table->string('status')->default('PENDING'); // PENDING, PAID, CANCELED

            // Detalles de Pago
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable(); // APPROVED, DECLINED, PENDING
            $table->string('payment_reference')->nullable(); // Referencia de Wompi
            $table->string('bank_name')->nullable();
            $table->string('payment_source_id')->nullable();
            $table->integer('amount_in_cents')->nullable();
            $table->string('currency')->default('COP')->nullable();
            $table->string('customer_email')->nullable();

            // Campos específicos
            $table->string('payment_description', 255)->nullable(); // Aumentado a 255
            $table->string('payment_receipt')->nullable(); // Ruta del archivo

            // Meta data
            $table->json('paid_installments_ids')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_orders');
    }
};
