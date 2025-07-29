<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockAttributesTable extends Migration
{
    public function up()
    {
        Schema::create('block_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('text')->nullable();
            $table->longText('html')->nullable();
            $table->string('image_path',512)->nullable();
            $table->timestamps();

            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('block_attributes');
    }
}
