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
        Schema::create('customer_order_items', function (Blueprint $table) {
            $table->id();
        
            $table->unsignedBigInteger('customer_order_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('variant_id')->nullable()->index();
        
            $table->decimal('actual_rate', 10, 2)->default(0);
            $table->decimal('offered_rate', 10, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0);
        
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('customer_order_id')->references('id')->on('customer_orders')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('variant_id')->references('id')->on('product_quantities')->onDelete('set null');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_order_items');
    }
};
