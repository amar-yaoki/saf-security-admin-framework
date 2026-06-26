<?php
/**
 * PHPUnit bootstrap for SAF v2.
 * Usage: vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/
 */

// Paths
$saf_dir = dirname( __DIR__ ) . '/';

// WordPress test suite (for integration tests)
$wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ?: '/tmp/wordpress-tests-lib';

if ( file_exists( $wp_tests_dir . '/includes/functions.php' ) ) {
    require_once $wp_tests_dir . '/includes/functions.php';
}

// Autoloader
require_once $saf_dir . 'src/Autoloader.php';
SAF\Autoloader::register();

// Mock WP functions for unit tests
if ( ! function_exists( 'get_option' ) ) {
    function get_option( $option, $default = false ) { return $default; }
    function update_option( $option, $value ) { return true; }
    function add_action() {}
    function add_filter() {}
    function add_shortcode() {}
    function __( $text, $domain = 'default' ) { return $text; }
    function esc_html( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
    function esc_url( $url ) { return $url; }
    function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
    function wp_kses_post( $data ) { return $data; }
    function wp_trim_words( $text, $num_words = 55 ) { $words = explode( ' ', $text ); return implode( ' ', array_slice( $words, 0, $num_words ) ); }
    function wp_strip_all_tags( $string ) { return strip_tags( $string ); }
    function wp_create_nonce( $action ) { return md5( $action ); }
    function current_user_can( $capability ) { return true; }
    function is_admin() { return true; }
    function get_locale() { return 'it_IT'; }
    function add_query_arg( ...$args ) { return '?' . http_build_query( $args[0] ?? [] ); }
    function remove_query_arg( $key, $query = false ) { return $query; }
    function plugin_dir_path( $file ) { return dirname( $file ) . '/'; }
    function plugin_dir_url( $file ) { return 'https://example.com/wp-content/plugins/' . basename( dirname( $file ) ) . '/'; }
    function plugin_basename( $file ) { return basename( dirname( $file ) ) . '/' . basename( $file ); }
    function load_plugin_textdomain( $domain, $deprecated = false, $plugin_rel_path = '' ) {}
    function wp_die( $message ) { throw new \RuntimeException( $message ); }
    function wp_safe_redirect( $location ) { throw new \RuntimeException( 'Redirect: ' . $location ); }
    function wp_verify_nonce( $nonce, $action ) { return $nonce === md5( $action ); }
    function wp_enqueue_style() {}
    function wp_enqueue_script() {}
    function wp_localize_script() {}
    function wp_mkdir_p( $path ) { return mkdir( $path, 0777, true ); }
    function wp_upload_dir() { return [ 'basedir' => sys_get_temp_dir() . '/saf-test-uploads' ]; }
    function get_theme_root() { return sys_get_temp_dir() . '/themes'; }
}
