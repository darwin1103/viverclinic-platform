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
        Schema::table('contracted_treatments', function (Blueprint $table) {
            $table->decimal('legacy_paid_amount', 15, 2)->default(0.00)->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracted_treatments', function (Blueprint $table) {
            $table->dropColumn('legacy_paid_amount');
        });
    }
};
