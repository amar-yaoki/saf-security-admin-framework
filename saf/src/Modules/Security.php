<?php
namespace SAF\Modules;
defined( 'ABSPATH' ) || exit;

class Security {
    public function init(): void {
        add_filter( 'authenticate', [ $this, 'checkLoginRateLimit' ], 30, 3 );
        add_action( 'wp_login_failed', [ $this, 'onLoginFailed' ] );
        add_action( 'wp_login', [ $this, 'onLoginSuccess' ], 10, 2 );
        add_action( 'template_redirect', [ $this, 'blockAuthorScan' ] );
        add_filter( 'rest_endpoints', [ $this, 'restrictRestUserEndpoints' ] );
        add_filter( 'rest_authentication_errors', [ $this, 'restAuthErrors' ] );
        add_filter( 'xmlrpc_enabled', '__return_false' );
        add_filter( 'xmlrpc_methods', function() { return []; } );
        add_action( 'wp_head', [ $this, 'removeGeneratorTag' ], 1 );
        add_filter( 'the_generator', '__return_empty_string' );
        add_filter( 'style_loader_src', [ $this, 'removeVersionQuery' ], 15 );
        add_filter( 'script_loader_src', [ $this, 'removeVersionQuery' ], 15 );
        add_action( 'send_headers', [ $this, 'sendSecurityHeaders' ] );
        add_filter( 'login_errors', [ $this, 'genericLoginError' ] );
        add_action( 'login_enqueue_scripts', [ $this, 'applyLoginCss' ] );
        add_filter( 'login_headerurl', fn() => home_url( '/' ) );
        add_filter( 'login_headertext', fn() => get_bloginfo( 'name' ) );
        add_filter( 'login_message', [ $this, 'loginWelcomeMessage' ] );
        add_action( 'admin_notices', [ $this, 'showSecurityNotices' ] );

        if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) define( 'DISALLOW_FILE_EDIT', true );
        if ( ! defined( 'DISALLOW_FILE_MODS' ) ) define( 'DISALLOW_FILE_MODS', false );
    }

    public function getClientIp(): string {
        $headers = [ 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR' ];
        foreach ( $headers as $header ) {
            if ( ! empty( $_SERVER[ $header ] ) ) {
                $ip = trim( explode( ',', $_SERVER[ $header ] )[0] );
                if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
                    return $ip;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    private function loginProtectionActive(): bool {
        return ! ( defined( 'SAF_DISABLE_LOGIN_PROTECTION' ) && SAF_DISABLE_LOGIN_PROTECTION );
    }

    public function onLoginFailed( $username ): void {
        $ip  = $this->getClientIp();
        $key = 'saf_attempts_' . md5( $ip );
        set_transient( $key, ( (int) get_transient( $key ) ) + 1, 15 * MINUTE_IN_SECONDS );
    }

    public function checkLoginRateLimit( $user, $username, $password ) {
        if ( empty( $username ) && empty( $password ) ) return $user;
        if ( ! $this->loginProtectionActive() ) return $user;
        $ip       = $this->getClientIp();
        $attempts = (int) get_transient( 'saf_attempts_' . md5( $ip ) );
        $max      = max( 3, min( 20, (int) get_option( 'saf_max_login_attempts', 5 ) ) );
        if ( $attempts >= $max ) {
            return new \WP_Error( 'too_many_attempts', \__( 'Troppi tentativi. Riprova tra 15 minuti.', 'saf' ) );
        }
        return $user;
    }

    public function onLoginSuccess( $user_login, $user ): void {
        delete_transient( 'saf_attempts_' . md5( $this->getClientIp() ) );
    }

    public function blockAuthorScan(): void {
        if ( isset( $_GET['author'] ) && ! is_user_logged_in() ) {
            wp_safe_redirect( home_url( '/' ), 301 );
            exit;
        }
    }

    public function restrictRestUserEndpoints( $endpoints ): array {
        if ( ! is_user_logged_in() ) {
            unset( $endpoints['/wp/v2/users'] );
            unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
        }
        return $endpoints;
    }

    public function restAuthErrors( $access ) {
        if ( ! is_user_logged_in() ) {
            $current_route = $GLOBALS['wp']->query_vars['rest_route'] ?? '';
            if ( $current_route && preg_match( '#^/wp/v2/(users|posts|pages|media|comments)#', $current_route ) ) {
                return new \WP_Error( 'rest_not_logged_in', 'Accesso non autorizzato.', [ 'status' => 401 ] );
            }
        }
        return $access;
    }

    public function removeGeneratorTag(): void {
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        wp_deregister_script( 'wp-embed' );
    }

    public function removeVersionQuery( string $src ): string {
        if ( ! $src ) return $src;
        static $wp_ver = null;
        if ( $wp_ver === null ) $wp_ver = get_bloginfo( 'version' );
        if ( strpos( $src, 'ver=' . $wp_ver ) !== false ) {
            $src = remove_query_arg( 'ver', $src );
        }
        return $src;
    }

    public function sendSecurityHeaders(): void {
        if ( headers_sent() ) return;
        header( 'X-Frame-Options: SAMEORIGIN' );
        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-XSS-Protection: 1; mode=block' );
        header( 'Referrer-Policy: strict-origin-when-cross-origin' );
        header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()' );
        $adv = (array) get_option( 'saf_adv_settings', [] );
        if ( ! empty( $adv['hsts_enabled'] ) ) {
            header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );
        }
    }

    public function genericLoginError(): string {
        return \__( 'Credenziali non valide.', 'saf' );
    }

    public function applyLoginCss(): void {
        $login_css = SAF_DIR . 'assets/css/login.css';
        if ( file_exists( $login_css ) ) {
            wp_enqueue_style( 'saf-login', SAF_URL . 'assets/css/login.css', [], SAF_VERSION );
        }
        $org = (array) get_option( 'saf_org_settings', [] );
        $logo = $org['logo'] ?? '';
        if ( $logo ) {
            echo '<style>#login h1 a { background-image: url(' . esc_url( $logo ) . ') !important; }</style>';
        }
        echo '<style>.saf-login-welcome h2 { color: #f47D39; }</style>';
    }

    public function loginWelcomeMessage( $message ): string {
        if ( isset( $_GET['action'] ) && in_array( $_GET['action'], [ 'lostpassword', 'rp', 'register' ], true ) ) {
            return $message;
        }
        return '<div class="saf-login-welcome"><h2>' . \__( 'Benvenuto', 'saf' ) . '</h2><p>' . \__( 'Accedi per gestire il sito.', 'saf' ) . '</p></div>';
    }

    public static function honeypotField(): void {
        $name = 'saf_hp_' . wp_hash( home_url() . 'honeypot' );
        echo '<div style="position:absolute;left:-9999px;opacity:0;pointer-events:none;" aria-hidden="true">';
        echo '<label for="' . esc_attr( $name ) . '">Non compilare</label>';
        echo '<input type="text" name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" tabindex="-1" autocomplete="off" value="">';
        echo '</div>';
    }

    public static function isSpam(): bool {
        $name = 'saf_hp_' . wp_hash( home_url() . 'honeypot' );
        return ! empty( $_POST[ $name ] );
    }

    public static function verifyAjaxNonce( string $action = 'saf_ajax_nonce' ): void {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, $action ) ) {
            wp_send_json_error( [ 'message' => 'Nonce non valido.' ], 403 );
        }
    }

    public function showSecurityNotices(): void {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG && current_user_can( 'manage_options' ) ) {
            echo '<div class="notice notice-warning is-dismissible"><p><strong>SAF:</strong> WP_DEBUG è attivo. Disabilitalo in <code>wp-config.php</code> in produzione.</p></div>';
        }
    }
}
