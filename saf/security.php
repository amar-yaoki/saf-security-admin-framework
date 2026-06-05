<?php
/**
 * inc/security.php
 * Hardening sicurezza WordPress — SAF � Security & Admin Framework.
 *
 * Sezione 11 — Funzioni helper (saf_login_protection_active, saf_get_client_ip)
 * Sezione 12 — Disabilitazione file editor backend + costante emergenza
 * Sezione 13 — Rate limiting tentativi login (nativo PHP, no plugin)
 * Sezione 14 — Blocco enumerazione utenti (?author=N + REST API)
 * Sezione 15 — Disabilitazione XML-RPC
 * Sezione 16 — Rimozione versione WP dagli asset
 * Sezione 17 — Headers di sicurezza HTTP
 * Sezione 18 — Messaggio errore login generico
 * Sezione 19 — Nonce helper per AJAX
 * Sezione 20 — Honeypot anti-spam per form custom
 * Sezione 21 — Blocco spam commenti via REST
 * Sezione 22 — Pagina login brandizzata (css/login.css + logo da Dati Sito)
 *
 * LOGIN: /wp-login.php standard — brandizzata via CSS.
 */

defined( 'ABSPATH' ) || exit;


/* ============================================================
   SEZIONE 11 — FUNZIONI HELPER
   Definite per prime — usate da tutti gli hook successivi
   ============================================================ */

/**
 * Controlla se la protezione sicurezza è attiva.
 * Ritorna false se saf_DISABLE_LOGIN_PROTECTION = true in wp-config.php.
 *
 * EMERGENZA — se non riesci ad accedere, aggiungi in wp-config.php:
 *   define( 'saf_DISABLE_LOGIN_PROTECTION', true );
 * Disabilita: rate limiting login.
 * ⚠️ Rimuovila appena recuperato l'accesso.
 *
 * @return bool
 */
function saf_login_protection_active() {
    return ! ( defined( 'saf_DISABLE_LOGIN_PROTECTION' ) && saf_DISABLE_LOGIN_PROTECTION );
}

/**
 * IP reale del client — Cloudflare-aware.
 * Ordine: CF-Connecting-IP → X-Forwarded-For → X-Real-IP → REMOTE_ADDR
 *
 * @return string
 */
function saf_get_client_ip() {
    $headers = array(
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR',
    );
    foreach ( $headers as $header ) {
        if ( ! empty( $_SERVER[ $header ] ) ) {
            $ip = trim( explode( ',', $_SERVER[ $header ] )[0] );
            if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
                return $ip;
            }
        }
    }
    return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}


/* ============================================================
   SEZIONE 12 — FILE EDITOR BACKEND + COSTANTE EMERGENZA
   ============================================================ */

// NOTA: DISALLOW_FILE_EDIT e DISALLOW_FILE_MODS devono essere definite
// in wp-config.php per avere effetto garantito (WordPress le legge prima
// di caricare i plugin). Se SAF è installato come MU plugin, le righe
// seguenti funzionano correttamente. Se è un plugin normale, aggiungi
// manualmente in wp-config.php:
//   define( 'DISALLOW_FILE_EDIT', true );
//   define( 'DISALLOW_FILE_MODS', false );
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', true );
}
// true blocca anche aggiornamenti automatici — lascia false in sviluppo
if ( ! defined( 'DISALLOW_FILE_MODS' ) ) {
    define( 'DISALLOW_FILE_MODS', false );
}


/* ============================================================
   SEZIONE 13 — RATE LIMITING TENTATIVI LOGIN
   Max N tentativi per IP per 15 minuti — nativo PHP, no plugin
   N configurabile da ⚙️ Dati Sito → Tab Sicurezza (range 3-20)
   ============================================================ */

add_action( 'wp_login_failed', 'saf_on_login_failed' );
function saf_on_login_failed( $username ) {
    $ip  = saf_get_client_ip();
    $key = 'saf_attempts_' . md5( $ip );
    set_transient( $key, ( (int) get_transient( $key ) ) + 1, 15 * MINUTE_IN_SECONDS );
}

