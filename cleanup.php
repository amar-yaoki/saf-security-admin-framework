<?php
/**
 * inc/cleanup.php
 * Pulizia backend WordPress — SAF � Security & Admin Framework.
 *
 * Sezione 70 — Disabilitazione commenti completa
 * Sezione 71 — Pulizia menu admin (voci inutili per il cliente)
 * Sezione 72 — Pulizia admin bar frontend
 * Sezione 73 — Rimozione colonne inutili nelle liste post
 * Sezione 74 — Personalizzazione footer admin
 *
 * NOTA: La visibilità delle voci di menu è configurabile da
 * ⚙️ Dati Sito → Tab Avanzate → "Pulizia menu admin".
 * Gli Amministratori vedono sempre tutto.
 */

defined( 'ABSPATH' ) || exit;


/* ============================================================
   SEZIONE 70 — DISABILITAZIONE COMMENTI COMPLETA
   Disabilita commenti su tutti i post type esistenti e futuri.
   Configurabile da ⚙️ Dati Sito → Avanzate.
   ============================================================ */

add_action( 'init', 'saf_maybe_disable_comments' );
function saf_maybe_disable_comments() {
    $adv = (array) get_option( 'saf_adv_settings', array() );
    if ( empty( $adv['disable_comments'] ) ) return;

    // Rimuovi supporto commenti da tutti i post type registrati
    foreach ( get_post_types() as $pt ) {
        if ( post_type_supports( $pt, 'comments' ) ) {
            remove_post_type_support( $pt, 'comments' );
            remove_post_type_support( $pt, 'trackbacks' );
        }
    }
}

// Chiudi commenti su tutti i post esistenti
add_filter( 'comments_open',  'saf_filter_comments_status', 20, 2 );
add_filter( 'pings_open',     'saf_filter_comments_status', 20, 2 );
function saf_filter_comments_status( $open, $post_id ) {
    $adv = (array) get_option( 'saf_adv_settings', array() );
    if ( ! empty( $adv['disable_comments'] ) ) return false;
    return $open;
}

// Restituisce array vuoto per i commenti
add_filter( 'comments_array', 'saf_filter_comments_array', 10, 2 );
function saf_filter_comments_array( $comments, $post_id ) {
    $adv = (array) get_option( 'saf_adv_settings', array() );
    if ( ! empty( $adv['disable_comments'] ) ) return array();
    return $comments;
}

// Rimuovi pagina commenti dall'admin e dal menu
add_action( 'admin_menu', 'saf_remove_comments_menu' );
function saf_remove_comments_menu() {
    $adv = (array) get_option( 'saf_adv_settings', array() );
    if ( empty( $adv['disable_comments'] ) ) return;
    remove_menu_page( 'edit-comments.php' );
}

// Rimuovi commenti dalla admin bar
add_action( 'wp_before_admin_bar_render', 'saf_remove_comments_adminbar' );
function saf_remove_comments_adminbar() {
    $adv = (array) get_option( 'saf_adv_settings', array() );
    if ( empty( $adv['disable_comments'] ) ) return;
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'comments' );
}

// Redirect tentativi di accesso diretti alla pagina commenti
add_action( 'admin_init', 'saf_redirect_comments_page' );
function saf_redirect_comments_page() {
    $adv = (array) get_option( 'saf_adv_settings', array() );
    if ( empty( $adv['disable_comments'] ) ) return;

    global $pagenow;
    if ( $pagenow === 'edit-comments.php' || $pagenow === 'comment.php' ) {
        wp_safe_redirect( admin_url() );
        exit;
    }
}

// Rimuovi bubble commenti in sospeso dal menu
add_filter( 'wp_count_comments', 'saf_hide_comments_count', 10, 2 );
function saf_hide_comments_count( $count, $post_id ) {
    $adv = (array) get_option( 'saf_adv_settings', array() );
    if ( empty( $adv['disable_comments'] ) ) return $count;
    return (object) array(
        'approved'            => 0,
        'spam'                => 0,
        'trash'               => 0,
        'post-trashed'        => 0,
        'total_comments'      => 0,
        'all'                 => 0,
        'moderated'           => 0,
    );
}


