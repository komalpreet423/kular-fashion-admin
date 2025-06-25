<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_pages', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('meta_description');
            $table->text('description')->nullable()->after('published_at');
            $table->text('summary')->nullable()->after('description');

            $table->string('image_small')->nullable()->after('summary');
            $table->string('image_medium')->nullable()->after('image_small');
            $table->string('image_large')->nullable()->after('image_medium');

            $table->json('rules')->nullable()->after('image_large');

            $table->boolean('hide_categories')->default(false)->after('rules');
            $table->boolean('hide_all_filters')->default(false)->after('hide_categories');
            $table->boolean('show_all_filters')->default(false)->after('hide_all_filters');
            $table->json('filters')->nullable()->after('show_all_filters');
        });
    }

    public function down(): void
    {
        Schema::table('web_pages', function (Blueprint $table) {
            $table->dropColumn([
                'published_at',
                'description',
                'summary',
                'image_small',
                'image_medium',
                'image_large',
                'rules',
                'hide_categories',
                'hide_all_filters',
                'show_all_filters',
                'filters',
               
            ]);
            
        });
    }
};
