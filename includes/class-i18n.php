<?php
namespace HeadlessWPAdmin;

class i18n {
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'headless-wp-admin',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}