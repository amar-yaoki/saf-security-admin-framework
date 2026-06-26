<?php
namespace SAF\Helpers;
defined( 'ABSPATH' ) || exit;

class ReadingTime {
    public function init(): void {
        add_shortcode( 'saf_reading_time', [ $this, 'renderShortcode' ] );
    }

    public function renderShortcode( $atts = [] ): string {
        global $post;
        if ( ! $post ) return '';

        $atts = shortcode_atts( [
            'wpm'    => 200,
            'label'  => 'Lettura: ',
            'suffix' => ' min',
        ], $atts, 'saf_reading_time' );

        $content = get_the_content( null, false, $post );
        $text    = wp_strip_all_tags( $content );
        preg_match_all( '/\p{L}+/u', $text, $matches );
        $words   = count( $matches[0] );
        $minutes = max( 1, (int) ceil( $words / (int) $atts['wpm'] ) );

        return '<span class="saf-reading-time">'
             . esc_html( $atts['label'] )
             . '<strong>' . $minutes . esc_html( $atts['suffix'] ) . '</strong>'
             . '</span>';
    }
}
