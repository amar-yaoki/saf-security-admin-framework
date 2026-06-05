<?php
/**
 * inc/performance.php
 * Ottimizzazioni performance WordPress — SAF � Security & Admin Framework.
 *
 * Sezione 25 — Rimozione script e stili inutili di WP core
 * Sezione 26 — Disabilitazione oEmbed e link correlati
 * Sezione 27 — Disabilitazione heartbeat in frontend
 * Sezione 28 — Ottimizzazione revisioni post
 * Sezione 29 — Lazy load nativo immagini extra
 * Sezione 30 — Rimozione CSS Contact Form 7 dove non serve (opzionale)
 */

defined( 'ABSPATH' ) || exit;


/* ============================================================
   SEZIONE 25 — RIMOZIONE SCRIPT E STILI INUTILI WP CORE
   ============================================================ */

add_action( 'wp_enqueue_scripts', 'saf_deregister_unused_assets', 100 );
function saf_deregister_unused_assets() {

    // Gutenberg — già in theme-setup ma ripetiamo per sicurezza
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'global-styles' );
    wp_dequeue_style( 'classic-theme-styles' );

    // jQuery migrate (non necessario con WP 6+)
    // ATTENZIONE: alcuni plugin vecchi potrebbero dipenderne
    // wp_deregister_script( 'jquery-migrate' );

    // Dashicons in frontend (usate solo da admin bar)
    if ( ! is_user_logged_in() ) {
        wp_dequeue_style( 'dashicons' );
    }
}


/* ============================================================
   SEZIONE 26 — DISABILITAZIONE oEmbed
   Rimuove il JS oEmbed e i link discovery dal head.
   Mantenere attivo se usi embed di YouTube/Twitter nativi WP.
   Commentare il blocco per riattivare.
   ============================================================ */

add_action( 'init', 'saf_disable_oembed' );
function saf_disable_oembed() {
    // Rimuovi endpoint REST oEmbed
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    // Rimuovi filtro oEmbed dal contenuto
    remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
    // Rimuovi link discovery dal head
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    // Rimuovi tinymce plugin
    add_filter( 'tiny_mce_plugins', function( $plugins ) {
        return array_diff( $plugins, array( 'wpembed' ) );
    } );
    // Rimuovi script embed
    wp_deregister_script( 'wp-embed' );
}


/* ============================================================
   SEZIONE 27 — DISABILITAZIONE HEARTBEAT IN FRONTEND
   Il heartbeat WP consuma risorse — in frontend non serve.
   Rimane attivo in backend per autosave e blocchi Gutenberg.
   ============================================================ */

add_action( 'init', 'saf_optimize_heartbeat' );
function saf_optimize_heartbeat() {
    if ( ! is_admin() ) {
        wp_deregister_script( 'heartbeat' );
    }
}


/* ============================================================
   SEZIONE 28 — OTTIMIZZAZIONE REVISIONI POST
   Limita le revisioni salvate per post (default WP = illimitate)
   Cambia il valore in base alle esigenze del progetto.
   ============================================================ */

if ( ! defined( 'WP_POST_REVISIONS' ) ) {
    define( 'WP_POST_REVISIONS', 5 );
}

// Intervallo autosave: da 60s (default) a 120s
if ( ! defined( 'AUTOSAVE_INTERVAL' ) ) {
    define( 'AUTOSAVE_INTERVAL', 120 );
}


/* ============================================================
   SEZIONE 29 — LAZY LOAD NATIVO EXTRA
   WP core aggiunge loading="lazy" alle immagini nel contenuto.
   Questo filtro lo aggiunge anche alle thumbnail custom.
   ============================================================ */

add_filter( 'wp_get_attachment_image_attributes', 'saf_add_lazy_loading' );
function saf_add_lazy_loading( $attr ) {
    if ( ! isset( $attr['loading'] ) ) {
        $attr['loading'] = 'lazy';
    }
    return $attr;
}


/* ============================================================
   SEZIONE 30 — RIMOZIONE CSS/JS CONTACT FORM 7 GLOBALE
   CF7 carica CSS e JS su tutte le pagine — questo li rimuove
   e li ricarica solo sulle pagine che contengono il form.
   Commentare se non usi CF7.
   ============================================================ */

add_action( 'wp_enqueue_scripts', 'saf_cf7_dequeue', 99 );
function saf_cf7_dequeue() {
    if ( ! is_singular() ) return;

    global $post;
    if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'contact-form-7' ) ) {
        return; // Ha il form — lascia caricare
    }

    wp_dequeue_style( 'contact-form-7' );
    wp_dequeue_script( 'contact-form-7' );
    wp_dequeue_style( 'contact-form-7-rtl' );
}
