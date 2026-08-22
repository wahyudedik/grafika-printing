import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;

// Register sidebar store BEFORE Alpine.start() so it's reactive across all components
document.addEventListener('alpine:init', () => {
    Alpine.store('sidebar', {
        collapsed: false,
        mobileOpen: false,
    });
});

Alpine.start();

import Swal from 'sweetalert2';
window.Swal = Swal;

// Lazy load chart libraries hanya saat diperlukan
// ApexCharts: digunakan di dashboard-charts.js dan laporan views
window.loadApexCharts = async () => {
    if (!window.ApexCharts) {
        const { default: ApexCharts } = await import('apexcharts');
        window.ApexCharts = ApexCharts;
    }
    return window.ApexCharts;
};

// Chart.js: digunakan di dev/dashboard dan admin/cms/statistics
window.loadChart = async () => {
    if (!window.Chart) {
        const { default: Chart } = await import('chart.js/auto');
        window.Chart = Chart;
    }
    return window.Chart;
};

// SortableJS: digunakan di vendor/linktree/products
window.loadSortable = async () => {
    if (!window.Sortable) {
        const { default: Sortable } = await import('sortablejs');
        window.Sortable = Sortable;
    }
    return window.Sortable;
};

// Dashboard charts (conditionally renders only when chart elements exist)
import './dashboard-charts';

// Auth helpers (togglePassword, initPasswordStrength)
import './auth';
