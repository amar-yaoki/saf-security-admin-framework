<?php
/**
 * SAF — Duplicate: pulsante duplica per post/pagine/CPT
 *
 * Aggiunge un'azione bulk "Duplica" e un link azione su ogni riga
 * nell'elenco post/pagine/CPT. Copia contenuto, meta, tassonomie,
 * thumbnail, e page builder (Divi 5, Elementor, WPBakery, Gutenberg).
 *
 * SAF v1.0.1
 */

defined( 'ABSPATH' ) || exit;

// ============================================================
// 1. AGGIUNGE LINK "DUPLICA" NELLE AZIONI RIGA
// ============================================================
add_filter( 'post_row_actions', 'saf_duplicate_link', 10, 2 );
add_filter( 'page_row_actions', 'saf_duplicate_link', 10, 2 );

function saf_duplicate_link( array $actions, WP_Post $post ): array {
    if ( ! current_user_can( 'edit_posts' ) ) return $actions;

    $url = wp_nonce_url(
        admin_url( 'admin.php?action=saf_duplicate_post&post_id=' . $post->ID ),
        'saf_duplicate_' . $post->ID
    );

    $actions['saf_duplicate'] = '<a href="' . esc_url( $url ) . '" style="color:#f47D39">'
        . esc_html__( 'Duplica', 'saf' ) . '</a>';

    return $actions;
}

// ============================================================
// 2. AZIONE BULK "DUPLICA" (admin_footer + bulk_action)
// ============================================================
add_action( 'admin_footer-edit.php', 'saf_duplicate_bulk_footer' );

function saf_duplicate_bulk_footer(): void {
    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->post_type, saf_duplicate_allowed_types(), true ) ) return;
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var sel = document.querySelector('select[name="action"], select[name="action2"]');
        if (!sel) return;
        var opt = document.createElement('option');
        opt.value = 'saf_duplicate_bulk';
        opt.textContent = 'Duplica';
        sel.appendChild(opt);
    });
    </script>
    <?php
}

add_action( 'load-edit.php', 'saf_duplicate_bulk_handler' );

function saf_duplicate_bulk_handler(): void {
    $wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
    $action = $wp_list_table->current_action();
    if ( $action !== 'saf_duplicate_bulk' ) return;

    check_admin_referer( 'bulk-posts' );

    $post_ids = array_map( 'intval', $_GET['post'] ?? $_POST['post'] ?? [] );
    if ( empty( $post_ids ) ) return;

    $count = 0;
    foreach ( $post_ids as $id ) {
        if ( saf_duplicate_post( $id ) ) $count++;
    }

    $sendback = remove_query_arg( [ 'action', 'action2', 'post' ], wp_get_referer() );
    wp_safe_redirect( add_query_arg( 'saf_duplicated', $count, $sendback ) );
    exit;
}

add_action( 'admin_notices', 'saf_duplicate_bulk_notice' );

function saf_duplicate_bulk_notice(): void {
    if ( ! empty( $_GET['saf_duplicated'] ) ) {
        $n = intval( $_GET['saf_duplicated'] );
        echo '<div class="notice notice-success is-dismissible"><p>'
            . sprintf( esc_html( _n( '%d elemento duplicato.', '%d elementi duplicati.', $n, 'saf' ) ), $n )
            . '</p></div>';
    }
}

// ============================================================
// 3. ESECUZIONE DUPLICAZIONE
// ============================================================
add_action( 'admin_action_saf_duplicate_post', 'saf_duplicate_action_handler' );

function saf_duplicate_action_handler(): void {
    $post_id = intval( $_GET['post_id'] ?? 0 );
    if ( ! $post_id ) wp_die( 'Post ID mancante.' );

    check_admin_referer( 'saf_duplicate_' . $post_id );

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        wp_die( 'Non autorizzato.' );
    }

    $new_id = saf_duplicate_post( $post_id );

    if ( ! $new_id ) {
        wp_die( 'Errore durante la duplicazione.' );
    }

    wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
    exit;
}

// ============================================================
// 4. FUNZIONE PRINCIPALE DI DUPLICAZIONE
// ============================================================
function saf_duplicate_post( int $post_id ) {
    $post = get_post( $post_id );
    if ( ! $post ) return false;

    // Crea il nuovo post come bozza
    $new_post = [
        'post_title'   => $post->post_title . ' (' . __( 'copia', 'saf' ) . ')',
        'post_content' => $post->post_content,
        'post_excerpt' => $post->post_excerpt,
        'post_status'  => 'draft',
        'post_type'    => $post->post_type,
        'post_author'  => get_current_user_id(),
        'post_parent'  => $post->post_parent,
        'menu_order'   => $post->menu_order,
    ];

    $new_id = wp_insert_post( $new_post );
    if ( is_wp_error( $new_id ) ) return false;

    // Copia thumbnail (post_thumbnail)
    $thumb_id = get_post_thumbnail_id( $post_id );
    if ( $thumb_id ) {
        set_post_thumbnail( $new_id, $thumb_id );
    }

    // Copia tassonomie
    $taxonomies = get_object_taxonomies( $post->post_type );
    foreach ( $taxonomies as $tax ) {
        $terms = wp_get_object_terms( $post_id, $tax, [ 'fields' => 'ids' ] );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            wp_set_object_terms( $new_id, $terms, $tax );
        }
    }

    // Copia meta fields (inclusi Divi/Elementor/ACF)
    $meta = get_post_meta( $post_id );
    foreach ( $meta as $key => $values ) {
        // Salta meta interni WP
        if ( in_array( $key, [ '_edit_lock', '_edit_last', '_wp_old_slug' ], true ) ) continue;
        foreach ( $values as $value ) {
            add_post_meta( $new_id, $key, maybe_unserialize( $value ) );
        }
    }

    // Sincronizza Elementor (se presente)
    if ( defined( 'ELEMENTOR_VERSION' ) ) {
        $doc_type = get_post_meta( $post_id, '_elementor_template_type', true );
        if ( $doc_type ) {
            update_post_meta( $new_id, '_elementor_template_type', $doc_type );
            update_post_meta( $new_id, '_elementor_edit_mode', 'builder' );
        }
    }

    // Sincronizza Divi 5 (se presente)
    if ( function_exists( 'et_pb_is_pagebuilder_used' ) && et_pb_is_pagebuilder_used( $post_id ) ) {
        update_post_meta( $new_id, '_et_pb_use_builder', 'on' );
    }

    return $new_id;
}

// ============================================================
// 5. TIPI DI POST ABILITATI (filtrabile)
// ============================================================
function saf_duplicate_allowed_types(): array {
    $defaults = [ 'post', 'page' ];
    return apply_filters( 'saf_duplicate_post_types', $defaults );
}

// ============================================================
// 6. SUPPORTO PER TIPI DI POST AGGIUNTIVI (hook filtrabile)
// ============================================================
// Usa: add_filter( 'saf_duplicate_post_types', fn($types) => array_merge($types, ['product']) );
