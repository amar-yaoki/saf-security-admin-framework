<?php
namespace SAF\Helpers;
defined( 'ABSPATH' ) || exit;

class SocialShare {
    public static function getLinks( string $url, string $title, array $networks = [] ): array {
        $default = [ 'facebook', 'twitter', 'linkedin', 'telegram', 'whatsapp' ];
        $networks = ! empty( $networks ) ? $networks : $default;
        $links = [];
        $encoded_url = rawurlencode( $url );
        $encoded_title = rawurlencode( $title );
        foreach ( $networks as $network ) {
            $share_url = '';
            switch ( $network ) {
                case 'facebook':
                    $share_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
                    break;
                case 'twitter':
                    $share_url = 'https://twitter.com/intent/tweet?text=' . $encoded_title . '&url=' . $encoded_url;
                    break;
                case 'linkedin':
                    $share_url = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $encoded_url . '&title=' . $encoded_title;
                    break;
                case 'telegram':
                    $share_url = 'https://t.me/share/url?url=' . $encoded_url . '&text=' . $encoded_title;
                    break;
                case 'whatsapp':
                    $share_url = 'https://wa.me/?text=' . $encoded_title . '%20' . $encoded_url;
                    break;
            }
            if ( ! empty( $share_url ) ) $links[ $network ] = $share_url;
        }
        return $links;
    }

    public static function renderButtons( string $url, string $title, array $networks = [], string $class = '' ): string {
        $links = self::getLinks( $url, $title, $networks );
        if ( empty( $links ) ) return '';
        $html = '<div class="saf-share-buttons ' . esc_attr( $class ) . '">';
        foreach ( $links as $network => $share_url ) {
            $html .= sprintf(
                '<a href="%s" target="_blank" rel="nofollow noopener noreferrer" class="saf-share saf-share--%s" title="Condividi su %s">%s</a>',
                esc_url( $share_url ),
                esc_attr( $network ),
                esc_attr( ucfirst( $network ) ),
                esc_html( ucfirst( $network ) )
            );
        }
        $html .= '</div>';
        return $html;
    }
}
