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

        $locale = $request->session()->get('site_locale');

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
