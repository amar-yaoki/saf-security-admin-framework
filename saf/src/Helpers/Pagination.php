<?php
defined( 'ABSPATH' ) || exit;
namespace SAF\Helpers;

class Pagination {
    public static function render( \WP_Query $query, array $args = [] ): string {
        $defaults = [
            'prev_text' => '&laquo; Precedente',
            'next_text' => 'Successivo &raquo;',
            'echo'      => false,
        ];
        $args = wp_parse_args( $args, $defaults );
        $big = 999999999;
        $pages = paginate_links( [
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, get_query_var( 'paged' ) ),
            'total'     => $query->max_num_pages,
            'prev_text' => $args['prev_text'],
            'next_text' => $args['next_text'],
            'type'      => 'array',
        ] );
        if ( ! is_array( $pages ) ) return '';
        $html = '<nav class="saf-pagination" role="navigation" aria-label="Paginazione">';
        $html .= '<ul class="saf-pagination__list">';
        foreach ( $pages as $page ) {
            $class = '';
            if ( strpos( $page, 'current' ) !== false ) $class = ' saf-pagination__item--active';
            if ( strpos( $page, 'prev' ) !== false ) $class = ' saf-pagination__item--prev';
            if ( strpos( $page, 'next' ) !== false ) $class = ' saf-pagination__item--next';
            $html .= '<li class="saf-pagination__item' . $class . '">' . $page . '</li>';
        }
        $html .= '</ul></nav>';
        return $html;
    }
}
