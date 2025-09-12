<?php

namespace HeadlessWPAdmin\Core\Services;

use HeadlessWPAdmin\Core\HeadlessHandler;

class SecurityService
{
    private HeadlessHandler $headlessHandler;

    public function __construct(HeadlessHandler $headlessHandler)
    {
        $this->headlessHandler = $headlessHandler;
    }

    public function configure(): void
    {
        if ($this->headlessHandler->get_setting('security_headers_enabled')) {
            $this->setup_security_headers();
        }

        if ($this->headlessHandler->get_setting('block_theme_access')) {
            $this->block_theme_access();
        }
    }

    private function setup_security_headers(): void
    {
        add_action('send_headers', function () {
            if (!is_admin()) {
                header('X-Content-Type-Options: nosniff');
                header('X-Frame-Options: SAMEORIGIN');
                header('X-XSS-Protection: 1; mode=block');
                header('Referrer-Policy: strict-origin-when-cross-origin');
            }
        });
    }

    private function block_theme_access(): void
    {
        add_action('init', function () {
            if (!is_admin() && !defined('DOING_AJAX')) {
                $request = $_SERVER['REQUEST_URI'] ?? '';
                if (preg_match('/\/(wp-content\/themes\/.*\.php)/', $request)) {
                    wp_die('Acceso denegado', 'Error 403', ['response' => 403]);
                }
            }
        });
    }
}
