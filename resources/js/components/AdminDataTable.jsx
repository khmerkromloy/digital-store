import React, { useEffect, useRef, useState } from 'react';
import { t, getLocale, onChange } from '../i18n.js';

/**
 * Wraps a Yajra-served DataTable with React.
 * - server-side AJAX
 * - Bootstrap 5 fixed pagination (uses datatables.net-bs5 styling)
 * - re-translates static labels when locale changes (no refresh)
 *
 * Props (read from the mount node's data-* attributes):
 *   data-ajax-url     URL of the Yajra JSON endpoint
 *   data-columns      JSON array [{data, name, title, orderable, searchable, render?}]
 *   data-page-length  default 10
 *   data-table-id     id for the rendered <table>
 */
export default function AdminDataTable({
    ajaxUrl,
    columns,
    pageLength = 10,
    tableId = 'admin-data-table',
    extraQuery,
}) {
    const tableRef = useRef(null);
    const dtRef = useRef(null);
    const [locale, setLocale] = useState(getLocale());

    useEffect(() => onChange(setLocale), []);

    useEffect(() => {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            console.error('[AdminDataTable] jQuery + DataTables not loaded');
            return;
        }
        const dt = $(tableRef.current).DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength,
            order: [[0, 'desc']],
            ajax: {
                url: ajaxUrl,
                data: (d) => ({ ...d, ...(extraQuery?.() || {}) }),
            },
            columns: columns.map((c) => ({
                ...c,
                render: c.render ? new Function('data', 'type', 'row', 'meta', `return (${c.render})(data, type, row, meta);`) : undefined,
            })),
            language: localeLanguage(locale),
        });
        dtRef.current = dt;
        return () => {
            dt.destroy();
            dtRef.current = null;
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [ajaxUrl]);

    // When the user switches Khmer/English, re-render Yajra labels live.
    useEffect(() => {
        if (!dtRef.current) return;
        const $ = window.jQuery || window.$;
        $(tableRef.current).DataTable().destroy();
        dtRef.current = $(tableRef.current).DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength,
            order: [[0, 'desc']],
            ajax: { url: ajaxUrl, data: (d) => ({ ...d, ...(extraQuery?.() || {}) }) },
            columns: columns.map((c) => ({
                ...c,
                render: c.render ? new Function('data', 'type', 'row', 'meta', `return (${c.render})(data, type, row, meta);`) : undefined,
            })),
            language: localeLanguage(locale),
        });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [locale]);

    return (
        <div className="table-responsive">
            <table id={tableId} ref={tableRef} className="table table-hover table-striped align-middle w-100">
                <thead>
                    <tr>
                        {columns.map((c) => (
                            <th key={c.data || c.name}>{c.title}</th>
                        ))}
                    </tr>
                </thead>
            </table>
        </div>
    );
}

function localeLanguage(locale) {
    if (locale === 'km') {
        return {
            search: t('admin.actions.search'),
            zeroRecords: t('admin.misc.no_records'),
            emptyTable: t('admin.misc.no_records'),
            info: 'បង្ហាញ _START_ ដល់ _END_ នៃ _TOTAL_ ធាតុ',
            infoEmpty: 'បង្ហាញ ០ ដល់ ០ នៃ ០ ធាតុ',
            infoFiltered: '(បានច្រោះពី _MAX_ ធាតុសរុប)',
            lengthMenu: 'បង្ហាញ _MENU_ ធាតុ',
            paginate: { first: 'ដំបូង', previous: 'មុន', next: 'បន្ទាប់', last: 'ចុង' },
            processing: 'កំពុងផ្ទុក…',
        };
    }
    return {
        search: t('admin.actions.search'),
        zeroRecords: t('admin.misc.no_records'),
        emptyTable: t('admin.misc.no_records'),
    };
}
