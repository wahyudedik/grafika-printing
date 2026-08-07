import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

import Swal from 'sweetalert2';
import ApexCharts from 'apexcharts';
import Chart from 'chart.js/auto';
import Sortable from 'sortablejs';

window.Swal = Swal;
window.ApexCharts = ApexCharts;
window.Chart = Chart;
window.Sortable = Sortable;

// Dashboard charts (conditionally renders only when chart elements exist)
import './dashboard-charts';

// Auth helpers (togglePassword, initPasswordStrength)
import './auth';
