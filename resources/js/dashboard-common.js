// Common dashboard scripts
document.addEventListener('DOMContentLoaded', function () {
    // Common delete functionality
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteForm = document.getElementById('delete-form');

    if (deleteButtons.length > 0 && deleteForm) {
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                confirmDelete(id);
            });
        });

        // Individual delete confirmation
        window.confirmDelete = function (id) {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading('Menghapus...');
                    deleteForm.action = deleteForm.getAttribute('data-action') + '/' + id;
                    deleteForm.submit();
                }
            });
        };
    }

    // Common form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Harap lengkapi semua field yang wajib diisi.',
                    icon: 'error'
                });
            }
        });
    });

});
