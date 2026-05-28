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
        Schema::table('promotions', function (Blueprint $table) {
            $table->foreignId('treatment_id')->nullable()->constrained('treatments')->onDelete('cascade');
            $table->foreignId('branch_treatment_id')->nullable()->constrained('branch_treatment')->onDelete('cascade');
            $table->string('discount_type')->default('percentage'); // 'percentage' or 'fixed'
            $table->string('activation_mode')->default('manual'); // 'manual' or 'scheduled'
        });

        // Drop is_active and recreate it with default false for SQLite compatibility
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->boolean('is_active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['treatment_id', 'branch_treatment_id', 'discount_type', 'activation_mode']);
            $table->dropColumn('is_active');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });
    }
};
