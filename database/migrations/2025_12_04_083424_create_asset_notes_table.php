<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // El usuario que crea la nota
            $table->text('content'); // El contenido de la nota
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_notes');
    }
};
