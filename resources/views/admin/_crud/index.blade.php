@extends('admin.layouts.admin_layout')

@section('page_title', $meta['plural'])
@section('page_title_key', $meta['plural_key'])

@section('breadcrumb')
    <li class="breadcrumb-item active" data-i18n="{{ $meta['plural_key'] }}">{{ $meta['plural'] }}</li>
@endsection

@section('page_actions')
    @if(\Route::has('admin.' . $meta['route_slug'] . '.create'))
        <a href="{{ route('admin.' . $meta['route_slug'] . '.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            <span
                data-i18n="admin.actions.add_new"
                data-i18n-replacements='@json(['name' => '@@' . $meta['singular_key']])'
            >{{ __('admin.actions.add_new', ['name' => $meta['singular']]) }}</span>
        </a>
    @endif
@endsection

@section('content')
<div class="card admin-card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h3 class="card-title mb-0" data-i18n="{{ $meta['plural_key'] }}">{{ $meta['plural'] }}</h3>
    </div>

    <div class="card-body">
        @yield('filters')

        <table id="crud-table" class="table table-hover table-striped align-middle w-100">
            <thead>
                <tr>
                    <th data-i18n="admin.misc.id">{{ __('admin.misc.id') }}</th>
                    @foreach($columns as $col => $label)
                        <th @if(\Illuminate\Support\Str::startsWith($label, 'admin.')) data-i18n="{{ $label }}" @endif>
                            {{ \Illuminate\Support\Str::startsWith($label, 'admin.') ? __($label) : $label }}
                        </th>
                    @endforeach
                    <th class="text-end" data-i18n="admin.misc.actions_col">{{ __('admin.misc.actions_col') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dataUrl = @json($dataUrl);
    const columns = @json(array_keys($columns));
    const cols = [
        { data: 'id', name: 'id', orderable: true, searchable: false },
        ...columns.map((c) => ({ data: c, name: c, orderable: true, searchable: true })),
        { data: 'action_buttons', name: 'action_buttons', orderable: false, searchable: false, className: 'text-end' },
    ];

    function buildLang() {
        const t = window.DigitalStore.t;
        if (window.__i18n?.current === 'km') {
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

    function buildTable() {
        return $('#crud-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [[0, 'desc']],
            ajax: dataUrl,
            columns: cols,
            pageLength: 10,
            language: buildLang(),
            drawCallback: function () {
                // Re-bind newly inserted action buttons + re-translate their tooltips.
                window.DigitalStore.bindConfirmDelete(this.api().table().container());
                window.DigitalStore.applyTranslations(this.api().table().container());
            },
        });
    }

    let table = buildTable();

    // Re-init the table in place when locale changes (no full page reload).
    window.addEventListener('i18n:locale-changed', () => {
        table.destroy();
        $('#crud-table thead').nextAll().remove(); // wipe DataTables-generated controls
        table = buildTable();
    });
});
</script>
@endpush
