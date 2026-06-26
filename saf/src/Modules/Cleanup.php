<?php
namespace SAF\Modules;
defined( 'ABSPATH' ) || exit;

class Cleanup {
    public function init(): void {
        add_action( 'init', [ $this, 'disableComments' ] );
        add_action( 'init', [ $this, 'cleanAdminBar' ] );
        add_action( 'init', [ $this, 'removeDefaultPostsPage' ] );
        add_filter( 'dashboard_glance_items', [ $this, 'addPostTypeCounts' ] );
    }

    public function disableComments(): void {
        add_action( 'admin_init', function () {
            global $pagenow;
            if ( $pagenow === 'edit-comments.php' ) {
                wp_safe_redirect( admin_url() ); exit;
            }
            remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        } );
        add_filter( 'comments_open', '__return_false', 20, 2 );
        add_filter( 'pings_open', '__return_false', 20, 2 );
        add_filter( 'comments_array', '__return_empty_array', 10, 2 );
        add_action( 'admin_menu', function () { remove_menu_page( 'edit-comments.php' ); } );
        add_action( 'wp_before_admin_bar_render', function () {
            global $wp_admin_bar;
            if ( $wp_admin_bar ) $wp_admin_bar->remove_menu( 'comments' );
        } );
        add_action( 'admin_init', function () {
            $post_types = get_post_types();
            foreach ( $post_types as $pt ) {
                if ( post_type_supports( $pt, 'comments' ) ) remove_post_type_support( $pt, 'comments' );
                if ( post_type_supports( $pt, 'trackbacks' ) ) remove_post_type_support( $pt, 'trackbacks' );
            }
        } );
    }

    public function cleanAdminBar(): void {
        add_action( 'wp_before_admin_bar_render', function () {
            global $wp_admin_bar;
            if ( ! $wp_admin_bar ) return;
            $wp_admin_bar->remove_menu( 'wp-logo' );
            $wp_admin_bar->remove_menu( 'about' );
            $wp_admin_bar->remove_menu( 'wporg' );
            $wp_admin_bar->remove_menu( 'documentation' );
            $wp_admin_bar->remove_menu( 'support-forums' );
            $wp_admin_bar->remove_menu( 'feedback' );
            $wp_admin_bar->remove_menu( 'view-site' );
            $wp_admin_bar->remove_menu( 'customize' );
        } );
        add_action( 'admin_head', function () {
            echo '<style>#wpadminbar #wp-admin-bar-wp-logo, #wpadminbar #wp-admin-bar-view-site, #wpadminbar #wp-admin-bar-customize { display:none !important; }</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static CSS string
        } );
    }

    public function removeDefaultPostsPage(): void {
        add_action( 'admin_menu', function () {
            remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
            remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
        }, 999 );
    }

    public function addPostTypeCounts( array $items ): array {
        $types = get_post_types( [ 'public' => true, '_builtin' => false ], 'objects' );
        foreach ( $types as $pt ) {
            $count = wp_count_posts( $pt->name );
            $number = (int) ( $count->publish ?? 0 );
            if ( $number > 0 ) {
                $items[] = sprintf(
                    '<a href="%s">%d %s</a>',
                    esc_url( admin_url( 'edit.php?post_type=' . $pt->name ) ),
                    $number,
                    esc_html( $number === 1 ? $pt->labels->singular_name : $pt->labels->name )
                );
            }
        }
        return $items;
    }
}
