{{-- ============================================================
     Unified Alert Component (SweetAlert2 Toast)
     Digunakan oleh semua layout: vendor, user, dan admin/dev

     Session keys yang didukung:
     - toast_success, toast_error, toast_info, toast_warning (judul saja)
     - success, error, warning, info (judul + teks)
     ============================================================ --}}

{{-- Toast-style alerts --}}
@if (session('toast_success') || session('toast_error') || session('toast_info') || session('toast_warning'))
    <script>
        @if (session('toast_success'))
            Swal.fire({
                icon: 'success',
                title: '{{ session('toast_success') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        @endif

        @if (session('toast_error'))
            Swal.fire({
                icon: 'error',
                title: '{{ session('toast_error') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        @endif

        @if (session('toast_info'))
            Swal.fire({
                icon: 'info',
                title: '{{ session('toast_info') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        @endif

        @if (session('toast_warning'))
            Swal.fire({
                icon: 'warning',
                title: '{{ session('toast_warning') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        @endif
    </script>
@endif

{{-- Standard session alerts --}}
@if (session('success') || session('error') || session('warning') || session('info'))
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        @endif

        @if (session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: '{{ session('warning') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        @endif

        @if (session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: '{{ session('info') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        @endif
    </script>
@endif

{{-- Utility functions --}}
<script>
    // Loading alert
    function showLoading(message = 'Memproses...') {
        Swal.fire({
            title: message,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Delete confirmation
    function confirmDelete(formId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading('Menghapus...');
                document.getElementById(formId).submit();
            }
        });
    }

    // Generic confirmation dialog
    function confirmAction(options = {}) {
        Swal.fire({
            title: options.title || 'Konfirmasi',
            text: options.text || 'Apakah Anda yakin?',
            icon: options.icon || 'warning',
            showCancelButton: true,
            confirmButtonColor: options.confirmColor || '#3085d6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: options.confirmText || 'Ya',
            cancelButtonText: options.cancelText || 'Batal'
        }).then((result) => {
            if (result.isConfirmed && typeof options.onConfirm === 'function') {
                options.onConfirm();
            }
        });
    }

    // Loading state for forms (only for forms with data-loading attribute)
    document.querySelectorAll('form[data-loading]').forEach(form => {
        form.addEventListener('submit', () => {
            showLoading('Memproses...');
        });
    });

    // Loading state for links (only for links with data-loading attribute)
    document.querySelectorAll('a[data-loading]:not([href^="#"])').forEach(link => {
        link.addEventListener('click', () => {
            showLoading('Memuat...');
        });
    });
</script>
