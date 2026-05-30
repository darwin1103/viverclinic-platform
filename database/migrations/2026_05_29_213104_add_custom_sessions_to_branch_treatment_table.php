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
            $table->boolean('custom_sessions')->default(false)->after('allow_installments');
            $table->unsignedInteger('sessions')->nullable()->after('custom_sessions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_treatment', function (Blueprint $table) {
            $table->dropColumn(['custom_sessions', 'sessions']);
        });
    }
};
