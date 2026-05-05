import './bootstrap';
import $ from 'jquery';
window.$ = window.jQuery = $;

import 'bootstrap';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';

import React from 'react';
import { createRoot } from 'react-dom/client';
import Swal from 'sweetalert2';
import flatpickr from 'flatpickr';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';
import 'flatpickr/dist/flatpickr.min.css';
import 'sweetalert2/dist/sweetalert2.min.css';

import { applyTranslations, t, setLocale, onChange, i18nState } from './i18n.js';
import LanguageSwitcher from './components/LanguageSwitcher.jsx';
import AdminDataTable from './components/AdminDataTable.jsx';

window.Swal = Swal;
window.flatpickr = flatpickr;
window.TomSelect = TomSelect;

/* ------------------------------------------------------------------
 | jQuery AJAX CSRF defaults.
 -----------------------------------------------------------------*/
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
    },
});

/* ------------------------------------------------------------------
 | SweetAlert2 confirm-delete handler.
 | Any form/link with `data-confirm-delete` opens a Swal confirmation;
 | the form is submitted on confirm.
 -----------------------------------------------------------------*/
function bindConfirmDelete(root = document) {
    root.querySelectorAll('[data-confirm-delete]').forEach((el) => {
        if (el.dataset.confirmBound) return;
        el.dataset.confirmBound = '1';
        el.addEventListener('submit', (e) => {
            e.preventDefault();
            const form = e.currentTarget;
            const fallback = form.dataset.confirmDelete || t('admin.dialogs.confirm_delete_text');
            Swal.fire({
                title: t('admin.dialogs.confirm_delete_title'),
                text: fallback,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: t('admin.dialogs.confirm_delete_button'),
                cancelButtonText: t('admin.dialogs.cancel_button'),
                confirmButtonColor: '#dc3545',
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // Also support <a data-confirm-delete> + <button data-confirm-delete-form="#form-id">
    root.querySelectorAll('button[data-confirm-delete-form]').forEach((btn) => {
        if (btn.dataset.confirmBound) return;
        btn.dataset.confirmBound = '1';
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const formSel = btn.getAttribute('data-confirm-delete-form');
            const form = document.querySelector(formSel);
            if (!form) return;
            Swal.fire({
                title: t('admin.dialogs.confirm_delete_title'),
                text: t('admin.dialogs.confirm_delete_text'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: t('admin.dialogs.confirm_delete_button'),
                cancelButtonText: t('admin.dialogs.cancel_button'),
                confirmButtonColor: '#dc3545',
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
}

/* ------------------------------------------------------------------
 | flatpickr on inputs flagged `.flatpickr-date` / `.flatpickr-time` / `.flatpickr-datetime`.
 -----------------------------------------------------------------*/
function bindFlatpickr(root = document) {
    root.querySelectorAll('input.flatpickr-date').forEach((el) => {
        if (el._flatpickr) return;
        flatpickr(el, { dateFormat: 'Y-m-d', allowInput: true });
    });
    root.querySelectorAll('input.flatpickr-time').forEach((el) => {
        if (el._flatpickr) return;
        flatpickr(el, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true });
    });
    root.querySelectorAll('input.flatpickr-datetime').forEach((el) => {
        if (el._flatpickr) return;
        flatpickr(el, { enableTime: true, dateFormat: 'Y-m-d H:i' });
    });
}

/* ------------------------------------------------------------------
 | Tom Select on selects flagged `.tom-select`.
 -----------------------------------------------------------------*/
function bindTomSelect(root = document) {
    root.querySelectorAll('select.tom-select').forEach((el) => {
        if (el.tomselect) return;
        const opts = {
            allowEmptyOption: true,
            create: false,
            plugins: el.multiple ? ['remove_button'] : [],
        };
        if (el.dataset.tomSelectAjax) {
            opts.valueField = 'id';
            opts.labelField = el.dataset.tomSelectLabel || 'name';
            opts.searchField = (el.dataset.tomSelectSearch || el.dataset.tomSelectLabel || 'name').split(',').map((s) => s.trim());
            opts.load = (q, cb) => {
                fetch(`${el.dataset.tomSelectAjax}?q=${encodeURIComponent(q)}`, { headers: { Accept: 'application/json' } })
                    .then((r) => r.json())
                    .then((j) => cb(j.data || j))
                    .catch(() => cb());
            };
        }
        new TomSelect(el, opts);
    });
}

/* ------------------------------------------------------------------
 | Auto-dismiss server-side flash messages after 5s.
 -----------------------------------------------------------------*/
function bindFlash() {
    document.querySelectorAll('[data-flash]').forEach((el) => {
        setTimeout(() => {
            el.classList.add('fade');
            setTimeout(() => el.remove(), 300);
        }, 5000);
    });
}

/* ------------------------------------------------------------------
 | Mount React components into [data-react-component] nodes.
 -----------------------------------------------------------------*/
const REACT_COMPONENTS = {
    'language-switcher': LanguageSwitcher,
    'admin-data-table': AdminDataTable,
};

function mountReactComponents(root = document) {
    root.querySelectorAll('[data-react-component]').forEach((el) => {
        if (el.dataset.reactMounted) return;
        const name = el.getAttribute('data-react-component');
        const Component = REACT_COMPONENTS[name];
        if (!Component) {
            console.warn(`[react] Unknown component "${name}"`);
            return;
        }
        let props = {};
        const propsAttr = el.getAttribute('data-react-props');
        if (propsAttr) {
            try {
                props = JSON.parse(propsAttr);
            } catch (e) {
                console.error('[react] invalid props JSON', e);
            }
        }
        const root = createRoot(el);
        root.render(React.createElement(Component, props));
        el.dataset.reactMounted = '1';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    applyTranslations();
    bindConfirmDelete();
    bindFlatpickr();
    bindTomSelect();
    bindFlash();
    mountReactComponents();
});

// Re-translate any newly added DOM nodes after a locale switch.
onChange(() => {
    applyTranslations();
});

window.DigitalStore = {
    bindConfirmDelete,
    bindFlatpickr,
    bindTomSelect,
    bindFlash,
    mountReactComponents,
    setLocale,
    t,
    applyTranslations,
    Swal,
};
