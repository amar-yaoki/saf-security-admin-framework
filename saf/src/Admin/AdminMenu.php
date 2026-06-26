<?php
defined( 'ABSPATH' ) || exit;
namespace SAF\Admin;

class AdminMenu {
    public function init(): void {
        add_action( 'admin_menu', [ $this, 'registerMenu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAssets' ] );
        add_action( 'admin_post_saf_create_child', [ $this, 'handleCreateChild' ] );
    }

    public function registerMenu(): void {
        add_menu_page(
            'SAF — Security & Admin Framework',
            'SAF',
            'manage_options',
            'saf',
            [ $this, 'renderDashboard' ],
            'data:image/svg+xml;base64,' . base64_encode( $this->getIconSvg() ),
            3
        );
        add_submenu_page( 'saf', 'Dashboard SAF', 'Dashboard', 'manage_options', 'saf', [ $this, 'renderDashboard' ] );
    }

    public function enqueueAssets( string $hook ): void {
        if ( strpos( $hook, 'saf' ) === false && strpos( $hook, 'toplevel_page_saf' ) === false ) return;
        wp_enqueue_style( 'saf-admin', SAF_URL . 'assets/css/admin.css', [], SAF_VERSION );
        wp_enqueue_script( 'saf-admin', SAF_URL . 'assets/js/admin.js', [ 'jquery' ], SAF_VERSION, true );
        wp_localize_script( 'saf-admin', 'safAdmin', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'saf_ajax_nonce' ),
        ] );
    }

    public function renderDashboard(): void {
        $tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'dashboard';
        $allowed_tabs = [ 'dashboard', 'settings', 'modules', 'tools', 'child', 'guida', 'about' ];
        if ( ! in_array( $tab, $allowed_tabs, true ) ) $tab = 'dashboard';
        $credits = '';
        if ( function_exists( 'saf_get_credits_html' ) ) $credits = saf_get_credits_html();
        include SAF_DIR . 'templates/admin/page.php';
    }

    public function handleCreateChild(): void {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Accesso negato.' );
        check_admin_referer( 'saf_create_child', 'saf_child_nonce' );
        $force = ! empty( $_POST['force'] );
        if ( function_exists( 'saf_create_child_theme' ) ) {
            $result = saf_create_child_theme( $force );
        } else {
            $result = 'error_source_missing';
        }
        wp_safe_redirect( add_query_arg( [ 'page' => 'saf', 'tab' => 'child', 'saf_result' => $result ], admin_url( 'admin.php' ) ) );
        exit;
    }

    private function getIconSvg(): string {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#e68a2e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
    }
}
