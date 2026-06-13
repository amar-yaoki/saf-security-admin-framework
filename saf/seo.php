<?php
/**
 * inc/seo.php
 * SEO base — SAF � Security & Admin Framework.
 *
 * Sezione 40 — JSON-LD Organization (homepage, da ⚙️ Dati Sito)
 * Sezione 41 — JSON-LD WebSite + SearchAction (homepage)
 * Sezione 42 — Canonical URL automatico (fallback se no Rank Math/Yoast)
 * Sezione 43 — Open Graph + Twitter Card + Article time (fallback se no Rank Math/Yoast)
 * Sezione 44 — Shortcode [saf_breadcrumb] con BreadcrumbList JSON-LD
 * Sezione 45 — Robots meta tag (noindex search/404)
 * Sezione 46 — Title tag ottimizzato (fallback)
 *
 * NOTA: Rank Math o Yoast gestiscono meta/OG/title su blog e pagine normali.
 * Questo file aggiunge solo JSON-LD Organization/WebSite in homepage
 * e il breadcrumb universale. I fallback OG/canonical si attivano
 * automaticamente solo se i plugin SEO non sono installati.
 */

defined( 'ABSPATH' ) || exit;


/* ============================================================
   SEZIONE 40 — JSON-LD ORGANIZATION
   ============================================================ */

add_action( 'wp_head', 'saf_json_ld_organization', 5 );
function saf_json_ld_organization() {
    if ( ! is_front_page() ) return;

    $org = saf_get_org_data();
    if ( empty( $org['name'] ) ) return;

    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => $org['name'],
        'url'      => ! empty( $org['url'] ) ? $org['url'] : home_url( '/' ),
    );

    if ( ! empty( $org['logo'] ) ) {
        $schema['logo'] = array(
            '@type' => 'ImageObject',
            'url'   => $org['logo'],
        );
    }

    if ( ! empty( $org['address'] ) ) {
        $schema['address'] = array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => $org['address'],
            'postalCode'      => $org['cap'],
            'addressLocality' => $org['city'],
            'addressCountry'  => $org['country'] ?? 'IT',
        );
    }

    if ( ! empty( $org['email'] ) ) {
        $schema['email'] = $org['email'];
    }
    if ( ! empty( $org['phone'] ) ) {
        $schema['telephone'] = $org['phone'];
    }

    $socials = array_filter( array(
        $org['facebook'], $org['instagram'], $org['youtube'],
        $org['linkedin'], $org['twitter'],
    ) );
    if ( ! empty( $socials ) ) {
        $schema['sameAs'] = array_values( $socials );
    }

    saf_output_json_ld( $schema );
}


/* ============================================================
   SEZIONE 41 — JSON-LD WEBSITE + SEARCHACTION
   ============================================================ */

add_action( 'wp_head', 'saf_json_ld_website', 6 );
function saf_json_ld_website() {
    if ( ! is_front_page() ) return;

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        'name'            => get_bloginfo( 'name' ),
        'url'             => home_url( '/' ),
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => array(
                '@type'       => 'EntryPoint',
                'urlTemplate' => home_url( '/?s={search_term_string}' ),
            ),
            'query-input' => 'required name=search_term_string',
        ),
    );

    saf_output_json_ld( $schema );
}


/* ============================================================
   SEZIONE 42 — CANONICAL URL AUTOMATICO (FALLBACK)
   ============================================================ */

add_action( 'wp_head', 'saf_canonical_fallback', 1 );
function saf_canonical_fallback() {
    if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) ) return;

    $canonical = '';

    if ( is_singular() ) {
        $canonical = get_permalink();
    } elseif ( is_front_page() ) {
        $canonical = home_url( '/' );
    } elseif ( is_home() ) {
        $page      = get_option( 'page_for_posts' );
        $canonical = $page ? get_permalink( $page ) : home_url( '/' );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $canonical = get_term_link( get_queried_object() );
    } elseif ( is_author() ) {
        $canonical = get_author_posts_url( get_queried_object_id() );
    } elseif ( is_post_type_archive() ) {
        $canonical = get_post_type_archive_link( get_post_type() );
    }

    if ( empty( $canonical ) || is_wp_error( $canonical ) ) return;

    $paged = (int) get_query_var( 'paged' );
    if ( $paged > 1 ) {
        $canonical = trailingslashit( $canonical ) . 'page/' . $paged . '/';
    }

    echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
}


/* ============================================================
   SEZIONE 43 — OPEN GRAPH BASE (FALLBACK)
   ============================================================ */

