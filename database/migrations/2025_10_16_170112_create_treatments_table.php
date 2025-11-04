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
        Schema::create('treatments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description');
            $table->boolean('active')->default(true);
            $table->string('main_image')->nullable();
            $table->unsignedInteger('sessions');
            $table->unsignedInteger('days_between_sessions');
            $table->text('terms_conditions')->nullable();
            $table->decimal('price_additional_zone', 10, 2)->default(0.00);
            $table->decimal('price_additional_mini_zone', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
