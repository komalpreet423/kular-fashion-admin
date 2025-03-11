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
        Schema::create('product_quantities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('product_color_id')->index();
            $table->unsignedBigInteger('product_size_id')->index(); 
            $table->string('sku', 25)->index(); 
            $table->integer('quantity')->default(0);
            $table->integer('total_quantity')->default(0);
            $table->string('barcode', 25)->nullable();
            $table->date('first_barcode_printed_date')->nullable();
            $table->string('manufacture_barcode', 25)->nullable();
            $table->integer('original_printed_barcodes')->default(0);
            $table->integer('total_printed_barcodes')->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_color_id')->references('id')->on('product_colors')->onDelete('cascade');
            $table->foreign('product_size_id')->references('id')->on('product_sizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_quantities');
    }
};
