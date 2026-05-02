<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, registered, rewarded
            $table->unsignedSmallInteger('bonus_sessions')->default(0);
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('staff_commission', 10, 2)->nullable();
            $table->string('staff_commission_status')->nullable(); // pending, paid
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
