<?php

use App\Models\Article;
use Database\Seeders\EditorialCalendarSeeder;

use App\Models\ExposureChallenge;
use App\Models\ExposureReport;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

Artisan::command('article:publish-black-friday', function (): int {
    $article = Article::where('editorial_key', 'plan-2026-11-1')
        ->where('title', 'Black Friday: cum verifici un magazin online înainte să plătești')
        ->whereHas('category', fn ($query) => $query->where('slug', 'fraude-online'))
        ->first();

    if ($article === null || $article->access_type !== 'public') {
        $this->error('Draftul public Black Friday așteptat nu a fost găsit.');

        return 1;
    }

    if ($article->status === 'published') {
        $this->info('Articolul Black Friday este deja publicat; nu a fost modificat.');

        return 0;
    }

    $markdownPath = database_path('data/articles/black-friday-verificare-magazin.md');
    if (! is_file($markdownPath)) {
        $this->error('Conținutul articolului Black Friday nu a fost găsit.');

        return 1;
    }

    $markdown = file_get_contents($markdownPath);
    $markdown = preg_replace('/\A# Black Friday: cum verifici un magazin online înainte să plătești\s*/u', '', $markdown, 1);
    $article->update([
        'slug' => 'black-friday-cum-verifici-un-magazin-online-inainte-sa-platesti',
        'excerpt' => 'Verifică magazinul, prețul, datele comerciantului și metoda de plată înainte de o comandă de Black Friday. Află ce semne pot indica o ofertă riscantă.',
        'content' => Illuminate\Support\Str::markdown($markdown),
        'status' => 'published',
        'published_at' => '2026-10-27',
        'editorial_date' => '2026-10-27',
        'seo_title' => 'Black Friday: cum verifici un magazin online înainte să plătești',
        'meta_description' => 'Verifică magazinul, prețul, datele comerciantului și metoda de plată înainte de o comandă de Black Friday. Află ce semne pot indica o ofertă riscantă.',
        'reading_time' => 5,
    ]);

    $this->info('Articolul Black Friday a fost publicat cu data de 27.10.2026.');

    return 0;
})->purpose('Publică o singură dată draftul editorial Black Friday cu conținutul aprobat.');

Artisan::command('exposure:prune', function () {
    $deleted = ExposureReport::where('expires_at', '<=', now())->delete();
    ExposureChallenge::where('expires_at', '<=', now())->delete();
    ExposureReport::whereIn('state', ['queued', 'processing'])->where('updated_at', '<', now()->subMinutes(20))->update(['state' => 'incomplete']);
    DB::table('exposure_usage')->where('day', '<', today()->subDays(29))->delete();
    $this->info('Rapoarte expirate șterse: '.$deleted);
})->purpose('Șterge rapoartele expirate fără a afișa date personale.');
Schedule::command('exposure:prune')->everyMinute()->withoutOverlapping();
Artisan::command('exposure:admin {id}', function () {
    $user = User::findOrFail($this->argument('id'));
    $user->forceFill(['is_exposure_admin' => true])->save();
    $this->info('Acces administrativ acordat utilizatorului selectat.');
});

Artisan::command('exposure:key', function () {
    $file = app()->environmentFilePath();
    if (! is_file($file)) {
        $this->error('Creează mai întâi fișierul .env din exemplu.');

        return 1;
    }
    $contents = file_get_contents($file);
    if (preg_match('/^EXPOSURE_HMAC_KEY=.+$/m', $contents)) {
        $this->error('Cheia există deja; nu a fost modificată.');

        return 1;
    }
    $line = 'EXPOSURE_HMAC_KEY='.bin2hex(random_bytes(32));
    $contents = preg_match('/^EXPOSURE_HMAC_KEY=.*$/m', $contents) ? preg_replace('/^EXPOSURE_HMAC_KEY=.*$/m', $line, $contents) : $contents."\n".$line."\n";
    file_put_contents($file, $contents, LOCK_EX);
    $this->info('Cheia dedicată a fost salvată în .env fără afișare. Reîncarcă configurația și workerii.');
})->purpose('Generează cheia HMAC dedicată fără a o afișa sau suprascrie.');
