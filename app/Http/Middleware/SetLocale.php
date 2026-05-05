<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolve the active locale for every request, in priority order:
 *   1. ?lang=km   (explicit override via querystring)
 *   2. cookie     (set by /locale/{locale} after the user clicks the switcher)
 *   3. config('app.locale') fallback
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('app.supported_locales', ['en', 'km']);
        $candidate = $request->query('lang')
            ?: $request->cookie('locale')
            ?: config('app.locale');

        if (in_array($candidate, $supported, true)) {
            App::setLocale($candidate);
        }

        return $next($request);
    }
}
