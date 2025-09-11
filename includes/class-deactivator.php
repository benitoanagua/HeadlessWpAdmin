<?php
namespace HeadlessWPAdmin;

class Deactivator {
    public static function deactivate() {
        // Flush rewrite rules on deactivation
        flush_rewrite_rules();
    }
}