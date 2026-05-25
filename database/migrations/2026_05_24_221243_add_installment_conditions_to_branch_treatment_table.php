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
        Schema::table('branch_treatment', function (Blueprint $table) {
            $table->string('installment_conditions', 500)
                ->nullable()
                ->default('Cancela el 50% del tratamiento para comenzar y el otro 50% en la tercera sesión');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_treatment', function (Blueprint $table) {
            $table->dropColumn('installment_conditions');
        });
    }
};