add_action( 'wp_head', 'saf_og_fallback', 2 );
function saf_og_fallback() {
    if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) ) return;
    if ( ! is_singular() && ! is_front_page() ) return;

    $title       = is_front_page() ? get_bloginfo( 'name' ) : get_the_title();
    $description = is_front_page()
        ? get_bloginfo( 'description' )
        : saf_truncate( get_the_excerpt(), 25, '...' );
    $url         = is_front_page() ? home_url( '/' ) : get_permalink();

    // Immagine: featured image del post → fallback OG default da Dati Sito
    $image = '';
    if ( is_singular() && has_post_thumbnail() ) {
        $img   = wp_get_attachment_image_src( get_post_thumbnail_id(), 'saf-og' );
        $image = $img ? $img[0] : '';
    }
    if ( empty( $image ) && function_exists( 'saf_get_org_data' ) ) {
        $org   = saf_get_org_data();
        $image = $org['og_default'] ?? '';
    }

    $type = is_front_page() ? 'website' : 'article';

    echo '<meta property="og:type"        content="' . esc_attr( $type )        . '" />' . "\n";
    echo '<meta property="og:title"       content="' . esc_attr( $title )       . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
    echo '<meta property="og:url"         content="' . esc_url( $url )          . '" />' . "\n";
    echo '<meta property="og:site_name"   content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";

    if ( $image ) {
        echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
        echo '<meta property="og:image:width"  content="1200" />' . "\n";
        echo '<meta property="og:image:height" content="630"  />' . "\n";
        echo '<meta name="twitter:card"        content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:image"       content="' . esc_url( $image ) . '" />' . "\n";
    } else {
        echo '<meta name="twitter:card"        content="summary" />' . "\n";
    }
    echo '<meta name="twitter:title"       content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
    if ( is_singular() ) {
        echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '" />' . "\n";
        echo '<meta property="article:modified_time"  content="' . esc_attr( get_the_modified_date( 'c' ) ) . '" />' . "\n";
    }
}


/* ============================================================
   SEZIONE 44 — [saf_breadcrumb]
   Breadcrumb semantico con BreadcrumbList JSON-LD.
   Funziona su qualsiasi tipo di pagina WP.
   ============================================================ */

add_shortcode( 'saf_breadcrumb', 'saf_sc_breadcrumb' );
function saf_sc_breadcrumb( $atts ) {
    $atts = shortcode_atts( array(
        'sep'        => '&rsaquo;',
        'home_label' => 'Home',
    ), $atts, 'saf_breadcrumb' );

    $items = array();
    $items[] = array( 'label' => $atts['home_label'], 'url' => home_url( '/' ) );

    if ( is_singular() ) {
        $pt = get_post_type();
        if ( $pt === 'post' ) {
            $cats = get_the_category();
            if ( ! empty( $cats ) ) {
                $items[] = array( 'label' => $cats[0]->name, 'url' => get_category_link( $cats[0]->term_id ) );
            }
        } elseif ( $pt !== 'page' ) {
            $pto = get_post_type_object( $pt );
            if ( $pto && $pto->has_archive ) {
                $items[] = array( 'label' => $pto->labels->name, 'url' => get_post_type_archive_link( $pt ) );
            }
        }
        $items[] = array( 'label' => get_the_title(), 'url' => '' );

    } elseif ( is_post_type_archive() ) {
        $pto     = get_post_type_object( get_post_type() );
        $items[] = array( 'label' => $pto ? $pto->labels->name : '', 'url' => '' );

    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( $term->parent ) {
            $parent = get_term( $term->parent, $term->taxonomy );
            if ( $parent && ! is_wp_error( $parent ) ) {
                $items[] = array( 'label' => $parent->name, 'url' => get_term_link( $parent ) );
            }
        }
        $items[] = array( 'label' => $term->name, 'url' => '' );

    } elseif ( is_author() ) {
        $items[] = array( 'label' => get_the_author_meta( 'display_name', get_queried_object_id() ), 'url' => '' );

    } elseif ( is_search() ) {
        $items[] = array( 'label' => 'Risultati per: ' . get_search_query(), 'url' => '' );

    } elseif ( is_404() ) {
        $items[] = array( 'label' => 'Pagina non trovata', 'url' => '' );
    }

    // JSON-LD BreadcrumbList
    $ld_items = array();
    foreach ( $items as $i => $item ) {
        $ld_items[] = array(
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => wp_strip_all_tags( $item['label'] ),
            'item'     => $item['url'] ?: ( is_singular() ? get_permalink() : home_url( '/' ) ),
        );
    }
    $ld = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $ld_items,
    );

    ob_start();
    echo '<script type="application/ld+json">' . wp_json_encode( $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
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


/* ============================================================
   SEZIONE 45 — ROBOTS META TAG
   noindex per pagine non indicizzabili (search, 404, login)
   ============================================================ */

add_action( 'wp_head', 'saf_robots_meta', 5 );
function saf_robots_meta() {
    if ( is_search() || is_404() ) {
        echo '<meta name="robots" content="noindex,follow">' . "\n";
    }
}


/* ============================================================
   SEZIONE 46 — HELPER OUTPUT JSON-LD
   ============================================================ */

/**
 * Stampa un blocco JSON-LD nel head.
 *
 * @param array $schema Schema.org array
 */
function saf_output_json_ld( array $schema ) {
    echo '<script type="application/ld+json">'
       . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
       . '</script>' . "\n";
}
