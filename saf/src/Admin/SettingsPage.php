<?php
namespace SAF\Admin;
defined( 'ABSPATH' ) || exit;

class SettingsPage {
    public function init(): void {
        add_action( 'admin_init', [ $this, 'registerSettings' ] );
        add_action( 'admin_post_saf_save_settings', [ $this, 'handleSave' ] );
    }

    public function registerSettings(): void {
        register_setting( 'saf_org_group', 'saf_org_settings', [ 'sanitize_callback' => [ $this, 'sanitizeOrg' ] ] );
        register_setting( 'saf_seo_group', 'saf_seo_settings', [ 'sanitize_callback' => [ $this, 'sanitizeSeo' ] ] );
        register_setting( 'saf_sec_group', 'saf_sec_settings', [ 'sanitize_callback' => [ $this, 'sanitizeSec' ] ] );
        register_setting( 'saf_sc_group', 'saf_sc_settings', [ 'sanitize_callback' => [ $this, 'sanitizeSc' ] ] );
        register_setting( 'saf_robots_group', 'saf_robots_content', 'sanitize_textarea_field' );
        register_setting( 'saf_nap_group', 'saf_nap_html', 'wp_kses_post' );
        register_setting( 'saf_adv_group', 'saf_adv_settings', [ 'sanitize_callback' => [ $this, 'sanitizeAdv' ] ] );
        register_setting( 'saf_credits_group', 'saf_credits_settings', [ 'sanitize_callback' => [ $this, 'sanitizeCredits' ] ] );

        if ( ! empty( $_POST['saf_export_robots'] ) && check_admin_referer( 'saf_export_robots', 'saf_export_robots_nonce' ) ) {
            $this->handleRobotsExport();
        }
        if ( ! empty( $_POST['saf_action'] ) && 'cleanup_options' === $_POST['saf_action'] ) {
            $this->handleCleanup();
        }
    }

    public function sanitizeOrg( $input ): array {
        return [
            'name'      => sanitize_text_field( $input['name']      ?? '' ),
            'url'       => esc_url_raw( $input['url']       ?? '' ),
            'logo'      => esc_url_raw( $input['logo']      ?? '' ),
            'address'   => sanitize_text_field( $input['address']   ?? '' ),
            'cap'       => sanitize_text_field( $input['cap']       ?? '' ),
            'city'      => sanitize_text_field( $input['city']      ?? '' ),
            'country'   => strtoupper( sanitize_text_field( $input['country'] ?? 'IT' ) ),
            'piva'      => sanitize_text_field( $input['piva']      ?? '' ),
            'email'     => sanitize_email( $input['email']     ?? '' ),
            'phone'     => sanitize_text_field( $input['phone']     ?? '' ),
            'facebook'  => esc_url_raw( $input['facebook']  ?? '' ),
            'instagram' => esc_url_raw( $input['instagram'] ?? '' ),
            'youtube'   => esc_url_raw( $input['youtube']   ?? '' ),
            'linkedin'  => esc_url_raw( $input['linkedin']  ?? '' ),
            'twitter'   => esc_url_raw( $input['twitter']   ?? '' ),
            'github'    => esc_url_raw( $input['github']    ?? '' ),
            'reddit'    => esc_url_raw( $input['reddit']    ?? '' ),
            'amazon_author' => esc_url_raw( $input['amazon_author'] ?? '' ),
        ];
    }

    public function sanitizeSeo( $input ): array {
        return [
            'og_default'   => esc_url_raw( $input['og_default']   ?? '' ),
            'og_default_2' => esc_url_raw( $input['og_default_2'] ?? '' ),
        ];
    }

    public function sanitizeSec( $input ): array {
        $attempts = absint( $input['max_attempts'] ?? 5 );
        $attempts = max( 3, min( 20, $attempts ) );
        update_option( 'saf_max_login_attempts', $attempts );
        return [ 'max_attempts' => $attempts ];
    }

    public function sanitizeSc( $input ): array {
        $allowed_social = [ 'facebook', 'whatsapp', 'telegram', 'instagram', 'tiktok', 'email', 'reddit', 'github', 'copy' ];
        $allowed_dev    = [ 'github', 'gitlab', 'stackoverflow', 'reddit', 'devto', 'medium', 'linkedin', 'amazon_author', 'x', 'mastodon', 'youtube', 'codepen', 'personal_site' ];
        $clean = [];
        if ( ! empty( $input['social_share'] ) && is_array( $input['social_share'] ) ) {
            $clean['social_share'] = array_values( array_intersect( $input['social_share'], $allowed_social ) );
        } else {
            $clean['social_share'] = [];
        }
        if ( ! empty( $input['dev_enabled'] ) && is_array( $input['dev_enabled'] ) ) {
            $clean['dev_enabled'] = array_values( array_intersect( $input['dev_enabled'], $allowed_dev ) );
        } else {
            $clean['dev_enabled'] = [];
        }
        $clean['dev_urls'] = [];
        if ( ! empty( $input['dev_urls'] ) && is_array( $input['dev_urls'] ) ) {
            foreach ( $input['dev_urls'] as $key => $url ) {
                if ( in_array( $key, $allowed_dev, true ) && ! empty( trim( $url ) ) ) {
                    $clean['dev_urls'][ $key ] = esc_url_raw( trim( $url ) );
                }
            }
        }
        return $clean;
    }

