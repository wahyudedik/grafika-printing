/**
 * File Upload Validation
 * Client-side validation for file uploads in Grafika Printing
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // Configuration
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const ALLOWED_DOCUMENT_TYPES = ['application/pdf', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

    /**
     * Validate file before upload
     * @param {File} file - The file to validate
     * @param {Array} allowedTypes - Allowed MIME types
     * @param {number} maxSize - Maximum file size in bytes
     * @returns {Object} Validation result
     */
    function validateFile(file, allowedTypes, maxSize) {
        const result = { valid: true, errors: [] };

        if (!allowedTypes.includes(file.type)) {
            result.valid = false;
            result.errors.push(`Tipe file "${file.type}" tidak diizinkan. Tipe yang diizinkan: ${allowedTypes.join(', ')}`);
        }

        if (file.size > maxSize) {
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            const maxMB = (maxSize / (1024 * 1024)).toFixed(0);
            result.valid = false;
            result.errors.push(`Ukuran file (${sizeMB}MB) melebihi batas maksimum (${maxMB}MB)`);
        }

        return result;
    }

    /**
     * Show validation error to user
     * @param {string} message - Error message
     * @param {HTMLElement} container - Container element for error display
     */
    function showError(message, container) {
        // Remove existing error
        const existingError = container.querySelector('.upload-error');
        if (existingError) existingError.remove();

        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger upload-error mt-2';
        errorDiv.innerHTML = `<i class="icon fa fa-exclamation-triangle"></i> ${message}`;
        container.appendChild(errorDiv);

        // Auto-remove after 5 seconds
        setTimeout(() => errorDiv.remove(), 5000);
    }

    /**
     * Preview image before upload
     * @param {File} file - Image file
     * @param {HTMLImageElement} previewElement - img element for preview
     */
    function previewImage(file, previewElement) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewElement.src = e.target.result;
                previewElement.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    /**
     * Format file size for display
     * @param {number} bytes - File size in bytes
     * @returns {string} Formatted size
     */
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Attach to all file inputs with validation attribute
    document.querySelectorAll('input[type="file"][data-validate]').forEach(function (input) {
        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const container = input.closest('.form-group') || input.parentElement;
            const validationType = input.dataset.validate;

            let allowedTypes = ALLOWED_IMAGE_TYPES;
            let maxSize = MAX_FILE_SIZE;

            if (validationType === 'document') {
                allowedTypes = ALLOWED_DOCUMENT_TYPES;
            } else if (validationType === 'image') {
                allowedTypes = ALLOWED_IMAGE_TYPES;
            }

            const result = validateFile(file, allowedTypes, maxSize);

            if (!result.valid) {
                showError(result.errors.join('<br>'), container);
                input.value = ''; // Clear the invalid file
                return;
            }

            // Show file info
            const info = container.querySelector('.file-info');
            if (info) {
                info.textContent = `${file.name} (${formatFileSize(file.size)})`;
                info.style.display = 'block';
            }

            // Preview image if preview element exists
            const preview = container.querySelector('.file-preview');
            if (preview && preview.tagName === 'IMG') {
                previewImage(file, preview);
            }
        });
    });

    // Make functions globally available
    window.FileUploadValidation = {
        validateFile: validateFile,
        previewImage: previewImage,
        formatFileSize: formatFileSize,
        ALLOWED_IMAGE_TYPES: ALLOWED_IMAGE_TYPES,
        ALLOWED_DOCUMENT_TYPES: ALLOWED_DOCUMENT_TYPES,
        MAX_FILE_SIZE: MAX_FILE_SIZE
    };
});
