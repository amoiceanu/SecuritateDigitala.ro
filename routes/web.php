<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DailyTipController as AdminDailyTipController;
use App\Http\Controllers\Admin\GoogleIndexingController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExposureAdminController;
use App\Http\Controllers\ExposureController;
use App\Http\Controllers\PublicCategoryController;
use App\Http\Controllers\SitePagesAdminController;
use App\Http\Middleware\ExposureAccess;
use App\Http\Middleware\SiteAdmin;
use App\Services\PublicSitemap;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/', [BlogController::class, 'home'])->name('home');
Route::get('/articole', [BlogController::class, 'index'])->name('articles.index');
Route::get('/sectiuni/{category:slug}', [PublicCategoryController::class, 'show'])->name('sections.show');
Route::get('/articole/{article:slug}', [BlogController::class, 'show'])->name('articles.show');
Route::view('/despre', 'pages.about')->name('about');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'store'])->middleware('throttle:5,1')->name('admin.login.store');
Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->middleware('auth')->name('admin.logout');
Route::get('/admin', function () {
    $user = auth()->user();
    abort_unless($user?->is_admin || $user?->is_exposure_admin, 403);

    return redirect($user->is_exposure_admin ? '/admin/pagini-si-module' : '/admin/articole');
})->middleware('auth')->name('admin.dashboard');
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function (): void {
    Route::get('google-indexing', [GoogleIndexingController::class, 'index'])->name('google-indexing.index');
    Route::post('google-indexing/connect', [GoogleIndexingController::class, 'connect'])->name('google-indexing.connect');
    Route::get('google-indexing/callback', [GoogleIndexingController::class, 'callback'])->name('google-indexing.callback');
    Route::post('google-indexing/validate-sitemap', [GoogleIndexingController::class, 'validateSitemap'])->name('google-indexing.validate-sitemap');
    Route::delete('google-indexing/disconnect', [GoogleIndexingController::class, 'disconnect'])->name('google-indexing.disconnect');
    Route::post('google-indexing/sitemap', [GoogleIndexingController::class, 'submitSitemap'])->name('google-indexing.sitemap');
    Route::post('google-indexing/refresh', [GoogleIndexingController::class, 'refreshGoogleData'])->name('google-indexing.refresh');
    Route::post('google-indexing/inspect/{auditItem}', [GoogleIndexingController::class, 'inspect'])->name('google-indexing.inspect');
    Route::post('google-indexing/audit', [GoogleIndexingController::class, 'audit'])->name('google-indexing.audit');
    Route::get('google-indexing/export.csv', [GoogleIndexingController::class, 'export'])->name('google-indexing.export');
    Route::get('calendar-editorial', [\App\Http\Controllers\Admin\EditorialCalendarController::class, 'index'])->name('editorial.index');
    Route::resource('articole', AdminArticleController::class)->except('show')->parameters(['articole' => 'article']);
    Route::get('articole/{article}', [AdminArticleController::class, 'show'])->name('articole.show');
    Route::resource('sectiuni', AdminCategoryController::class)->except('show')->parameters(['sectiuni' => 'category']);
    Route::resource('sfaturi', AdminDailyTipController::class)->except('show')->parameters(['sfaturi' => 'dailyTip']);
    Route::resource('contact', AdminContactMessageController::class)->only(['index', 'show'])->parameters(['contact' => 'contactMessage']);
});
Route::view('/politica-de-confidentialitate', 'pages.privacy')->name('privacy');
Route::view('/politica-cookies', 'pages.cookies')->name('cookies');
Route::view('/termeni-si-conditii', 'pages.terms')->name('terms');

Route::get('/sitemap.xml', function (PublicSitemap $sitemap) {
    $urls = $sitemap->entries();
    $xml = view('sitemap', compact('urls'))->render();
    $response = Response::make($xml, HttpResponse::HTTP_OK, [
        'Content-Type' => 'application/xml; charset=UTF-8',
        'Cache-Control' => 'public, max-age=300, s-maxage=3600, stale-while-revalidate=86400',
    ])->setEtag(hash('sha256', $xml));
    $response->isNotModified(request());

    return $response;
})->withoutMiddleware([StartSession::class, ShareErrorsFromSession::class, VerifyCsrfToken::class])->name('sitemap');
Route::get('/robots.txt', fn () => Response::make("User-agent: *\nAllow: /\n\nSitemap: https://securitatedigitala.ro/sitemap.xml\n", HttpResponse::HTTP_OK, [
    'Content-Type' => 'text/plain; charset=UTF-8',
    'Cache-Control' => 'public, max-age=3600, s-maxage=86400',
]))->withoutMiddleware([StartSession::class, ShareErrorsFromSession::class, VerifyCsrfToken::class])->name('robots');

// Private exposure workflows keep searched subjects out of application URLs.
Route::middleware(ExposureAccess::class)->prefix('verificare-date-personale')->name('exposure.')->group(function (): void {
    Route::get('/', [ExposureController::class, 'index'])->name('index');
    Route::post('/', [ExposureController::class, 'store'])->name('store');
    Route::get('/confirmare/{token}', [ExposureController::class, 'verificationLink'])->middleware('throttle:30,1')->name('verify');
    Route::get('/confirmare', [ExposureController::class, 'confirmation'])->name('confirmation');
    Route::post('/confirmare', [ExposureController::class, 'confirm'])->middleware('throttle:10,1')->name('confirm');
    Route::get('/raport/{id}', [ExposureController::class, 'show'])->middleware('throttle:120,1')->name('report');
    Route::get('/raport/{id}/status', [ExposureController::class, 'status'])->middleware('throttle:120,1')->name('status');
    Route::post('/raport/{id}/retrimite', [ExposureController::class, 'resend'])->name('resend');
    Route::post('/raport/{id}/clasificare/{index}', [ExposureController::class, 'classify'])->whereNumber('index')->middleware('throttle:30,1')->name('classify');
    Route::delete('/raport/{id}', [ExposureController::class, 'destroy'])->name('destroy');
});
Route::middleware([ExposureAccess::class.':admin', 'auth.basic', SiteAdmin::class, 'throttle:30,1'])->group(function (): void {
    Route::get('/admin/pagini-si-module', [SitePagesAdminController::class, 'index'])->name('admin.pages');
    Route::put('/admin/pagini-si-module', [SitePagesAdminController::class, 'update'])->name('admin.pages.update');
    Route::get('/admin/verificarea-datelor', [ExposureAdminController::class, 'index'])->name('exposure.admin');
    Route::put('/admin/verificarea-datelor', [ExposureAdminController::class, 'update'])->name('exposure.admin.update');
});
