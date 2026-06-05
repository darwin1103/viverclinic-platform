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
        Schema::create('repurchase_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contracted_treatment_id')->constrained('contracted_treatments')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('staff_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->string('commission_type');
            $table->decimal('commission_value', 12, 2)->default(0);
            $table->decimal('treatment_total', 12, 2)->default(0);
            $table->string('status')->default('approved');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repurchase_commissions');
    }
};
