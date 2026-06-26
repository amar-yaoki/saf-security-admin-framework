<?php
namespace SAF\Helpers;
defined( 'ABSPATH' ) || exit;

class Breadcrumb {
    public function init(): void {
        add_shortcode( 'saf_breadcrumb', [ $this, 'renderShortcode' ] );
    }

    public function renderShortcode( $atts = [] ): string {
        $atts = shortcode_atts( [
            'sep'        => '&rsaquo;',
            'home_label' => 'Home',
        ], $atts, 'saf_breadcrumb' );

        $items = [];
        $items[] = [ 'label' => $atts['home_label'], 'url' => home_url( '/' ) ];

        if ( is_singular() ) {
            $pt = get_post_type();
            if ( $pt === 'post' ) {
                $cats = get_the_category();
                if ( ! empty( $cats ) ) {
                    $items[] = [ 'label' => $cats[0]->name, 'url' => get_category_link( $cats[0]->term_id ) ];
                }
            } elseif ( $pt !== 'page' ) {
                $pto = get_post_type_object( $pt );
                if ( $pto && $pto->has_archive ) {
                    $items[] = [ 'label' => $pto->labels->name, 'url' => get_post_type_archive_link( $pt ) ];
                }
            }
            $items[] = [ 'label' => get_the_title(), 'url' => '' ];
        } elseif ( is_post_type_archive() ) {
            $pto     = get_post_type_object( get_post_type() );
            $items[] = [ 'label' => $pto ? $pto->labels->name : '', 'url' => '' ];
        } elseif ( is_category() || is_tag() || is_tax() ) {
            $term = get_queried_object();
            if ( $term->parent ) {
                $parent = get_term( $term->parent, $term->taxonomy );
                if ( $parent && ! is_wp_error( $parent ) ) {
                    $items[] = [ 'label' => $parent->name, 'url' => get_term_link( $parent ) ];
                }
            }
            $items[] = [ 'label' => $term->name, 'url' => '' ];
        } elseif ( is_author() ) {
            $items[] = [ 'label' => get_the_author_meta( 'display_name', get_queried_object_id() ), 'url' => '' ];
        } elseif ( is_search() ) {
            $items[] = [ 'label' => 'Risultati per: ' . get_search_query(), 'url' => '' ];
        } elseif ( is_404() ) {
            $items[] = [ 'label' => 'Pagina non trovata', 'url' => '' ];
        }

        $ld_items = [];
        foreach ( $items as $i => $item ) {
            $ld_items[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => wp_strip_all_tags( $item['label'] ),
                'item'     => $item['url'] ?: ( is_singular() ? get_permalink() : home_url( '/' ) ),
            ];
        }
        $ld = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $ld_items,
        ];

        ob_start();
        echo '<script type="application/ld+json">' . wp_json_encode( $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
        echo '<nav class="saf-breadcrumb" aria-label="Breadcrumb">';
        echo '<ol class="saf-breadcrumb__list">';
        $last = count( $items ) - 1;
        foreach ( $items as $i => $item ) {
            $is_last = ( $i === $last );
            $class   = 'saf-breadcrumb__item' . ( $is_last ? ' saf-breadcrumb__item--current' : '' );
            echo '<li class="' . $class . '">';
            if ( ! $is_last ) {
                echo '<a href="' . esc_url( $item['url'] ) . '" class="saf-breadcrumb__link">' . esc_html( $item['label'] ) . '</a>';
                echo ' <span class="saf-breadcrumb__sep" aria-hidden="true">' . $atts['sep'] . '</span> ';
            } else {
                echo '<span aria-current="page">' . esc_html( $item['label'] ) . '</span>';
            }
            echo '</li>';
        }
        echo '</ol></nav>';
        return ob_get_clean();
    }
}
