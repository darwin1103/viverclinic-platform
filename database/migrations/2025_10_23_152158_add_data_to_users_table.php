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

            $table->date('birthday')->nullable();
            $table->string('directory')->nullable();
            $table->string('photo_profile')->nullable();
            $table->string('surgery')->nullable();
            $table->string('recommendation')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['pathological_id']);
            $table->dropForeign(['toxicological_id']);
            $table->dropForeign(['gyneco_obstetric_id']);
            $table->dropForeign(['medication_id']);
            $table->dropForeign(['dietary_id']);
            $table->dropForeign(['treatment_id']);

            // Drop the columns
            $table->dropColumn([
                'birthday',
                'pathological_id',
                'toxicological_id',
                'gyneco_obstetric_id',
                'medication_id',
                'dietary_id',
                'treatment_id',
                'directory',
                'photo_profile',
                'surgery',
                'recommendation',
            ]);
        });
    }
};
