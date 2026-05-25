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
        Schema::create('package_upgrades', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('contracted_treatment_id')->constrained('contracted_treatments')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            
            $table->json('old_package_data');
            
            $table->foreignId('new_package_id')->constrained('branch_treatment')->onDelete('cascade');
            $table->json('new_package_data');
            
            $table->decimal('price_difference', 12, 2);
            
            $table->foreignId('staff_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('commission_amount', 12, 2);
            $table->string('commission_type');
            $table->decimal('commission_value', 12, 2);
            
            $table->string('payment_method');
            $table->string('payment_status');
            
            $table->json('old_selected_zones')->nullable();
            $table->json('new_selected_zones')->nullable();
            
            $table->foreignId('processed_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_upgrades');
    }
};
