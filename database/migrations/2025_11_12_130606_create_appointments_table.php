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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contracted_treatment_id')->constrained()->onDelete('cascade');

            $table->timestamp('schedule');
            $table->smallInteger('session_number');

            $table->unsignedBigInteger('staff_user_id')->nullable();
            $table->foreign('staff_user_id')->references('id')->on('users');

            $table->boolean('attended')->nullable();
            $table->string('review')->nullable();
            $table->smallInteger('review_score')->nullable();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
