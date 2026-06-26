<?php
defined( 'ABSPATH' ) || exit;
namespace SAF\Modules;

class PostStatus {
    public function init(): void {
        add_action( 'admin_menu', [ $this, 'addStatusSubmenu' ] );
        add_filter( 'display_post_states', [ $this, 'showDraftStatus' ], 10, 2 );
        add_action( 'admin_bar_menu', [ $this, 'addDraftStatusAdminBar' ], 100 );
        add_shortcode( 'saf_version_info', [ $this, 'renderVersionShortcode' ] );
    }

    public function addStatusSubmenu(): void {
        $types = get_post_types( [ 'public' => true ], 'objects' );
        foreach ( $types as $type ) {
            if ( $type->name === 'attachment' ) continue;
            $counts = wp_count_posts( $type->name );
            $drafts = (int) ( $counts->draft ?? 0 ) + (int) ( $counts->pending ?? 0 );
            $label = $type->labels->name . ' <span class="awaiting-mod">' . $drafts . '</span>';
            add_submenu_page(
                'edit.php?post_type=' . $type->name,
                $type->labels->name . ' — Bozze',
                $label,
                'edit_posts',
                'saf-drafts-' . $type->name,
                function () use ( $type ) {
                    $url = add_query_arg( [ 'post_status' => 'draft', 'post_type' => $type->name ], 'edit.php' );
                    wp_safe_redirect( $url );
                    exit;
                }
            );
        }
    }

    public function showDraftStatus( array $states, \WP_Post $post ): array {
        if ( $post->post_status === 'draft' ) $states[] = '<span class="saf-status-draft">Bozza</span>';
        if ( $post->post_status === 'pending' ) $states[] = '<span class="saf-status-pending">In attesa di revisione</span>';
        return $states;
    }

    public function addDraftStatusAdminBar( \WP_Admin_Bar $wp_admin_bar ): void {
        if ( ! is_admin() ) return;
        $counts = wp_count_posts( 'post' );
        $drafts = (int) ( $counts->draft ?? 0 ) + (int) ( $counts->pending ?? 0 );
        if ( $drafts > 0 ) {
            $wp_admin_bar->add_node( [
                'id'     => 'saf-drafts',
                'title'  => '<span class="ab-icon dashicons-admin-post"></span> ' . $drafts . ' bozze',
                'href'   => admin_url( 'edit.php?post_status=draft' ),
                'parent' => 'top-secondary',
            ] );
        }
    }

    public function renderVersionShortcode(): string {
        $output = '<div class="saf-version-info">';
        $output .= '<p><strong>Versione PHP:</strong> ' . esc_html( phpversion() ) . '</p>';
        $output .= '<p><strong>Versione WordPress:</strong> ' . esc_html( get_bloginfo( 'version' ) ) . '</p>';
        $output .= '<p><strong>Versione SAF:</strong> ' . esc_html( SAF_VERSION ) . '</p>';
        $output .= '</div>';
        return $output;
    }
}
