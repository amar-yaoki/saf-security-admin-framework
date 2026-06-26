<?php
defined( 'ABSPATH' ) || exit;
namespace SAF\Admin;

class GuidaPage {
    public function init(): void {
        add_action( 'admin_menu', [ $this, 'registerSubmenu' ], 11 );
    }

    public function registerSubmenu(): void {
        add_submenu_page(
            'saf',
            'Guida SAF — Documentazione',
            'Guida',
            'manage_options',
            'saf&tab=guida',
            '__return_false'
        );
    }
}
