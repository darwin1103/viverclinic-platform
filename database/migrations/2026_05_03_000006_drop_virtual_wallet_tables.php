<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('virtual_wallets');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No implementado intencionalmente
    }
};
