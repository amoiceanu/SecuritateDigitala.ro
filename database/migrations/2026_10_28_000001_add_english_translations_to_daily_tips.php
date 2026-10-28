<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_tips', function (Blueprint $table): void {
            $table->string('title_en', 180)->nullable();
            $table->text('body_en')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('daily_tips', function (Blueprint $table): void {
            $table->dropColumn(['title_en', 'body_en']);
        });
    }
};
