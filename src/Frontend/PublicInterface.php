<?php

namespace HeadlessWPAdmin\Frontend;

/**
 * Clase para manejar la interfaz pública
 */
class PublicInterface {
    
    public function __construct() {
        add_action('wp_footer', [$this, 'renderFrontendApp']);
        add_shortcode('headless-wp-admin', [$this, 'renderShortcode']);
    }
    
    public function renderFrontendApp(): void {
        // Renderizar app frontend si es necesario
    }
    
    public function renderShortcode($atts): string {
        $atts = shortcode_atts([
            'type' => 'default'
        ], $atts);
        
        return '<div id="headless-wp-admin-shortcode" data-type="' . esc_attr($atts['type']) . '"></div>';
    }
}
