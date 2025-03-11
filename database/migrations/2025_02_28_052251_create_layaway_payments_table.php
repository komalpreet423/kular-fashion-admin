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
        Schema::create('layaway_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('layaway_id')->index();
            $table->unsignedBigInteger('customer_id')->index();
            $table->string('method', 25);
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('original_amount', 15, 2)->nullable(); // For Euro
            $table->date('payment_date')->nullable();
            $table->unsignedBigInteger('sales_person_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('layaway_id')->references('id')->on('layaways')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('sales_person_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layaway_payments');
    }
};
