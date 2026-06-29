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
            $table->decimal('commission_amount', 12, 2)->default(0)->after('base_salary');
            
            $table->dropColumn([
                'referral_commissions',
                'upgrade_commissions',
                'repurchase_commissions',
                'sales_commissions',
                'manual_bonus',
                'manual_bonus_note'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_settlements', function (Blueprint $table) {
            $table->dropColumn('commission_amount');
            
            $table->decimal('referral_commissions', 10, 2)->default(0);
            $table->decimal('upgrade_commissions', 10, 2)->default(0);
            $table->decimal('repurchase_commissions', 10, 2)->default(0);
            $table->decimal('sales_commissions', 10, 2)->default(0);
            $table->decimal('manual_bonus', 10, 2)->default(0);
            $table->text('manual_bonus_note')->nullable();
        });
    }
};
