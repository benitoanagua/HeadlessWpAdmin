<?php
namespace HeadlessWPAdmin\Admin;

class Settings {
    private $option_name = 'headless_wp_settings';

    public function get_settings() {
        return get_option($this->option_name, array());
    }

    public function get_setting($key, $default = null) {
        $settings = $this->get_settings();
        return isset($settings[$key]) ? $settings[$key] : $default;
    }

    public function update_setting($key, $value) {
        $settings = $this->get_settings();
        $settings[$key] = $value;
        return update_option($this->option_name, $settings);
    }
}