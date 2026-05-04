@extends('layouts.app')

@section('title', 'All Products')
@section('description', 'Browse the full digital product catalog: license keys, premium accounts, streaming subscriptions and more.')

@section('content')
    <section class="py-4 bg-white border-bottom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1 fw-bold">All products</h1>
            <p class="text-muted mb-0">Server-side searchable, sortable and paginated via Yajra DataTables.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <aside class="col-lg-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Filters</h6>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Category</label>
                                <select id="filter-category" class="form-select form-select-sm">
                                    <option value="">All categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->slug }}" @selected($selectedCategory === $cat->slug)>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Price range (USD)</label>
                                <div class="d-flex gap-2">
                                    <input type="number" min="0" step="0.01" id="filter-price-min" placeholder="Min" class="form-control form-control-sm">
                                    <input type="number" min="0" step="0.01" id="filter-price-max" placeholder="Max" class="form-control form-control-sm">
                                </div>
                            </div>

                            <button id="filter-apply" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-funnel me-1"></i> Apply filters
                            </button>
                            <button id="filter-reset" class="btn btn-link btn-sm w-100 mt-1">
                                Reset
                            </button>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Quick categories</h6>
                            <div class="d-grid gap-2">
                                @foreach($categories as $cat)
                                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
                                       class="btn btn-sm btn-outline-secondary text-start d-flex align-items-center gap-2">
                                        <i class="bi {{ $cat->icon ?? 'bi-box-seam' }}"></i>
                                        <span>{{ $cat->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <table id="products-table" class="table table-hover align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-center">Sales</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (function() {
        const dataUrl = @json(route('products.data'));
        const detailRouteBase = @json(url('/products'));

        const table = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [[4, 'desc']],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: '',
                searchPlaceholder: 'Search products...',
                emptyTable: 'No products found',
            },
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.category = $('#filter-category').val();
                    d.price_min = $('#filter-price-min').val();
                    d.price_max = $('#filter-price-max').val();
                },
            },
            columns: [
                {
                    data: 'name',
                    name: 'name',
                    render: function (data, type, row) {
                        if (type !== 'display') return data;
                        const featured = row.is_featured
                            ? '<span class="badge bg-warning-subtle text-warning ms-1">Featured</span>'
                            : '';
                        const short = row.short_description
                            ? `<div class="text-muted small">${$('<div/>').text(row.short_description).html()}</div>`
                            : '';
                        return `
                            <div class="d-flex flex-column">
                                <a href="${row.detail_url}" class="text-decoration-none fw-semibold text-dark">
                                    ${$('<div/>').text(data).html()}${featured}
                                </a>
                                ${short}
                            </div>`;
                    },
                },
                {
                    data: 'category_name',
                    name: 'category_name',
                    render: function (data) {
                        if (!data) return '';
                        return `<span class="badge badge-soft-primary">${$('<div/>').text(data).html()}</span>`;
                    },
                },
                {
                    data: 'price_formatted',
                    name: 'price',
                    className: 'text-end',
                    render: function (data, type, row) {
                        if (type !== 'display') return row.price;
                        let html = `<span class="fw-semibold text-primary">${data}</span>`;
                        if (row.original_price_formatted) {
                            html += ` <small class="text-muted text-decoration-line-through">${row.original_price_formatted}</small>`;
                        }
                        if (row.discount_percent) {
                            html += ` <span class="badge bg-danger-subtle text-danger">-${row.discount_percent}%</span>`;
                        }
                        return html;
                    },
                },
                {
                    data: 'stock',
                    name: 'stock',
                    className: 'text-center',
                    render: function (data) {
                        const cls = data > 10 ? 'text-success' : (data > 0 ? 'text-warning' : 'text-danger');
                        const label = data > 0 ? `${data} in stock` : 'Out of stock';
                        return `<span class="${cls} small fw-semibold">${label}</span>`;
                    },
                },
                {
                    data: 'sales_count',
                    name: 'sales_count',
                    className: 'text-center',
                },
                {
                    data: 'detail_url',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    className: 'text-end',
                    render: function (data) {
                        return `<a href="${data}" class="btn btn-sm btn-outline-primary">View <i class="bi bi-arrow-right"></i></a>`;
                    },
                },
            ],
        });

        $('#filter-apply').on('click', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });

        $('#filter-reset').on('click', function (e) {
            e.preventDefault();
            $('#filter-category').val('');
            $('#filter-price-min').val('');
            $('#filter-price-max').val('');
            table.search('').ajax.reload();
        });

        $('#filter-category').on('change', function () {
            table.ajax.reload();
        });
    })();
</script>
@endpush
