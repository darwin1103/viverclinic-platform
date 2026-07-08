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
        Schema::table('global_schedules', function (Blueprint $table) {
            $table->dropColumn(['regular_slots', 'sales_slots']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_schedules', function (Blueprint $table) {
            $table->unsignedSmallInteger('regular_slots')->default(0)->after('end_time');
            $table->unsignedSmallInteger('sales_slots')->default(0)->after('regular_slots');
        });
    }
};
