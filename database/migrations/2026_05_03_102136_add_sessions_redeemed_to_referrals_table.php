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
        Schema::table('referrals', function (Blueprint $table) {
            if (!Schema::hasColumn('referrals', 'referred_id')) {
                $table->foreignId('referred_id')->nullable()->constrained('users')->onDelete('set null')->after('referrer_id');
            }
            if (!Schema::hasColumn('referrals', 'bonus_sessions')) {
                $table->unsignedSmallInteger('bonus_sessions')->default(0)->after('status');
            }
            if (!Schema::hasColumn('referrals', 'staff_id')) {
                $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null')->after('bonus_sessions');
            }
            if (!Schema::hasColumn('referrals', 'staff_commission')) {
                $table->decimal('staff_commission', 10, 2)->nullable()->after('staff_id');
            }
            if (!Schema::hasColumn('referrals', 'staff_commission_status')) {
                $table->string('staff_commission_status')->nullable()->after('staff_commission');
            }
            if (!Schema::hasColumn('referrals', 'rewarded_at')) {
                $table->timestamp('rewarded_at')->nullable()->after('staff_commission_status');
            }
            if (!Schema::hasColumn('referrals', 'sessions_redeemed')) {
                $table->boolean('sessions_redeemed')->default(false)->after('bonus_sessions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn('sessions_redeemed');
        });
    }
};