    public function sanitizeAdv( $input ): array {
        $clean = [];
        $clean['smtp_from_name']    = sanitize_text_field( $input['smtp_from_name'] ?? '' );
        $clean['smtp_from_email']   = sanitize_email( $input['smtp_from_email']   ?? '' );
        $clean['disable_comments']  = ! empty( $input['disable_comments'] ) ? 1 : 0;
        $clean['hsts_enabled']      = ! empty( $input['hsts_enabled'] ) ? 1 : 0;
        $clean['hide_progetto']     = ! empty( $input['hide_progetto'] ) ? 1 : 0;
        $clean['enable_svg']        = ! empty( $input['enable_svg'] ) ? 1 : 0;
        $clean['hide_menu_items']   = array_map( 'intval', $input['hide_menu_items'] ?? [] );
        $clean['custom_hide']       = sanitize_textarea_field( $input['custom_hide'] ?? '' );
        return $clean;
    }

    public function sanitizeCredits( $input ): array {
        return [
            'author_name' => sanitize_text_field( $input['author_name'] ?? '' ),
            'author_url'  => esc_url_raw( $input['author_url']  ?? '' ),
            'client_name' => sanitize_text_field( $input['client_name'] ?? '' ),
            'created'     => sanitize_text_field( $input['created']     ?? '' ),
        ];
    }

    private function handleRobotsExport(): void {
        if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'saf_export_robots', 'saf_export_robots_nonce' ) ) return;
        $content = get_option( 'saf_robots_content', '' );
        if ( empty( trim( $content ) ) ) {
            add_action( 'admin_notices', [ $this, 'notifyRobotsEmpty' ] );
            return;
        }
        $site_url = home_url( '/' );
        $content  = str_replace( [ 'https://www.tuosito.it/', 'https://www.tuosito.it', '{{SITE_URL}}' ], $site_url, $content );
        $file     = ABSPATH . 'robots.txt';
        if ( function_exists( 'saf_write_file' ) && saf_write_file( $file, $content ) ) {
            add_action( 'admin_notices', [ $this, 'notifyRobotsOk' ] );
        } else {
            add_action( 'admin_notices', [ $this, 'notifyRobotsErr' ] );
        }
    }

    public function notifyRobotsEmpty(): void {
        echo '<div class="notice notice-warning is-dismissible"><p>Nessun contenuto robots.txt da esportare.</p></div>';
    }

    public function notifyRobotsOk(): void {
        echo '<div class="notice notice-success is-dismissible"><p>robots.txt esportato in ' . esc_html( ABSPATH . 'robots.txt' ) . '</p></div>';
    }

    public function notifyRobotsErr(): void {
        echo '<div class="notice notice-error is-dismissible"><p>Errore scrittura robots.txt.</p></div>';
    }

    private function handleCleanup(): void {
        if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( $_POST['saf_cleanup_nonce'] ?? '', 'saf_cleanup' ) ) return;
        $options = [ 'saf_org_settings', 'saf_seo_settings', 'saf_sec_settings', 'saf_sc_settings', 'saf_robots_content', 'saf_nap_html', 'saf_adv_settings', 'saf_credits_settings', 'saf_guida_group', 'saf_checklist', 'saf_dev_notes', 'saf_project_docs', 'saf_max_login_attempts' ];
        foreach ( $options as $opt ) delete_option( $opt );
        add_action( 'admin_notices', [ $this, 'notifyCleanupOk' ] );
    }

    public function notifyCleanupOk(): void {
        echo '<div class="notice notice-success is-dismissible"><p>Opzioni SAF pulite.</p></div>';
    }

    public function handleSave(): void {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Accesso negato.' );
        check_admin_referer( 'saf_save_settings', 'saf_nonce' );
        if ( isset( $_POST['saf_adv_settings'] ) && is_array( $_POST['saf_adv_settings'] ) ) {
            $settings = $_POST['saf_adv_settings'];
            array_walk_recursive( $settings, function ( &$value ) { $value = sanitize_text_field( $value ); } );
            update_option( 'saf_adv_settings', $settings );
        }
        wp_safe_redirect( add_query_arg( [ 'page' => 'saf', 'tab' => 'settings', 'updated' => '1' ], admin_url( 'admin.php' ) ) );
        exit;
    }
}
