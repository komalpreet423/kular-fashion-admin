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
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->string('recipient_email',75)->index();
            $table->string('sender_name',20);
            $table->text('message')->nullable();
            $table->date('delivery_date')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('card_number', 20)->unique();
            $table->string('transaction_id', 150);
            $table->string('payment_method', 25);
            $table->enum('status', ['active', 'redeemed', 'expired'])->default('active');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
