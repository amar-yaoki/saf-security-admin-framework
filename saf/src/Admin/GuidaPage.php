<?php
namespace SAF\Admin;
defined( 'ABSPATH' ) || exit;

class GuidaPage {
    public function init(): void {
        add_action( 'admin_menu', [ $this, 'registerSubmenu' ], 11 );
        add_action( 'admin_init', [ $this, 'registerGuidaSettings' ] );
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

    public function registerGuidaSettings(): void {
        register_setting( 'saf_guida_group', 'saf_checklist', [ $this, 'sanitizeChecklist' ] );
        register_setting( 'saf_guida_group', 'saf_dev_notes', 'sanitize_textarea_field' );
        register_setting( 'saf_guida_group', 'saf_project_docs', [ $this, 'sanitizeProjectDocs' ] );
    }

    public function sanitizeChecklist( $input ): array {
        $out = [];
        if ( is_array( $input ) ) {
            foreach ( $input as $key => $val ) {
                $out[ sanitize_key( $key ) ] = (bool) $val;
            }
        }
        return $out;
    }

    public function sanitizeProjectDocs( $input ): array {
        $docs = [];
        if ( ! empty( $input['docs'] ) && is_array( $input['docs'] ) ) {
            foreach ( $input['docs'] as $doc ) {
                $url   = esc_url_raw( $doc['url']   ?? '' );
                $title = sanitize_text_field( $doc['title'] ?? '' );
                $date  = sanitize_text_field( $doc['date']  ?? '' );
                $notes = sanitize_textarea_field( $doc['notes'] ?? '' );
                if ( $url ) $docs[] = compact( 'url', 'title', 'date', 'notes' );
            }
        }
        return [ 'docs' => $docs ];
    }
}
