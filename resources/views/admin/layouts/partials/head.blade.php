<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', __('admin.resources.dashboard')) | {{ __('admin.app_name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Khmer-friendly font --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Khmer:wght@300;400;500;700&display=swap">

    {{-- App / Vite assets (Bootstrap 5 + DataTables-bs5 + SweetAlert2 + flatpickr + Tom Select all bundled) --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    {{-- Translation dictionary, exposed for the React language switcher. --}}
    <script>
        window.__i18n = {
            current: @json(app()->getLocale()),
            supported: @json(config('app.supported_locales', ['en', 'km'])),
            messages: {
                en: @json(\App\Support\Translations::dictionary('en')),
                km: @json(\App\Support\Translations::dictionary('km')),
            },
        };
    </script>

    @stack('head')
</head>
