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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('sale_number', 50)->unique();
            $table->string('customer_name', 100)->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status', 20)->default('completed'); // completed, cancelled
            $table->text('notes')->nullable();
            $table->dateTime('sold_at');
            $table->timestamps();

            $table->index('status');
            $table->index('sold_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
