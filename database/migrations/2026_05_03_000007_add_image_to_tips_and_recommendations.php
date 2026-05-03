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
        Schema::table('care_tips', function (Blueprint $table) {
            $table->string('image')->nullable()->after('title');
        });

        Schema::table('recommendations', function (Blueprint $table) {
            $table->string('image')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('care_tips', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('recommendations', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
