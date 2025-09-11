import AdminApp from '../svelte/AdminApp.svelte';
import '../css/admin.css';

// Inicializar app de administración
const adminContainer = document.getElementById('headless-wp-admin-admin-app');
if (adminContainer) {
    new AdminApp({
        target: adminContainer
    });
}
