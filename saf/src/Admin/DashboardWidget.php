<?php
defined( 'ABSPATH' ) || exit;
namespace SAF\Admin;

class DashboardWidget {
    public function init(): void {
        add_action( 'wp_dashboard_setup', [ $this, 'registerWidget' ] );
    }

    public function registerWidget(): void {
        wp_add_dashboard_widget(
            'saf_dashboard_widget',
            'SAF — Security & Admin Framework',
            [ $this, 'renderWidget' ]
        );
    }

    public function renderWidget(): void {
        $child_exists = is_dir( get_theme_root() . '/amar-design/' ) && file_exists( get_theme_root() . '/amar-design/style.css' );
        $sites = is_multisite() ? get_blog_count() : 1;
        $users = count_users();
        $posts = wp_count_posts();
        $drafts = (int) ( $posts->draft ?? 0 ) + (int) ( $posts->pending ?? 0 );
        echo '<div class="saf-widget-grid">';
        echo '<div class="saf-widget-card"><strong class="saf-widget-value">' . esc_html( $users['total_users'] ) . '</strong><br>Utenti</div>';
        echo '<div class="saf-widget-card"><strong class="saf-widget-value">' . esc_html( $drafts ) . '</strong><br>Bozze</div>';
        echo '<div class="saf-widget-card"><strong class="saf-widget-value">' . esc_html( $sites ) . '</strong><br>Siti</div>';
        echo '<div class="saf-widget-card"><strong class="saf-widget-value">' . ( $child_exists ? '✅' : '❌' ) . '</strong><br>Child Theme</div>';
        echo '</div>';
        echo '<p class="saf-widget-cta"><a href="' . esc_url( admin_url( 'admin.php?page=saf' ) ) . '" class="button button-primary">Apri Dashboard SAF</a></p>';
        if ( function_exists( 'saf_get_credits_html' ) ) {
            echo '<p class="saf-widget-credits">' . wp_kses_post( saf_get_credits_html() ) . '</p>';
        }
    }
}
