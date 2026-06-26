<?php
namespace SAF\Modules;
defined( 'ABSPATH' ) || exit;

class Duplicate {
    public function init(): void {
        add_filter( 'post_row_actions', [ $this, 'addDuplicateLink' ], 10, 2 );
        add_filter( 'page_row_actions', [ $this, 'addDuplicateLink' ], 10, 2 );
        add_action( 'admin_action_saf_duplicate_post', [ $this, 'handleDuplicate' ] );
    }

    public function addDuplicateLink( array $actions, \WP_Post $post ): array {
        if ( ! current_user_can( 'edit_posts' ) ) return $actions;
        $nonce = wp_create_nonce( 'saf_duplicate_' . $post->ID );
        $url = admin_url( 'admin.php?action=saf_duplicate_post&post=' . $post->ID . '&nonce=' . $nonce );
        $actions['duplicate'] = '<a href="' . esc_url( $url ) . '" title="Duplica questo elemento">Clona</a>';
        return $actions;
    }

    public function handleDuplicate(): void {
        if ( ! isset( $_GET['post'], $_GET['nonce'] ) ) wp_die( 'Parametri mancanti.' );
        $post_id = (int) sanitize_text_field( wp_unslash( $_GET['post'] ) );
        $nonce   = sanitize_text_field( wp_unslash( $_GET['nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'saf_duplicate_' . $post_id ) ) wp_die( 'Nonce non valido.' );
        $post = get_post( $post_id );
        if ( ! $post ) wp_die( 'Post non trovato.' );
        if ( ! current_user_can( 'edit_posts' ) ) wp_die( 'Permessi insufficienti.' );
        $new_post = [
            'post_title'   => $post->post_title . ' (Copia)',
            'post_content' => $post->post_content,
            'post_status'  => 'draft',
            'post_type'    => $post->post_type,
            'post_author'  => get_current_user_id(),
        ];
        $new_id = wp_insert_post( $new_post );
        if ( ! $new_id || is_wp_error( $new_id ) ) wp_die( 'Errore durante la duplicazione.' );
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( $taxonomies as $tax ) {
            $terms = wp_get_post_terms( $post_id, $tax, [ 'fields' => 'slugs' ] );
            if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                wp_set_post_terms( $new_id, $terms, $tax );
            }
        }
        $meta = get_post_meta( $post_id );
        foreach ( $meta as $key => $values ) {
            if ( strpos( $key, '_' ) === 0 && $key !== '_thumbnail_id' ) continue;
            foreach ( $values as $value ) update_post_meta( $new_id, $key, maybe_unserialize( $value ) );
        }
        $thumbnail = get_post_thumbnail_id( $post_id );
        if ( $thumbnail ) set_post_thumbnail( $new_id, $thumbnail );
        wp_safe_redirect( admin_url( 'edit.php?post_type=' . $post->post_type . '&saf_duplicated=1' ) );
        exit;
    }
}
