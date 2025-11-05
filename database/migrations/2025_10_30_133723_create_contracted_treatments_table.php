<?php

use App\Models\Branch;
use App\Models\Treatment;
use App\Models\User;
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
        Schema::create('contracted_treatments', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Branch::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Treatment::class)->constrained()->onDelete('cascade');

            // Store a snapshot of the purchase details
            $table->json('contracted_packages')->nullable(); // Stores [{id, name, quantity, price_at_purchase}]
            $table->json('contracted_additionals')->nullable(); // Stores [{id, name, quantity, price_at_purchase}]

            // Store the final list of selected zones
            $table->json('selected_zones');

            // Financials and Status
            $table->decimal('total_price', 10, 2); // Best practice for currency
            $table->string('status')->default('Pending'); // e.g., Pending, Paid, Completed, Cancelled

            $table->smallInteger('sessions');
            $table->smallInteger('days_between_sessions');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracted_treatments');
    }
};
