/**
 * Tiny client-side i18n module (vanilla JS, framework-free).
 *
 * Reads the dictionary served by `<head>` (window.__i18n) and exposes:
 *
 *   t(key, replacements?)     -> translated string for the active locale
 *   getLocale() / setLocale() -> read / change the active locale
 *   onChange(fn)              -> subscribe to locale changes (React + others)
 *
 * Switching locale walks the DOM and replaces every node tagged with
 *   data-i18n="some.key"               (textContent)
 *   data-i18n-attr-<attr>="some.key"   (attribute, e.g. -placeholder, -title, -value)
 *   data-i18n-html="some.key"          (innerHTML – use sparingly)
 *
 * No page reload required.
 */

const state = {
    locale: window.__i18n?.current || 'en',
};

const listeners = new Set();

export function getLocale() {
    return state.locale;
}

export function getMessages(locale = state.locale) {
    return (window.__i18n && window.__i18n.messages && window.__i18n.messages[locale]) || {};
}

/**
 * Translate a key like "admin.actions.save" into the active locale.
 * Replacements work the same way as Laravel's __('msg', [...]):
 *   t('admin.actions.add_new', { name: 'Branch' })
 *
 * Falls back to English, then to the key itself if no translation exists.
 * Replacement values prefixed with "@@" are themselves looked up:
 *   t('admin.actions.add_new', { name: '@@admin.resources.branch' })
 */
export function t(key, replacements = {}) {
    if (!key) return '';
    const messages = getMessages();
    let value = messages[key];
    if (value === undefined) {
        value = (window.__i18n?.messages?.en || {})[key];
    }
    if (value === undefined) {
        return key;
    }
    return Object.entries(replacements).reduce(
        (acc, [k, v]) => {
            const resolved = typeof v === 'string' && v.startsWith('@@') ? t(v.slice(2)) : v;
            return acc.replaceAll(`:${k}`, String(resolved));
        },
        String(value),
    );
}

function parseReplacements(el) {
    const raw = el.getAttribute('data-i18n-replacements');
    if (!raw) return {};
    try {
        return JSON.parse(raw);
    } catch {
        return {};
    }
}

/**
 * Walk an element subtree and apply translations to all data-i18n* nodes.
 */
export function applyTranslations(root = document) {
    root.querySelectorAll('[data-i18n]').forEach((el) => {
        const key = el.getAttribute('data-i18n');
        if (!key) return;
        el.textContent = t(key, parseReplacements(el));
    });
    root.querySelectorAll('[data-i18n-html]').forEach((el) => {
        const key = el.getAttribute('data-i18n-html');
        if (!key) return;
        el.innerHTML = t(key, parseReplacements(el));
    });
    root.querySelectorAll('*').forEach((el) => {
        for (const attr of el.attributes) {
            if (!attr.name.startsWith('data-i18n-attr-')) continue;
            const targetAttr = attr.name.slice('data-i18n-attr-'.length);
            const key = attr.value;
            if (!key) continue;
            el.setAttribute(targetAttr, t(key, parseReplacements(el)));
        }
    });

    document.body?.classList?.toggle('locale-km', state.locale === 'km');
    document.body?.classList?.toggle('locale-en', state.locale === 'en');
    document.documentElement?.setAttribute('lang', state.locale);
}

export function onChange(fn) {
    listeners.add(fn);
    return () => listeners.delete(fn);
}

/**
 * Switch to a new locale.
 *  - updates the internal state
 *  - re-applies all data-i18n* attributes
 *  - notifies React + other subscribers
 *  - persists the choice via /locale/<locale>
 *  - dispatches a `i18n:locale-changed` window event
 *
 * No page reload.
 */
export async function setLocale(locale) {
    if (!window.__i18n?.supported?.includes(locale)) {
        console.warn(`[i18n] unsupported locale "${locale}"`);
        return;
    }
    state.locale = locale;
    if (window.__i18n) window.__i18n.current = locale;
    applyTranslations();
    listeners.forEach((fn) => {
        try {
            fn(locale);
        } catch (e) {
            console.error('[i18n] listener error', e);
        }
    });
    window.dispatchEvent(new CustomEvent('i18n:locale-changed', { detail: { locale } }));
    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        await fetch(`/locale/${locale}`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: csrf ? { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' } : { Accept: 'application/json' },
        });
    } catch (err) {
        console.warn('[i18n] could not persist locale', err);
    }
}

export const i18nState = state;
