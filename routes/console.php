<?php

use App\Models\Article;
use Database\Seeders\EditorialCalendarSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('seo:check-indexability')->daily()->withoutOverlapping();

Artisan::command('editorial:seed', function (): void {
    $before = Article::whereNotNull('editorial_key')->count();
    app(EditorialCalendarSeeder::class)->run();
    $after = Article::whereNotNull('editorial_key')->count();
    $this->info('Drafturi editoriale adăugate: '.($after - $before).'. Înregistrările existente nu au fost modificate.');
})->purpose('Importă planul editorial noiembrie 2026 – decembrie 2027, fără duplicate sau suprascrieri.');
