@extends('admin.layouts.admin_layout')

@section('page_title', $meta['singular'] . ' #' . $record->id)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.' . $meta['route_slug'] . '.index') }}" data-i18n="{{ $meta['plural_key'] }}">{{ $meta['plural'] }}</a>
    </li>
    <li class="breadcrumb-item active">#{{ $record->id }}</li>
@endsection

@section('page_actions')
    @if(\Route::has('admin.' . $meta['route_slug'] . '.edit'))
        <a href="{{ route('admin.' . $meta['route_slug'] . '.edit', $record) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>
            <span data-i18n="admin.actions.edit">{{ __('admin.actions.edit') }}</span>
        </a>
    @endif
@endsection

@section('content')
<div class="card admin-card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-md-3" data-i18n="admin.misc.id">{{ __('admin.misc.id') }}</dt>
            <dd class="col-md-9">#{{ $record->id }}</dd>

            @foreach($fields as $field)
                @php $val = $record->{$field->name} ?? null; $i18nKey = 'admin.fields.' . $field->name; @endphp
                <dt class="col-md-3" @if(trans()->has($i18nKey)) data-i18n="{{ $i18nKey }}" @endif>
                    {{ trans()->has($i18nKey) ? __($i18nKey) : $field->label }}
                </dt>
                <dd class="col-md-9">
                    @if($field->type === 'checkbox' || is_bool($val))
                        @if($val) <span class="badge bg-success" data-i18n="admin.misc.yes">{{ __('admin.misc.yes') }}</span>
                        @else <span class="badge bg-secondary" data-i18n="admin.misc.no">{{ __('admin.misc.no') }}</span> @endif
                    @elseif($field->type === 'image' && $val)
                        <img src="{{ \Illuminate\Support\Str::startsWith($val, 'http') ? $val : asset('storage/' . $val) }}"
                             alt="" style="max-height:100px" class="img-thumbnail">
                    @elseif(is_null($val) || $val === '')
                        <span class="text-muted">—</span>
                    @else
                        {{ $val }}
                    @endif
                </dd>
            @endforeach

            <dt class="col-md-3" data-i18n="admin.misc.created_at">{{ __('admin.misc.created_at') }}</dt>
            <dd class="col-md-9">{{ $record->created_at?->format('Y-m-d H:i') }}</dd>
        </dl>
    </div>
</div>
@endsection
