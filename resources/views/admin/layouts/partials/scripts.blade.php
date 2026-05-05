{{-- PHPFlasher: renders any flash()->success(...) / ->error(...) calls as toasts. --}}
@flasher_render

<script>
    document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
        document.getElementById('admin-sidebar')?.classList.toggle('d-none');
    });
</script>

@stack('scripts')
