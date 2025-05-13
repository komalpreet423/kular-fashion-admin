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
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->id();
            $table->string('unique_order_id')->unique();
            
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('customer_address_id')->nullable()->index();
            $table->unsignedBigInteger('coupon_id')->nullable()->index();
        
            // Payment Details
            $table->enum('payment_type', ['cod', 'credit_debit_card', 'upi', 'net_banking'])->default('cod');
            $table->string('payment_gateway')->nullable();
            $table->enum('payment_status', ['pending', 'initiated', 'authorized', 'paid', 'failed', 'refunded'])->default('pending');
            $table->unsignedTinyInteger('payment_attempts')->default(0);
            $table->string('transaction_id')->nullable();
            $table->string('payment_reference_id')->nullable();
            $table->string('payment_signature')->nullable();  
            $table->text('payment_failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('gateway_response_snapshot')->nullable();
        
            // Order Totals
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
        
            // Order Lifecycle
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'])->default('pending');
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('returned_at')->nullable();
        
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('customer_address_id')->references('id')->on('customer_addresses')->onDelete('set null');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_orders');
    }
};
