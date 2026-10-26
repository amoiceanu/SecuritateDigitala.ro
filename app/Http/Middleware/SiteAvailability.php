<?php

namespace App\Http\Middleware;

use App\Services\SitePages;
use Closure;
use Illuminate\Http\Request;

class SiteAvailability
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route()?->getName();
        $key = match (true) {
            $route === 'home' => 'home',
            in_array($route, ['articles.index', 'articles.show', 'sections.show']) => 'articles',
            in_array($route, ['contact', 'contact.store']) => 'contact',
            in_array($route, ['about', 'privacy', 'cookies']) => $route,
            str_starts_with($route ?? '', 'exposure.') && ! str_starts_with($route ?? '', 'exposure.admin') => 'exposure',
            default => null,
        };
        if ($key) {
            $pages = app(SitePages::class);
            abort_unless($pages->enabled($key), 404);
            if ($route === 'articles.index' && in_array($request->query('categorie'), ['ghiduri', 'ghiduri-practice'], true)) {
                abort_unless($pages->enabled('guides'), 404);
            }
            if ($route === 'sections.show') {
                $category = $request->route('category');
                $slug = is_object($category) ? $category->slug : $category;
                if ($slug === 'ghiduri-practice') {
                    abort_unless($pages->enabled('guides'), 404);
                }
            }
        }
        $response = $next($request);
        // Availability and navigation changes must be reflected immediately, without public cached pages.
        if ($key) {
            $response->headers->set('Cache-Control', 'private, no-store');
        }

        return $response;
    }
}
