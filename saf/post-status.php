<?php
if ( defined( 'SAF_VERSION' ) && version_compare( SAF_VERSION, '2.0', '>=' ) ) return;
/**
 * SAF — Post Status: stato "Archivio" per post/pagine/CPT
 *
 * Aggiunge uno stato personalizzato "Archivio" che permette di:
 * - Conservare pagine senza pubblicarle (né averle in bozza)
 * - Escluderle dai risultati di ricerca e dai query standard
 * - Filtrarle nell'elenco admin: Tutti | Pubblicati | Bozze | Archivio
 *
 * SAF v1.0.1
 */

defined( 'ABSPATH' ) || exit;

// ============================================================
// 1. REGISTRA LO STATO "archived"
// ============================================================
add_action( 'init', 'saf_register_archived_status' );

function saf_register_archived_status(): void {
    register_post_status( 'archived', [
        'label'                     => _x( 'Archiviato', 'post status', 'saf' ),
        'label_count'               => _n_noop( 'Archiviati <span class="count">(%s)</span>', 'Archiviati <span class="count">(%s)</span>', 'saf' ),
        'public'                    => false,
        'private'                   => true,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'internal'                  => false,
        'protected'                 => true,
    ] );
}

// ============================================================
// 2. AGGIUNGE AL MENU A TENDINA "AZIONI MASSIVE" NELL'EDIT
// ============================================================
add_action( 'admin_footer-post.php', 'saf_archived_status_js' );
add_action( 'admin_footer-post-new.php', 'saf_archived_status_js' );

function saf_archived_status_js(): void {
    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->post_type, saf_archived_allowed_types(), true ) ) return;
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var sel = document.querySelector('select[name="post_status"]');
        if ( sel ) {
            var opt = document.createElement('option');
            opt.value = 'archived';
            opt.textContent = 'Archiviato';
            sel.appendChild(opt);
        }
        var btn = document.getElementById('post_status_tr');
        if ( btn && document.getElementById('post_status') ) {
            btn.style.display = '';
        }
    });
    </script>
    <?php
}

// ============================================================
// 3. AGGIUNGE FILTRO NELL'ELENCO ADMIN
// ============================================================
add_filter( 'views_edit-post', 'saf_archived_admin_filter' );
add_filter( 'views_edit-page', 'saf_archived_admin_filter' );

function saf_archived_admin_filter( array $views ): array {
    global $wpdb;

    $post_type = get_query_var( 'post_type' ) ?: 'post';
    $count     = (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = %s AND post_status = 'archived'",
        $post_type
    ) );

    if ( $count === 0 ) return $views;

    $current = isset( $_GET['post_status'] ) && sanitize_key( $_GET['post_status'] ) === 'archived' ? 'current' : '';
    $views['archived'] = '<a href="' . esc_url( add_query_arg( 'post_status', 'archived' ) ) . '" class="' . $current . '">'
        . sprintf( _x( 'Archivio <span class="count">(%s)</span>', 'post status filter', 'saf' ), number_format_i18n( $count ) )
        . '</a>';

    return $views;
}

// ============================================================
// 4. ESCLUDE POST ARCHIVIATI DAL FRONTEND (se qualcosa sfugge)
// ============================================================
add_action( 'pre_get_posts', 'saf_exclude_archived_from_query' );

function saf_exclude_archived_from_query( WP_Query $query ): void {
    if ( is_admin() || ! $query->is_main_query() ) return;

    $allowed = [ 'archived' ];
    $current = $query->get( 'post_status' );

    // Se è esplicitamente richiesto "archived", non filtrare
    if ( $current === 'archived' ) return;

    // Esclude "archived" da tutti i query frontend
    $post_status = $query->get( 'post_status' );
    if ( empty( $post_status ) ) {
        $query->set( 'post_status', [ 'publish', 'inherit' ] );
    }
}

// ============================================================
// 5. AGGIUNGE STATO NELLA LISTA RAPIDA (Quick Edit)
// ============================================================
add_action( 'admin_footer-edit.php', 'saf_archived_quickedit_js' );

function saf_archived_quickedit_js(): void {
    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->post_type, saf_archived_allowed_types(), true ) ) return;
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var sel = document.querySelector('select[name="_status"]');
        if ( sel ) {
            var opt = document.createElement('option');
            opt.value = 'archived';
            opt.textContent = 'Archiviato';
            sel.appendChild(opt);
        }
    });
    </script>
    <?php
}

// ============================================================
// 6. AZIONE BULK: SPOSTA IN ARCHIVIO
// ============================================================
add_action( 'admin_footer-edit.php', 'saf_archived_bulk_footer' );

function saf_archived_bulk_footer(): void {
    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->post_type, saf_archived_allowed_types(), true ) ) return;
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var sel = document.querySelector('select[name="action"], select[name="action2"]');
        if (!sel) return;
        var opt = document.createElement('option');
        opt.value = 'saf_archive_bulk';
        opt.textContent = 'Sposta in archivio';
        sel.appendChild(opt);
    });
    </script>
    <?php
}

add_action( 'load-edit.php', 'saf_archived_bulk_handler' );

function saf_archived_bulk_handler(): void {
    $wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
    $action = $wp_list_table->current_action();
    if ( $action !== 'saf_archive_bulk' ) return;

    check_admin_referer( 'bulk-posts' );

    $post_ids = array_map( 'intval', $_GET['post'] ?? $_POST['post'] ?? [] );
    if ( empty( $post_ids ) ) return;

    $count = 0;
    foreach ( $post_ids as $id ) {
        if ( current_user_can( 'edit_post', $id ) ) {
            wp_update_post( [ 'ID' => $id, 'post_status' => 'archived' ] );
            $count++;
        }
    }

    $sendback = remove_query_arg( [ 'action', 'action2', 'post' ], wp_get_referer() );
    wp_safe_redirect( add_query_arg( 'saf_archived', $count, $sendback ) );
    exit;
}

add_action( 'admin_notices', 'saf_archived_bulk_notice' );

function saf_archived_bulk_notice(): void {
    if ( ! empty( $_GET['saf_archived'] ) ) {
        $n = intval( $_GET['saf_archived'] );
        echo '<div class="notice notice-info is-dismissible"><p>'
            . sprintf( esc_html( _n( '%d elemento spostato in archivio.', '%d elementi spostati in archivio.', $n, 'saf' ) ), $n )
            . '</p></div>';
    }
}

// ============================================================
// 7. TIPI DI POST ABILITATI (filtrabile)
// ============================================================
function saf_archived_allowed_types(): array {
    $defaults = [ 'post', 'page' ];
    return apply_filters( 'saf_archived_post_types', $defaults );
}
