<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('home_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_path'); // Store image file path
            $table->enum('type', ['slider', 'newsletter']); // Define type of image
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_images');
    }
};
