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
        Schema::create('listing_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listable_id');
            $table->string('listable_type');

            $table->boolean('hide_categories')->default(false)->index();
            $table->boolean('hide_filters')->default(false)->index();
            $table->boolean('show_all_filters')->default(false)->index();

            $table->json('visible_filters')->nullable();
            $table->json('collapsed_filters')->nullable();

            $table->integer('show_per_page')->default(10)->index();
            $table->boolean('show_all_products')->default(false)->index();
            $table->json('sort_options')->nullable();
            $table->json('additional_sort_options')->nullable();

            $table->index(['listable_id', 'listable_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_options');
    }
};
