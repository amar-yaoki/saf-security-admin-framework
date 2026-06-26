<?php
namespace SAF\Helpers;
defined( 'ABSPATH' ) || exit;

class YouTube {
    public static function getEmbedUrl( string $url ): string {
        $patterns = [
            '~youtube\.com/watch\?v=([^&]+)~',
            '~youtu\.be/([^?]+)~',
            '~youtube\.com/embed/([^?]+)~',
            '~youtube\.com/shorts/([^?]+)~',
        ];
        $video_id = '';
        foreach ( $patterns as $pattern ) {
            if ( preg_match( $pattern, $url, $matches ) ) {
                $video_id = $matches[1];
                break;
            }
        }
        if ( empty( $video_id ) ) return '';
        return 'https://www.youtube-nocookie.com/embed/' . $video_id;
    }

    public static function renderEmbed( string $url, string $title = '', array $attrs = [] ): string {
        $embed_url = self::getEmbedUrl( $url );
        if ( empty( $embed_url ) ) return '';
        $width  = $attrs['width'] ?? 560;
        $height = $attrs['height'] ?? 315;
        $allow  = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
        return sprintf(
            '<div class="saf-video-wrapper">'
          . '<iframe src="%s" width="%d" height="%d" title="%s" frameborder="0" allow="%s" allowfullscreen loading="lazy"></iframe></div>',
            esc_url( $embed_url ),
            (int) $width,
            (int) $height,
            esc_attr( $title ),
            esc_attr( $allow )
        );
    }

    public static function shortcode( array $atts = [] ): string {
        $atts = shortcode_atts( [
            'url'   => '',
            'title' => '',
            'width' => 560,
            'height' => 315,
        ], $atts, 'saf_youtube' );
        if ( empty( $atts['url'] ) ) return '';
        return self::renderEmbed( $atts['url'], $atts['title'], [
            'width'  => (int) $atts['width'],
            'height' => (int) $atts['height'],
        ] );
    }

    public static function registerShortcode(): void {
        add_shortcode( 'saf_youtube', [ self::class, 'shortcode' ] );
    }
}
