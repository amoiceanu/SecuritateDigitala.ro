<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->date('editorial_date')->nullable()->index();
            $table->string('access_type', 20)->default('public')->index();
            $table->string('editorial_key', 40)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->dropIndex(['editorial_date']);
            $table->dropIndex(['access_type']);
            $table->dropUnique(['editorial_key']);
            $table->dropColumn(['editorial_date', 'access_type', 'editorial_key']);
        });
    }
};
