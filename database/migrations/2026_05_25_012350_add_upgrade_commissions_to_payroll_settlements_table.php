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
            $table->decimal('upgrade_commissions', 12, 2)->default(0)->after('referral_commissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_settlements', function (Blueprint $table) {
            $table->dropColumn('upgrade_commissions');
        });
    }
};
