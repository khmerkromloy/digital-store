@if (session('success'))
    <div data-flash class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('error'))
    <div data-flash class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-x-circle-fill me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('info'))
    <div data-flash class="alert alert-info alert-dismissible fade show">
        <i class="bi bi-info-circle-fill me-1"></i> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>{{ __('admin.misc.required') }}:</strong>
        <ul class="mt-2 mb-0">
            @foreach ($errors->all() as $msg)
                <li>{{ $msg }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
