<?php
namespace SAF\Helpers;
defined( 'ABSPATH' ) || exit;

class NapHtml {
    public function init(): void {
        add_shortcode( 'saf_nap_html', [ $this, 'renderShortcode' ] );
    }

    public function renderShortcode( $atts = [] ): string {
        $html = get_option( 'saf_nap_html', '' );
        if ( empty( trim( $html ) ) ) return '';

        $atts = shortcode_atts( [ 'class' => '' ], $atts, 'saf_nap_html' );
        $safe_html = wp_kses_post( $html );
        if ( ! empty( $atts['class'] ) ) {
            return '<div class="' . esc_attr( $atts['class'] ) . '">' . $safe_html . '</div>';
        }
        return $safe_html;
    }
}
