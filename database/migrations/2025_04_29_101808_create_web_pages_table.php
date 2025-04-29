<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('web_pages', function (Blueprint $table) {
        $table->id();
        $table->string('page_title');
        $table->text('page_content');
        $table->string('heading')->nullable();
        $table->string('meta_title')->nullable();
        $table->string('meta_keywords')->nullable();
        $table->text('meta_description')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_pages');
    }
};
