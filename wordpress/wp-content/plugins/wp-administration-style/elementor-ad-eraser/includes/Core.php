<?php
namespace Elementor_Ad_Eraser;

defined('ABSPATH') || exit();

class Core {
    public function __construct() {
        $this->add_custom_styles();
        $this->remove_elementor_ai();
    }

    private function add_custom_styles() {
        add_action('admin_enqueue_scripts', function () {
            require Globals::dir('/includes/is_gutenberg_active.php');

            if (is_gutenberg_active()) {
                wp_enqueue_style(Globals::$tag . '-gutenberg', Globals::url('/static/css/gutenberg.css'), [], Globals::$version);
            }

            wp_enqueue_style(Globals::$tag . '-dashboard', Globals::url('/static/css/dashboard.css'), [], Globals::$version);
        });

        add_action('elementor/editor/after_enqueue_styles', fn() => wp_enqueue_style(Globals::$tag . '-elementor-editor', Globals::url('/static/css/elementor-editor.css'), [], Globals::$version));

        add_action('elementor/preview/enqueue_styles', fn() => wp_enqueue_style(Globals::$tag . '-elementor-preview', Globals::url('/static/css/elementor-preview.css'), [], Globals::$version));

        add_action(
            'admin_enqueue_scripts',
            function () {
                if (!is_plugin_active('elementor-pro/elementor-pro.php')) {
                    wp_enqueue_style(Globals::$tag . '-dashboard-no-elementor-pro', Globals::url('/static/css/dashboard-no-elementor-pro.css'), [], Globals::$version);
                    wp_enqueue_script(Globals::$tag . '-dashboard-no-elementor-pro', Globals::url('/static/js/dashboard-no-elementor-pro.js'), [], Globals::$version);
                }
            },
            100
        );
    }

    private function remove_elementor_ai() {
        add_action(
            'elementor/editor/after_enqueue_styles',
            function () {
                wp_dequeue_style('elementor-ai-editor');
            },
            100
        );

        add_action(
            'elementor/preview/enqueue_styles',
            function () {
                wp_dequeue_style('elementor-ai-layout-preview');
            },
            100
        );

        if (is_admin()) {
            add_action(
                'wp_enqueue_media',
                function () {
                    wp_dequeue_script('elementor-ai-media-library');
                },
                100
            );
        }

        add_action(
            'elementor/editor/before_enqueue_scripts',
            function () {
                wp_dequeue_script('elementor-ai');
                wp_dequeue_script('elementor-ai-layout');
            },
            100
        );

        add_action(
            'enqueue_block_editor_assets',
            function () {
                wp_dequeue_script('elementor-ai-gutenberg');
            },
            100
        );

        add_action(
            'admin_enqueue_scripts',
            function () {
                wp_dequeue_script('elementor-ai-admin');

                // "Optimize your images to enhance site performance by using Image Optimizer". I think this is a paid plugin (or at least it requires login). Not sure if this works.
                wp_dequeue_script('media-hints');
            },
            100
        );
    }
}

new Core();
