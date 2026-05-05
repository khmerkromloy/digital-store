@php
    $name = $field->name;
    $current = old($name, $record->{$name} ?? $field->default);
    $required = $field->required ? 'required' : '';
    $invalid = $errors->has($name) ? ' is-invalid' : '';
    $i18nLabelKey = 'admin.fields.' . $name;
    $hasI18nLabel = trans()->has($i18nLabelKey);
@endphp

<div class="{{ $field->colClass }}">
    <label for="field-{{ $name }}" class="form-label small fw-medium">
        @if($hasI18nLabel)
            <span data-i18n="{{ $i18nLabelKey }}">{{ __($i18nLabelKey) }}</span>
        @else
            {{ $field->label }}
        @endif
        @if($field->required) <span class="text-danger">*</span> @endif
    </label>

    @switch($field->type)
        @case('textarea')
            <textarea
                id="field-{{ $name }}"
                name="{{ $name }}"
                rows="{{ $field->rows ?? 3 }}"
                class="form-control{{ $invalid }}"
                {!! $field->placeholder ? 'placeholder="' . e($field->placeholder) . '"' : '' !!}
                {{ $required }}
            >{{ $current }}</textarea>
        @break

        @case('select')
            <select
                id="field-{{ $name }}"
                name="{{ $name }}{{ str_contains($name, '[]') ? '' : '' }}"
                class="form-select tom-select{{ $invalid }}"
                {{ $required }}
            >
                <option value="">— {{ __('admin.misc.optional') }} —</option>
                @foreach($field->options as $value => $label)
                    @php
                        $i18nKey = is_string($label) && \Illuminate\Support\Str::startsWith($label, 'admin.') ? $label : null;
                    @endphp
                    <option value="{{ $value }}" @selected((string) $current === (string) $value)>
                        {{ $i18nKey ? __($i18nKey) : $label }}
                    </option>
                @endforeach
            </select>
        @break

        @case('multiselect')
            <select
                id="field-{{ $name }}"
                name="{{ $name }}[]"
                multiple
                class="form-select tom-select{{ $invalid }}"
            >
                @foreach($field->options as $value => $label)
                    <option value="{{ $value }}" @selected(is_array($current) && in_array($value, $current))>{{ $label }}</option>
                @endforeach
            </select>
        @break

        @case('checkbox')
            <div class="form-check pt-2">
                <input type="hidden" name="{{ $name }}" value="0">
                <input
                    id="field-{{ $name }}"
                    type="checkbox"
                    name="{{ $name }}"
                    value="1"
                    class="form-check-input{{ $invalid }}"
                    @checked((bool) $current)
                >
                <label class="form-check-label small" for="field-{{ $name }}">
                    @if($hasI18nLabel)
                        <span data-i18n="{{ $i18nLabelKey }}">{{ __($i18nLabelKey) }}</span>
                    @else
                        {{ $field->label }}
                    @endif
                </label>
            </div>
        @break

        @case('date')
            <input id="field-{{ $name }}" type="text" name="{{ $name }}"
                   value="{{ $current }}" class="form-control flatpickr-date{{ $invalid }}" {{ $required }}>
        @break

        @case('time')
            <input id="field-{{ $name }}" type="text" name="{{ $name }}"
                   value="{{ $current }}" class="form-control flatpickr-time{{ $invalid }}" {{ $required }}>
        @break

        @case('datetime')
            <input id="field-{{ $name }}" type="text" name="{{ $name }}"
                   value="{{ $current }}" class="form-control flatpickr-datetime{{ $invalid }}" {{ $required }}>
        @break

        @case('image')
        @case('file')
            <input id="field-{{ $name }}" type="file" name="{{ $name }}"
                   class="form-control{{ $invalid }}"
                   @if($field->accept) accept="{{ $field->accept }}" @endif
                   {{ $required }}>
            @if(!empty($record->{$name}))
                <small class="text-muted">Current: {{ $record->{$name} }}</small>
            @endif
        @break

        @case('color')
            <input id="field-{{ $name }}" type="color" name="{{ $name }}"
                   value="{{ $current ?: '#6c5ce7' }}" class="form-control form-control-color{{ $invalid }}">
        @break

        @case('password')
            <input id="field-{{ $name }}" type="password" name="{{ $name }}"
                   class="form-control{{ $invalid }}" {{ $required }} autocomplete="new-password">
        @break

        @case('email')
            <input id="field-{{ $name }}" type="email" name="{{ $name }}"
                   value="{{ $current }}" class="form-control{{ $invalid }}" {{ $required }}>
        @break

        @case('number')
        @case('decimal')
            <input id="field-{{ $name }}" type="number" name="{{ $name }}"
                   value="{{ $current }}" class="form-control{{ $invalid }}"
                   step="{{ $field->type === 'decimal' ? '0.01' : '1' }}" {{ $required }}>
        @break

        @default
            <input id="field-{{ $name }}" type="text" name="{{ $name }}"
                   value="{{ $current }}" class="form-control{{ $invalid }}"
                   {!! $field->placeholder ? 'placeholder="' . e($field->placeholder) . '"' : '' !!}
                   {{ $required }}>
    @endswitch

    @if($field->help)
        <small class="form-text text-muted">{{ $field->help }}</small>
    @endif

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
