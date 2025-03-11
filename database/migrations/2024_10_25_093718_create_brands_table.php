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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable();
            $table->string('name');
            $table->string('short_name', 100)->unique()->nullable(); 
            $table->string('image')->nullable();
            $table->string('small_image')->nullable();
            $table->string('medium_image')->nullable();
            $table->string('large_image')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('margin', 5, 2)->default(50);
            $table->string('heading')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('meta_description')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
