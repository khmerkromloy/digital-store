@extends('admin.layouts.admin_layout')

@section('page_title', $mode === 'create' ? __('admin.actions.add_new', ['name' => $meta['singular']]) : __('admin.actions.edit') . ' ' . $meta['singular'])

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.' . $meta['route_slug'] . '.index') }}" data-i18n="{{ $meta['plural_key'] }}">{{ $meta['plural'] }}</a>
    </li>
    <li class="breadcrumb-item active" data-i18n="admin.actions.{{ $mode === 'create' ? 'create' : 'edit' }}">
        {{ __('admin.actions.' . ($mode === 'create' ? 'create' : 'edit')) }}
    </li>
@endsection

@section('content')
<div class="card admin-card">
    <div class="card-body">
        <form method="POST"
              action="{{ $mode === 'create' ? route('admin.' . $meta['route_slug'] . '.store') : route('admin.' . $meta['route_slug'] . '.update', $record) }}"
              enctype="multipart/form-data">
            @csrf
            @if($mode !== 'create') @method('PUT') @endif

            <div class="row g-3">
                @foreach($fields as $field)
                    @include('admin._crud.partials.field', ['field' => $field, 'record' => $record])
                @endforeach
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    <span data-i18n="admin.actions.save">{{ __('admin.actions.save') }}</span>
                </button>
                <a href="{{ route('admin.' . $meta['route_slug'] . '.index') }}" class="btn btn-outline-secondary">
                    <span data-i18n="admin.actions.cancel">{{ __('admin.actions.cancel') }}</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