/* ============================================================
   SEZIONE 71 — PULIZIA MENU ADMIN
   Rimuove voci inutili per il cliente finale.
   Solo per ruoli NON amministratori (gli admin vedono tutto).
   Configurabile da ⚙️ Dati Sito → Avanzate.
   ============================================================ */

add_action( 'admin_menu', 'saf_cleanup_admin_menu', 999 );
function saf_cleanup_admin_menu() {
    // Non toccare niente per gli amministratori
    if ( current_user_can( 'manage_options' ) ) return;

    $adv  = (array) get_option( 'saf_adv_settings', array() );
    $hide = $adv['hide_menu_items'] ?? array();

    $menu_map = array(
        'tools'      => 'tools.php',
        'comments'   => 'edit-comments.php',
        'themes'     => 'themes.php',
        'plugins'    => 'plugins.php',
        'users'      => 'users.php',
        'settings'   => 'options-general.php',
        'projects'   => 'edit.php?post_type=project',
    );

    foreach ( $hide as $key => $active ) {
        if ( ! empty( $active ) && isset( $menu_map[ $key ] ) ) {
            remove_menu_page( $menu_map[ $key ] );
        }
    }

    // Voci personalizzate (slug arbitrari, uno per riga)
    $custom_raw = $adv['custom_hide'] ?? '';
    if ( ! empty( trim( $custom_raw ) ) ) {
        $custom_slugs = array_filter( array_map( 'trim', explode( "\n", $custom_raw ) ) );
        foreach ( $custom_slugs as $slug ) {
            remove_menu_page( $slug );
        }
    }
}


/* ============================================================
   SEZIONE 72 — PULIZIA ADMIN BAR FRONTEND
   Rimuove voci ridondanti dalla barra admin in frontend.
   ============================================================ */

add_action( 'wp_before_admin_bar_render', 'saf_cleanup_admin_bar_frontend' );
function saf_cleanup_admin_bar_frontend() {
    if ( is_admin() ) return;
    global $wp_admin_bar;

    // Rimuovi logo WordPress (link a wordpress.org — non serve al cliente)
    $wp_admin_bar->remove_menu( 'wp-logo' );
    // Rimuovi "Aggiornamenti" dalla barra frontend
    $wp_admin_bar->remove_menu( 'updates' );
    // Rimuovi "Commenti" se disabilitati
    $adv = (array) get_option( 'saf_adv_settings', array() );
    if ( ! empty( $adv['disable_comments'] ) ) {
        $wp_admin_bar->remove_menu( 'comments' );
    }
}


/* ============================================================
   SEZIONE 73 — RIMOZIONE COLONNE INUTILI NELLE LISTE POST
   ============================================================ */

// Rimuovi colonna commenti dalle liste post e pagine
add_filter( 'manage_posts_columns', 'saf_remove_comments_column' );
add_filter( 'manage_pages_columns', 'saf_remove_comments_column' );
function saf_remove_comments_column( $columns ) {
    $adv = (array) get_option( 'saf_adv_settings', array() );
    if ( ! empty( $adv['disable_comments'] ) ) {
        unset( $columns['comments'] );
    }
    return $columns;
}


/* ============================================================
   SEZIONE 74 — PERSONALIZZAZIONE FOOTER ADMIN
   Sostituisce il testo "Grazie per aver creato con WordPress"
   con il nome del tema/agenzia.
   ============================================================ */

add_filter( 'admin_footer_text', 'saf_admin_footer_text' );
function saf_admin_footer_text() {
    $html = saf_get_credits_html();

    // Aggiungi crediti sviluppatore se impostati
    $credits = (array) get_option( 'saf_credits_settings', array() );
    $author  = $credits['author_name'] ?? '';
    $url     = $credits['author_url'] ?? '';
    if ( $author ) {
        $link   = $url ? '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $author ) . '</a>' : esc_html( $author );
        $html  .= ' · ' . saf_t( 'footer_dev_by' ) . ' ' . $link;
    }

    echo $html;
}

add_filter( 'update_footer', '__return_empty_string', 11 );
