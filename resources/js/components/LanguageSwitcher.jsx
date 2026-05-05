import React, { useEffect, useState, useCallback, useRef } from 'react';
import { setLocale, t, getLocale, onChange } from '../i18n.js';

const FLAGS = { en: '🇬🇧', km: '🇰🇭' };

/**
 * Bootstrap-5 styled dropdown that lets users switch between English
 * and Khmer without reloading the page. We manage the open/close state
 * inside React (rather than via Bootstrap's `data-bs-toggle`) so that
 * the synthetic click handlers on the items always fire reliably under
 * React 18's createRoot event delegation.
 */
export default function LanguageSwitcher() {
    const [locale, setLocaleState] = useState(getLocale());
    const [open, setOpen] = useState(false);
    const supported = window.__i18n?.supported || ['en', 'km'];
    const ref = useRef(null);

    useEffect(() => onChange(setLocaleState), []);

    useEffect(() => {
        if (!open) return undefined;
        const onDoc = (e) => {
            if (!ref.current?.contains(e.target)) setOpen(false);
        };
        document.addEventListener('click', onDoc);
        return () => document.removeEventListener('click', onDoc);
    }, [open]);

    const choose = useCallback(
        (next) => {
            setOpen(false);
            if (next === locale) return;
            setLocale(next);
        },
        [locale],
    );

    return (
        <div ref={ref} className={`dropdown ds-lang-switcher${open ? ' show' : ''}`}>
            <a
                className="nav-link dropdown-toggle"
                href="#"
                role="button"
                aria-expanded={open ? 'true' : 'false'}
                onClick={(e) => {
                    e.preventDefault();
                    setOpen((v) => !v);
                }}
            >
                <span className="flag-icon">{FLAGS[locale] || locale}</span>
                <span className="d-none d-md-inline ms-1">{t(`admin.language_${locale}`)}</span>
            </a>
            <ul className={`dropdown-menu dropdown-menu-end shadow-sm${open ? ' show' : ''}`}>
                {supported.map((code) => (
                    <li key={code}>
                        <button
                            type="button"
                            className={`dropdown-item d-flex align-items-center gap-2${code === locale ? ' active' : ''}`}
                            onClick={() => choose(code)}
                        >
                            <span className="flag-icon">{FLAGS[code] || code}</span>
                            <span>{t(`admin.language_${code}`)}</span>
                        </button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
