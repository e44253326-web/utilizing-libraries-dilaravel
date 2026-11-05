import './echo';
import '../css/chasier.css';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

if (document.querySelector('[data-admin-dashboard]')) {
    import('./admin-dashboard');
}

if (document.querySelector('[data-chasier]')) {
    import('./chasier');
}