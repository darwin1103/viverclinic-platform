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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('pathological_id')->nullable();
            $table->foreign('pathological_id')->references('id')->on('pathological_conditions');

            $table->unsignedBigInteger('toxicological_id')->nullable();
            $table->foreign('toxicological_id')->references('id')->on('toxicological_conditions');

            $table->unsignedBigInteger('gyneco_obstetric_id')->nullable();
            $table->foreign('gyneco_obstetric_id')->references('id')->on('gyneco_obstetric_conditions');

            $table->unsignedBigInteger('medication_id')->nullable();
            $table->foreign('medication_id')->references('id')->on('medications');

            $table->unsignedBigInteger('dietary_id')->nullable();
            $table->foreign('dietary_id')->references('id')->on('dietary_conditions');

            $table->unsignedBigInteger('treatment_id')->nullable();
            $table->foreign('treatment_id')->references('id')->on('treatments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pathological_id']);
            $table->dropColumn('pathological_id');

            $table->dropForeign(['toxicological_id']);
            $table->dropColumn('toxicological_id');

            $table->dropForeign(['gyneco_obstetric_id']);
            $table->dropColumn('gyneco_obstetric_id');

            $table->dropForeign(['medication_id']);
            $table->dropColumn('medication_id');

            $table->dropForeign(['dietary_id']);
            $table->dropColumn('dietary_id');

            $table->dropForeign(['treatment_id']);
            $table->dropColumn('treatment_id');
        });
    }
};
