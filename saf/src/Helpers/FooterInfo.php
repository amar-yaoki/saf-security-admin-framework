<?php
namespace SAF\Helpers;
defined( 'ABSPATH' ) || exit;

class FooterInfo {
    public function init(): void {
        add_shortcode( 'saf_footer_info', [ $this, 'renderShortcode' ] );
    }

    public function renderShortcode( $atts = [] ): string {
        $atts = shortcode_atts( [
            'mostra' => 'nome,piva,email',
        ], $atts, 'saf_footer_info' );

        $org   = $this->getOrgData();
        $mostra = array_map( 'trim', explode( ',', $atts['mostra'] ) );
        $parts = [];

        if ( in_array( 'nome', $mostra ) && ! empty( $org['name'] ) ) {
            $parts[] = '<strong>' . esc_html( $org['name'] ) . '</strong>';
        }
        if ( in_array( 'piva', $mostra ) && ! empty( $org['piva'] ) ) {
            $parts[] = 'P.IVA ' . esc_html( $org['piva'] );
        }
        if ( in_array( 'address', $mostra ) && ! empty( $org['address'] ) ) {
            $addr = esc_html( $org['address'] );
            if ( $org['cap'] || $org['city'] ) {
                $addr .= ', ' . esc_html( trim( $org['cap'] . ' ' . $org['city'] ) );
            }
            $parts[] = $addr;
        }
        if ( in_array( 'email', $mostra ) && ! empty( $org['email'] ) ) {
            $parts[] = '<a href="mailto:' . esc_attr( $org['email'] ) . '">' . esc_html( $org['email'] ) . '</a>';
        }
        if ( in_array( 'phone', $mostra ) && ! empty( $org['phone'] ) ) {
            $parts[] = '<a href="tel:' . esc_attr( preg_replace( '/\s+/', '', $org['phone'] ) ) . '">' . esc_html( $org['phone'] ) . '</a>';
        }

        if ( empty( $parts ) ) return '';
        return '<span class="saf-footer-info">' . implode( ' &mdash; ', $parts ) . '</span>';
    }

    private function getOrgData(): array {
        $org = (array) get_option( 'saf_org_settings', [] );
        return [
            'name'    => $org['name']    ?? '',
            'piva'    => $org['piva']    ?? '',
            'address' => $org['address'] ?? '',
            'cap'     => $org['cap']     ?? '',
            'city'    => $org['city']    ?? '',
            'email'   => $org['email']   ?? '',
            'phone'   => $org['phone']   ?? '',
        ];
    }
}
