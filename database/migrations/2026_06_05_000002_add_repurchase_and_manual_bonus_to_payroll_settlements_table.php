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
        Schema::table('payroll_settlements', function (Blueprint $table) {
            $table->decimal('repurchase_commissions', 12, 2)->default(0)->after('upgrade_commissions');
            $table->decimal('manual_bonus', 12, 2)->default(0)->after('repurchase_commissions');
            $table->string('manual_bonus_note')->nullable()->after('manual_bonus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_settlements', function (Blueprint $table) {
            $table->dropColumn(['repurchase_commissions', 'manual_bonus', 'manual_bonus_note']);
        });
    }
};
