<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_settlement_id')->constrained('payroll_settlements')->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_bonuses');
    }
};
