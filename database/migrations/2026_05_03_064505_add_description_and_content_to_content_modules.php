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
            $table->text('description')->nullable()->after('title');
        });

        Schema::table('recommendations', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->longText('content')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('care_tips', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('recommendations', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};
