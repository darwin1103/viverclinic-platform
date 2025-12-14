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
            $table->string('payment_receipt')->nullable();

            // Meta data para saber qué se pagó (Snapshot)
            $table->json('paid_installments_ids')->nullable(); // IDs de las cuotas pagadas en esta orden

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_orders');
    }
};
