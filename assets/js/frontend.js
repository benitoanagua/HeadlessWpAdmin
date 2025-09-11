import FrontendApp from '../svelte/FrontendApp.svelte';
import '../css/frontend.css';

// Inicializar apps de shortcode
document.querySelectorAll('#headless-wp-admin-shortcode').forEach(element => {
    new FrontendApp({
        target: element,
        props: {
            type: element.dataset.type || 'default'
        }
    });
});
