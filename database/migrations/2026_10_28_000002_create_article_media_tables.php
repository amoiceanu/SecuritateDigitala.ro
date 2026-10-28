<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table): void {
            $table->id();
            $table->string('file_path', 500);
            $table->string('sha256', 64)->unique();
            $table->json('variants')->nullable();
            $table->string('mime_type', 100);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('image_type', 32)->default('photo');
            $table->text('source_url')->nullable();
            $table->string('photographer')->nullable();
            $table->string('license_name', 120)->nullable();
            $table->text('license_url')->nullable();
            $table->timestamps();
        });

        Schema::create('article_media_placements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('media_asset_id')->constrained('media_assets')->cascadeOnDelete();
            $table->string('position', 20);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->string('anchor_ro', 180)->nullable();
            $table->string('anchor_en', 180)->nullable();
            $table->string('alt_text', 500);
            $table->string('alt_text_en', 500)->nullable();
            $table->string('caption', 500)->nullable();
            $table->string('caption_en', 500)->nullable();
            $table->timestamps();
            $table->unique(['article_id', 'position', 'display_order'], 'article_media_position_order_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_media_placements');
        Schema::dropIfExists('media_assets');
    }
};
