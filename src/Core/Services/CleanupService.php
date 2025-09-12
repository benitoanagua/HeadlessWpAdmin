<?php

namespace HeadlessWPAdmin\Core\Services;

use HeadlessWPAdmin\Core\HeadlessHandler;

class CleanupService
{
    private HeadlessHandler $headlessHandler;

    public function __construct(HeadlessHandler $headlessHandler)
    {
        $this->headlessHandler = $headlessHandler;
    }

    public function configure(): void
    {
        if ($this->headlessHandler->get_setting('disable_feeds')) {
            $this->disable_feeds();
        }

        if ($this->headlessHandler->get_setting('disable_sitemaps')) {
            add_filter('wp_sitemaps_enabled', '__return_false');
        }

        if ($this->headlessHandler->get_setting('disable_comments')) {
            $this->disable_comments();
        }

        if ($this->headlessHandler->get_setting('clean_wp_head')) {
            $this->clean_wp_head();
        }

        if ($this->headlessHandler->get_setting('disable_emojis')) {
            $this->disable_emojis();
        }

        if ($this->headlessHandler->get_setting('disable_embeds')) {
            $this->disable_embeds();
        }
    }

    private function disable_feeds(): void
    {
        add_action('do_feed', [$this, 'disable_feeds_handler'], 1);
        add_action('do_feed_rdf', [$this, 'disable_feeds_handler'], 1);
        add_action('do_feed_rss', [$this, 'disable_feeds_handler'], 1);
        add_action('do_feed_rss2', [$this, 'disable_feeds_handler'], 1);
        add_action('do_feed_atom', [$this, 'disable_feeds_handler'], 1);
    }

    public function disable_feeds_handler(): void
    {
        wp_die('Feeds deshabilitados en configuración headless', 'Feeds No Disponibles', ['response' => 403]);
    }

    private function disable_comments(): void
    {
        // Usar funciones anónimas que acepten los parámetros pero siempre devuelvan false
        add_filter('comments_open', function ($open, $post_id) {
            return false;
        }, 20, 2);

        add_filter('pings_open', function ($open, $post_id) {
            return false;
        }, 20, 2);

        remove_menu_page('edit-comments.php');
    }

    private function clean_wp_head(): void
    {
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_shortlink_wp_head');
    }

    private function disable_emojis(): void
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
    }

    private function disable_embeds(): void
    {
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
    }
}
