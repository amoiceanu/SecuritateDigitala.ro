<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolvePublicLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin', 'admin/*')) {
            app()->setLocale('ro');

            return $next($request);
        }

        // Stateless endpoints (robots.txt, sitemap.xml) intentionally remove
        // StartSession. Keep locale resolution safe for those requests and tests.
        $locale = $request->hasSession()
            ? $request->session()->get('site_locale')
            : null;

        if (! in_array($locale, ['ro', 'en'], true)) {
            $country = strtoupper((string) (
                $request->header('CF-IPCountry')
                ?? $request->header('CloudFront-Viewer-Country')
                ?? $request->header('X-AppEngine-Country')
                ?? $request->header('X-Vercel-IP-Country')
            ));

            // When the edge does not provide a country, keep the requested Romanian default.
            $locale = $country !== '' ? ($country === 'RO' ? 'ro' : 'en') : 'ro';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
