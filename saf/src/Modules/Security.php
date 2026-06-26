<?php
namespace SAF\Modules;
defined( 'ABSPATH' ) || exit;

class Security {
    public function init(): void {
        add_action( 'init', [ $this, 'removeGeneratorTag' ] );
        add_action( 'init', [ $this, 'disableXpingback' ] );
        add_action( 'init', [ $this, 'disableRestUserEndpoint' ] );
        add_filter( 'rest_authentication_errors', [ $this, 'restAuthErrors' ] );
        add_action( 'admin_notices', [ $this, 'showSecurityNotices' ] );
        add_action( 'login_enqueue_scripts', [ $this, 'applyLoginCss' ] );
    }

    public function removeGeneratorTag(): void {
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'rsd_link' );
        add_filter( 'the_generator', '__return_empty_string' );
        add_filter( 'style_loader_src', [ $this, 'removeVersionQuery' ], 9999 );
        add_filter( 'script_loader_src', [ $this, 'removeVersionQuery' ], 9999 );
    }

    public function removeVersionQuery( string $src ): string {
        return $src ? remove_query_arg( 'ver', $src ) : $src;
    }

    public function disableXpingback(): void {
        add_filter( 'pings_open', '__return_false' );
        add_filter( 'xmlrpc_enabled', '__return_false' );
        remove_action( 'wp_head', 'wlwmanifest_link' ); // also handled above
    }

    public function disableRestUserEndpoint(): void {
        add_filter( 'rest_endpoints', function ( array $endpoints ) {
            if ( ! is_user_logged_in() && isset( $endpoints['/wp/v2/users'] ) ) {
                unset( $endpoints['/wp/v2/users'] );
            }
            return $endpoints;
        } );
    }

    public function restAuthErrors( $access ): ?\WP_Error {
        if ( ! is_user_logged_in() ) {
            $current_route = isset( $GLOBALS['wp'] ) ? ( $GLOBALS['wp']->query_vars['rest_route'] ?? '' ) : '';
            if ( $current_route && preg_match( '#^/wp/v2/(users|posts|pages|media|comments)#', $current_route ) ) {
                return new \WP_Error( 'rest_not_logged_in', 'Accesso non autorizzato.', [ 'status' => 401 ] );
            }
        }
        return $access;
    }

    public function showSecurityNotices(): void {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG && current_user_can( 'manage_options' ) ) {
            $msg = sprintf( '<strong>SAF:</strong> WP_DEBUG è attivo. Disabilitalo in <code>wp-config.php</code> in produzione.' );
            echo '<div class="notice notice-warning is-dismissible"><p>' . wp_kses_post( $msg ) . '</p></div>';
        }
    }

    public function applyLoginCss(): void {
        $login_css = get_template_directory() . '/login.css';
        if ( file_exists( $login_css ) ) {
            wp_enqueue_style( 'theme-login', get_template_directory_uri() . '/login.css', [], SAF_VERSION );
        }
        $saf_login = SAF_DIR . 'assets/css/login.css';
        if ( file_exists( $saf_login ) ) {
            wp_enqueue_style( 'saf-login', SAF_URL . 'assets/css/login.css', [], SAF_VERSION );
        }
    }
}
