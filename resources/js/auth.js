/**
 * Auth.js - Shared functions for authentication views
 * Toggle password visibility & password strength indicator
 */

/**
 * Toggle password field visibility
 * @param {string} fieldId - The ID of the password input field
 * @param {HTMLElement} btn - The toggle button element
 */
function togglePassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    const isPassword = field.type === 'password';
    field.type = isPassword ? 'text' : 'password';

    const eyeOpen = '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>';
    const eyeClosed = '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17.94 17.94a10.07 10.07 0 0 1 -11.291 -11.291"/><path d="M10.5 10.5a2 2 0 1 0 3.511 3.511"/><path d="M8.4 8.4l7.6 7.6"/><path d="M21 3l-6 6"/><path d="M3 3l6 6"/></svg>';

    btn.innerHTML = isPassword ? eyeClosed : eyeOpen;
}

/**
 * Initialize password strength indicator on a password field
 * @param {string} fieldId - The ID of the password input field
 * @param {string} fillId - The ID of the strength fill element
 * @param {string} textId - The ID of the strength text element
 */
function initPasswordStrength(fieldId, fillId, textId) {
    const field = document.getElementById(fieldId);
    const strengthFill = document.getElementById(fillId);
    const strengthText = document.getElementById(textId);

    if (!field || !strengthFill || !strengthText) return;

    field.addEventListener('input', function () {
        const password = this.value;
        let strength = 0;

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;

        const levels = {
            0: { width: '0%', color: '#e5e7eb', label: 'Masukkan password', textColor: '#9ca3af' },
            1: { width: '20%', color: '#ef4444', label: 'Sangat lemah', textColor: '#ef4444' },
            2: { width: '40%', color: '#f59e0b', label: 'Lemah', textColor: '#f59e0b' },
            3: { width: '60%', color: '#06b6d4', label: 'Cukup', textColor: '#06b6d4' },
            4: { width: '80%', color: '#10b981', label: 'Kuat', textColor: '#10b981' },
            5: { width: '100%', color: '#10b981', label: 'Sangat kuat', textColor: '#10b981' }
        };

        const level = password.length === 0 ? levels[0] : levels[strength];

        strengthFill.style.width = level.width;
        strengthFill.style.backgroundColor = level.color;
        strengthText.textContent = level.label;
        strengthText.style.color = level.textColor;
    });
}

// Expose to global scope for inline onclick handlers
window.togglePassword = togglePassword;
window.initPasswordStrength = initPasswordStrength;
