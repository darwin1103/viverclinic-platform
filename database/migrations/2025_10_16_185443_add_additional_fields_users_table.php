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
            $table->string('surgery')->nullable()->after('address');
            $table->string('recommendation')->nullable()->after('surgery');
            $table->boolean('terms_conditions')->default(false)->after('recommendation');
            $table->boolean('not_pregnant')->default(false)->after('terms_conditions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('surgery');
            $table->dropColumn('recommendation');
            $table->dropColumn('terms_conditions');
            $table->dropColumn('not_pregnant');
        });
    }
};
