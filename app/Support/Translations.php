<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Loads and flattens Laravel `lang/<locale>/admin.php` files for both server
 * rendering and the React-driven client-side language switcher.
 *
 * Flattened keys ("admin.actions.save" => "Save") match Laravel's __()
 * helper exactly so the same key works in Blade and in the JS i18n module.
 */
class Translations
{
    /**
     * The translation namespaces (lang file names) exposed to the frontend.
     * Add a new file here and it becomes available as window.__i18n.messages.<lang>.<file>.<key>.
     */
    public const NAMESPACES = ['admin'];

    public static function dictionary(string $locale): array
    {
        return Cache::driver('array')->rememberForever("translations:$locale", function () use ($locale) {
            $flat = [];
            foreach (self::NAMESPACES as $ns) {
                $messages = trans($ns, [], $locale);
                if (! is_array($messages)) {
                    continue;
                }
                self::flatten($messages, $ns, $flat);
            }

            return $flat;
        });
    }

    private static function flatten(array $node, string $prefix, array &$out): void
    {
        foreach ($node as $key => $value) {
            $compound = $prefix === '' ? $key : "$prefix.$key";
            if (is_array($value)) {
                self::flatten($value, $compound, $out);
            } else {
                $out[$compound] = $value;
            }
        }
    }
}
