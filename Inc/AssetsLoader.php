<?php

namespace VisitMarche\ThemeWp\Inc;

class AssetsLoader
{
    const leaflet_js = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    const leaflet_css = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this,'remove_unnecessary_core_styles'], 9999);
        add_action('wp_enqueue_scripts', fn() => $this->mainAssets());
        //add_filter('script_loader_tag', [$this, 'add_defer_attribute'], 10, 2);
    }

    public function mainAssets(): void
    {
        wp_enqueue_style(
            'theme-visit-style',
            get_template_directory_uri().'/style.css'
        );

        wp_enqueue_style(
            'visitmarche-css',
            get_template_directory_uri().'/assets/visit.css',
        );

        wp_enqueue_style(
            'tabler-icons-css',
            'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css',
        );

        wp_enqueue_script(
            'alpine-js',
            '//unpkg.com/alpinejs',
            [],
            false,
            false
        );
    }

    function add_defer_attribute($tag, $handle): string
    {
        // Add defer to Alpine.js and component scripts
        if (in_array($handle, ['marchebe-alpine', 'marchebe-header-nav', 'marchebe-category-show'])) {
            return str_replace(' src', ' defer src', $tag);
        }

        return $tag;
    }
    function remove_unnecessary_core_styles(): void
    {
        // Remove Classic Theme Styles (Often redundant/opinionated CSS)
        wp_dequeue_style('classic-theme-styles');

        // Remove Block Library Theme Styles (Often redundant/opinionated CSS)
        wp_dequeue_style('wp-block-library-theme');

        // Remove Duotone SVG filters (Large inline SVG definitions for image effects)
        remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

        // DO NOT remove 'wp-block-library' (Needed for basic block structure/layouts)
        // DO NOT remove 'global-styles' (Needed for colors, typography, and layout settings)
    }
}
