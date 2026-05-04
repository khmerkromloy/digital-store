import './bootstrap';
import $ from 'jquery';
window.$ = window.jQuery = $;

import 'bootstrap';

import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';

document.addEventListener('DOMContentLoaded', () => {
    // Setup CSRF token for jQuery AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
        },
    });
});
