// File upload validation for vendor logo
document.addEventListener('DOMContentLoaded', function () {
    const logoInputs = document.querySelectorAll('input[name="logo"]');

    logoInputs.forEach(input => {
        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            const errorElement = this.parentNode.querySelector('.file-error');

            // Remove existing error message
            if (errorElement) {
                errorElement.remove();
            }

            if (file) {
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    showFileError(this, 'Only PNG, JPG, and JPEG files are allowed.');
                    this.value = '';
                    return;
                }

                // Check file size (2MB = 2 * 1024 * 1024 bytes)
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    showFileError(this, 'File size must be less than 2MB.');
                    this.value = '';
                    return;
                }

                // Show success message
                showFileSuccess(this, `File selected: ${file.name} (${formatFileSize(file.size)})`);
            }
        });
    });

    function showFileError(input, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'file-error text-danger mt-1';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
        input.classList.add('is-invalid');
    }

    function showFileSuccess(input, message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'file-success text-success mt-1';
        successDiv.textContent = message;
        input.parentNode.appendChild(successDiv);
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