add_filter( 'authenticate', 'saf_check_login_rate_limit', 30, 3 );
function saf_check_login_rate_limit( $user, $username, $password ) {
    if ( empty( $username ) && empty( $password ) ) return $user;
    if ( ! saf_login_protection_active() ) return $user;

    $ip       = saf_get_client_ip();
    $attempts = (int) get_transient( 'saf_attempts_' . md5( $ip ) );
    $max      = max( 3, min( 20, (int) get_option( 'saf_max_login_attempts', 5 ) ) );

    if ( $attempts >= $max ) {
        return new WP_Error(
            'too_many_attempts',
            saf_t( 'err_too_many_attempts' )
        );
    }
    return $user;
}

add_action( 'wp_login', 'saf_on_login_success', 10, 2 );
function saf_on_login_success( $user_login, $user ) {
    delete_transient( 'saf_attempts_' . md5( saf_get_client_ip() ) );
}


/* ============================================================
   SEZIONE 14 — BLOCCO ENUMERAZIONE UTENTI
   ============================================================ */

add_action( 'template_redirect', 'saf_block_author_scan' );
function saf_block_author_scan() {
    if ( isset( $_GET['author'] ) && ! is_user_logged_in() ) {
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
}

add_filter( 'rest_endpoints', 'saf_restrict_rest_user_endpoints' );
function saf_restrict_rest_user_endpoints( $endpoints ) {
    if ( ! is_user_logged_in() ) {
        unset( $endpoints['/wp/v2/users'] );
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $endpoints;
}


/* ============================================================
   SEZIONE 15 — DISABILITAZIONE XML-RPC
   ============================================================ */

add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', function() { return array(); } );
remove_action( 'wp_head', 'wp_pingback_url' );

add_filter( 'wp_headers', 'saf_remove_x_pingback_header' );
function saf_remove_x_pingback_header( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
}


/* ============================================================
   SEZIONE 16 — RIMOZIONE VERSIONE WP DAGLI ASSET
   Ottimizzato: get_bloginfo('version') chiamato una sola volta
   ============================================================ */

remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

add_filter( 'script_loader_src', 'saf_strip_wp_version_from_src', 15 );
add_filter( 'style_loader_src',  'saf_strip_wp_version_from_src', 15 );
function saf_strip_wp_version_from_src( $src ) {
    static $wp_ver = null;
    if ( $wp_ver === null ) $wp_ver = get_bloginfo( 'version' );
    if ( strpos( $src, 'ver=' . $wp_ver ) !== false ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}


/* ============================================================
   SEZIONE 17 — HEADERS DI SICUREZZA HTTP
   ============================================================ */

add_action( 'send_headers', 'saf_send_security_headers' );
function saf_send_security_headers() {
    if ( headers_sent() ) return;
    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'X-Content-Type-Options: nosniff' );
    header( 'X-XSS-Protection: 1; mode=block' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()' );
    // HSTS — abilitabile da ⚙️ Dati Sito → Avanzate
    // ⚠️ Attivare SOLO con SSL stabile + Cloudflare Full (Strict)
    if ( function_exists( 'get_option' ) ) {
        static $hsts_checked = false;
        if ( ! $hsts_checked ) {
            $adv = (array) get_option( 'saf_adv_settings', array() );
            if ( ! empty( $adv['hsts_enabled'] ) ) {
                header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );
            }
            $hsts_checked = true;
        }
    }
}


/* ============================================================
   SEZIONE 18 — MESSAGGIO ERRORE LOGIN GENERICO
   Non rivela se è sbagliato username o password
   ============================================================ */

add_filter( 'login_errors', 'saf_generic_login_error' );
function saf_generic_login_error() {
    return saf_t( 'err_invalid_credentials' );
}


/* ============================================================
   SEZIONE 19 — NONCE HELPER PER AJAX
   ============================================================ */

/**
 * Verifica nonce AJAX — da chiamare in ogni handler wp_ajax_*
 * Risponde 403 e termina se nonce non valido.
 *
 * @param string $action Nome azione nonce (default: saf_ajax_nonce)
 */
function saf_verify_ajax_nonce( $action = 'saf_ajax_nonce' ) {
    $nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : '';
    if ( ! wp_verify_nonce( $nonce, $action ) ) {
        wp_send_json_error( array( 'message' => 'Richiesta non autorizzata.' ), 403 );
    }
}

/**
 * Termina con errore JSON — helper per handler AJAX
 *
 * @param string $message
 * @param int    $code HTTP status code
 */
function saf_ajax_error( $message, $code = 400 ) {
    wp_send_json_error( array( 'message' => $message ), $code );
}


/* ============================================================
   SEZIONE 20 — HONEYPOT ANTI-SPAM PER FORM CUSTOM
   ============================================================ */

/**
 * Renderizza il campo honeypot nel form.
 * Uso: <?php saf_honeypot_field(); ?> prima del bottone submit
 */
function saf_honeypot_field() {
    $name = 'saf_hp_' . wp_hash( home_url() . 'honeypot' );
    echo '<div style="position:absolute;left:-9999px;opacity:0;pointer-events:none;" aria-hidden="true">';
    echo '<label for="' . esc_attr( $name ) . '">Non compilare</label>';
    echo '<input type="text" name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" tabindex="-1" autocomplete="off" value="">';
    echo '</div>';
}

/**
 * Verifica campo honeypot.
 * @return bool true = spam | false = legittimo
 */
function saf_is_spam() {
    $name = 'saf_hp_' . wp_hash( home_url() . 'honeypot' );
    return ! empty( $_POST[ $name ] );
}


/* ============================================================
   SEZIONE 21 — BLOCCO SPAM COMMENTI VIA REST
   ============================================================ */

add_filter( 'rest_allow_anonymous_comments', '__return_false' );


/* ============================================================
   SEZIONE 22 — PAGINA LOGIN BRANDIZZATA
   css/login.css + logo dinamico da ⚙️ Dati Sito
   ============================================================ */

add_action( 'login_enqueue_scripts', 'saf_login_styles' );
function saf_login_styles() {
    $login_css = SAF_DIR . 'login.css';
    wp_enqueue_style(
        'saf-login',
        SAF_URL . 'login.css',
        array(),
        file_exists( $login_css ) ? filemtime( $login_css ) : '1.0'
    );
    // Logo dinamico da ⚙️ Dati Sito
    if ( function_exists( 'saf_get_org_data' ) ) {
        $org  = saf_get_org_data();
        $logo = $org['logo'] ?? '';
        if ( $logo ) {
            echo '<style>#login h1 a { background-image: url(' . esc_url( $logo ) . ') !important; }</style>' . "\n";
        }
    }
}

// Messaggio benvenuto sopra il form
add_filter( 'login_message', 'saf_login_welcome_message' );
function saf_login_welcome_message( $message ) {
	if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'lostpassword', 'rp', 'register' ), true ) ) {
		return $message;
	}
	return '<div class="saf-login-welcome"><h2>Accedi</h2><p>Inserisci le tue credenziali per accedere.</p></div>';
}

// URL logo → homepage, testo → nome sito
add_filter( 'login_headerurl',  function() { return home_url( '/' ); } );
add_filter( 'login_headertext', function() { return get_bloginfo( 'name' ); } );

// Sposta language switcher dentro #login sotto #nav (evita layout rotto con flex)
add_action( 'login_footer', function() {
    ?>
    <script>
    (function() {
        var ls = document.querySelector('.language-switcher');
        var nav = document.getElementById('nav');
        var login = document.getElementById('login');
        if (ls && nav && login && ls.parentNode !== login) {
            nav.parentNode.insertBefore(ls, nav.nextSibling);
        }
    })();
    </script>
    <?php
} );
