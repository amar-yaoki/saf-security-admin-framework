<?php
defined( 'ABSPATH' ) || exit;
namespace SAF\Admin;

class SettingsPage {
    public function init(): void {
        add_action( 'admin_menu', [ $this, 'registerSubmenu' ], 11 );
        add_action( 'admin_init', [ $this, 'registerSettings' ] );
        add_action( 'admin_post_saf_save_settings', [ $this, 'handleSave' ] );
    }

    public function registerSubmenu(): void {
        add_submenu_page(
            'saf',
            'Impostazioni SAF',
            'Impostazioni',
            'manage_options',
            'saf&tab=settings',
            '__return_false'
        );
    }

    public function registerSettings(): void {
        register_setting( 'saf_settings', 'saf_adv_settings' );
        add_settings_section( 'saf_main', 'Impostazioni Generali', '__return_empty_string', 'saf' );
        add_settings_field( 'saf_enable_seo', 'Modulo SEO', [ $this, 'fieldCheckbox' ], 'saf', 'saf_main', [
            'label' => 'Abilita meta description automatica e supporto excerpt',
            'name'  => 'enable_seo',
        ] );
        add_settings_field( 'saf_enable_svg', 'Caricamento SVG', [ $this, 'fieldCheckbox' ], 'saf', 'saf_main', [
            'label' => 'Abilita caricamento file SVG nella Libreria Media',
            'name'  => 'enable_svg',
        ] );
    }

    public function fieldCheckbox( array $args ): void {
        $options = get_option( 'saf_adv_settings', [] );
        $checked = ! empty( $options[ $args['name'] ] ) ? 'checked' : '';
        printf(
            '<label><input type="checkbox" name="saf_adv_settings[%s]" value="1" %s> %s</label>',
            esc_attr( $args['name'] ), $checked, esc_html( $args['label'] )
        );
    }

    public function handleSave(): void {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Accesso negato.' );
        check_admin_referer( 'saf_save_settings', 'saf_nonce' );
        if ( isset( $_POST['saf_adv_settings'] ) && is_array( $_POST['saf_adv_settings'] ) ) {
            $settings = $_POST['saf_adv_settings'];
            array_walk_recursive( $settings, function ( &$value ) {
                $value = sanitize_text_field( $value );
            } );
            update_option( 'saf_adv_settings', $settings );
        }
        wp_safe_redirect( add_query_arg( [ 'page' => 'saf', 'tab' => 'settings', 'updated' => '1' ], admin_url( 'admin.php' ) ) );
        exit;
    }
}
