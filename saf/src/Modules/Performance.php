<?php
defined( 'ABSPATH' ) || exit;
namespace SAF\Modules;

class Performance {
    public function init(): void {
        add_action( 'init', [ $this, 'removeEmojiScripts' ] );
        add_action( 'init', [ $this, 'removeEmbedScripts' ] );
        add_action( 'init', [ $this, 'removeJqueryMigrate' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'deferNonCriticalCss' ], 999 );
        add_action( 'wp_enqueue_scripts', [ $this, 'deferNonCriticalScripts' ], 999 );
    }

    public function removeEmojiScripts(): void {
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_action( 'wp_head', 'print_emoji_detection_image', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        add_filter( 'emoji_svg_url', '__return_false' );
    }

    public function removeEmbedScripts(): void {
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        remove_action( 'rest_api_init', 'wp_oembed_register_route' );
        add_filter( 'embed_oembed_discover', '__return_false' );
        remove_filter( 'oembed_dataparams', 'wp_filter_oembed_result' );
        remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result' );
        add_filter( 'tiny_mce_plugins', [ $this, 'disableEmbedsTinyMce' ] );
        add_action( 'wp_enqueue_scripts', function () {
            wp_deregister_script( 'wp-embed' );
        }, 999 );
    }

    public function disableEmbedsTinyMce( array $plugins ): array {
        return array_diff( $plugins, [ 'wpembed' ] );
    }

    public function removeJqueryMigrate(): void {
        add_filter( 'wp_default_scripts', function ( $scripts ) {
            if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
                $jquery_deps = $scripts->registered['jquery']->deps;
                $scripts->registered['jquery']->deps = array_diff( $jquery_deps, [ 'jquery-migrate' ] );
            }
        } );
    }

    public function deferNonCriticalCss(): void {
        $adv = get_option( 'saf_adv_settings', [] );
        if ( empty( $adv['perf_defer_css'] ) ) return;
        add_filter( 'style_loader_tag', function ( string $html, string $handle ) {
            $skip = [ 'admin-bar', 'dashicons', 'wp-block-library', 'saf-admin' ];
            if ( in_array( $handle, $skip, true ) ) return $html;
            if ( strpos( $html, 'media="print"' ) !== false ) return $html;
            $replacement = "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"";
            $new_rel = preg_replace( "/rel=['\"]stylesheet['\"]/", $replacement, $html );
            return $new_rel . '<noscript>' . $html . '</noscript>';
        }, 10, 2 );
    }

    public function deferNonCriticalScripts(): void {
        $adv = get_option( 'saf_adv_settings', [] );
        if ( empty( $adv['perf_defer_js'] ) ) return;
        add_filter( 'script_loader_tag', function ( string $tag, string $handle ) {
            $skip = [ 'admin-bar', 'jquery', 'jquery-core', 'jquery-migrate' ];
            if ( in_array( $handle, $skip, true ) ) return $tag;
            if ( strpos( $tag, 'defer' ) !== false ) return $tag;
            return str_replace( ' src=', ' defer src=', $tag );
        }, 10, 2 );
    }
}
