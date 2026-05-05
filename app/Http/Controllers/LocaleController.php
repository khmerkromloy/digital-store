<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Persist the user's locale choice in a 1-year cookie.
     * The actual page text is updated client-side by the React language switcher,
     * but this is what makes subsequent server-rendered pages already in the new locale.
     */
    public function set(Request $request, string $locale)
    {
        abort_unless(in_array($locale, config('app.supported_locales', ['en', 'km']), true), 404);

        return response()->noContent()->withCookie(
            cookie('locale', $locale, 60 * 24 * 365, '/', null, null, false, false, 'Lax')
        );
    }
}
