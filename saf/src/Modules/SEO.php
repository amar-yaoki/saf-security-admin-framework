<?php
namespace SAF\Modules;
defined( 'ABSPATH' ) || exit;

class SEO {
    public function init(): void {
        add_action( 'wp_head', [ $this, 'renderCanonical' ], 1 );
        add_action( 'wp_head', [ $this, 'renderOpenGraph' ], 2 );
        add_action( 'wp_head', [ $this, 'renderJsonLdOrganization' ], 5 );
        add_action( 'wp_head', [ $this, 'renderJsonLdWebsite' ], 6 );
        add_action( 'wp_head', [ $this, 'renderRobotsMeta' ], 5 );
        add_action( 'wp_head', [ $this, 'renderMetaDesc' ], 1 );
        add_filter( 'pre_get_document_title', [ $this, 'filterTitle' ], 20 );
    }

    public function renderCanonical(): void {
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

    public function renderOpenGraph(): void {
        if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) ) return;
        if ( ! is_singular() && ! is_front_page() ) return;

        $title       = is_front_page() ? get_bloginfo( 'name' ) : get_the_title();
        $description = is_front_page()
            ? get_bloginfo( 'description' )
            : $this->truncate( get_the_excerpt(), 25, '...' );
        $url         = is_front_page() ? home_url( '/' ) : get_permalink();
        $image       = '';

        if ( is_singular() && has_post_thumbnail() ) {
            $img   = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
            $image = $img ? $img[0] : '';
        }
        if ( empty( $image ) ) {
            $org   = (array) get_option( 'saf_org_settings', [] );
            $seo   = (array) get_option( 'saf_seo_settings', [] );
            $image = $seo['og_default'] ?? $org['logo'] ?? '';
        }

        $type = is_front_page() ? 'website' : 'article';

        echo '<meta property="og:type"        content="' . esc_attr( $type ) . '" />' . "\n";
        echo '<meta property="og:title"       content="' . esc_attr( $title ) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
        echo '<meta property="og:url"         content="' . esc_url( $url ) . '" />' . "\n";
        echo '<meta property="og:site_name"   content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";
        if ( $image ) {
            echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
            echo '<meta property="og:image:width"  content="1200" />' . "\n";
            echo '<meta property="og:image:height" content="630"  />' . "\n";
            echo '<meta name="twitter:card"  content="summary_large_image" />' . "\n";
            echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
        } else {
            echo '<meta name="twitter:card"  content="summary" />' . "\n";
        }
        echo '<meta name="twitter:title"       content="' . esc_attr( $title ) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
        if ( is_singular() ) {
            echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '" />' . "\n";
            echo '<meta property="article:modified_time"  content="' . esc_attr( get_the_modified_date( 'c' ) ) . '" />' . "\n";
        }
    }

    public function renderJsonLdOrganization(): void {
        if ( ! is_front_page() ) return;
        $org = (array) get_option( 'saf_org_settings', [] );
        if ( empty( $org['name'] ) ) return;

        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $org['name'],
            'url'      => ! empty( $org['url'] ) ? $org['url'] : home_url( '/' ),
        ];
        if ( ! empty( $org['logo'] ) ) {
            $schema['logo'] = [ '@type' => 'ImageObject', 'url' => $org['logo'] ];
        }
        if ( ! empty( $org['address'] ) ) {
            $schema['address'] = [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $org['address'],
                'postalCode'      => $org['cap'] ?? '',
                'addressLocality' => $org['city'] ?? '',
                'addressCountry'  => $org['country'] ?? 'IT',
            ];
        }
        if ( ! empty( $org['email'] ) ) $schema['email'] = $org['email'];
        if ( ! empty( $org['phone'] ) ) $schema['telephone'] = $org['phone'];
        $socials = array_filter( [ $org['facebook'] ?? '', $org['instagram'] ?? '', $org['youtube'] ?? '', $org['linkedin'] ?? '', $org['twitter'] ?? '' ] );
        if ( ! empty( $socials ) ) $schema['sameAs'] = array_values( $socials );
        $this->outputJsonLd( $schema );
    }

    public function renderJsonLdWebsite(): void {
        if ( ! is_front_page() ) return;
        $schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => get_bloginfo( 'name' ),
            'url'             => home_url( '/' ),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [ '@type' => 'EntryPoint', 'urlTemplate' => home_url( '/?s={search_term_string}' ) ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
        $this->outputJsonLd( $schema );
    }

    public function renderRobotsMeta(): void {
        if ( is_search() || is_404() ) {
            echo '<meta name="robots" content="noindex,follow">' . "\n";
        }
    }

    private function outputJsonLd( array $schema ): void {
        echo '<script type="application/ld+json">'
           . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
           . '</script>' . "\n";
    }

    public function renderMetaDesc(): void {
        $desc = $this->getMetaDesc();
        if ( ! empty( $desc ) ) {
            echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
        }
    }

    private $meta_desc_cache = null;

    private function getMetaDesc(): string {
        if ( $this->meta_desc_cache !== null ) return $this->meta_desc_cache;
        if ( is_singular() ) {
            $post = get_queried_object();
            if ( $post instanceof \WP_Post ) {
                if ( ! empty( $post->post_excerpt ) ) {
                    $this->meta_desc_cache = wp_trim_words( wp_strip_all_tags( $post->post_excerpt ), 25 );
                    return $this->meta_desc_cache;
                }
                $content = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30 );
                if ( ! empty( $content ) ) { $this->meta_desc_cache = $content; return $this->meta_desc_cache; }
            }
        } elseif ( is_archive() || is_home() || is_front_page() ) {
            $desc = get_option( 'blogdescription' );
            if ( ! empty( $desc ) ) { $this->meta_desc_cache = $desc; return $this->meta_desc_cache; }
        } elseif ( is_category() || is_tag() || is_tax() ) {
            $term = get_queried_object();
            if ( $term instanceof \WP_Term && ! empty( $term->description ) ) {
                $this->meta_desc_cache = wp_trim_words( wp_strip_all_tags( $term->description ), 25 );
                return $this->meta_desc_cache;
            }
        }
        $this->meta_desc_cache = '';
        return $this->meta_desc_cache;
    }

    public function filterTitle( string $title ): string {
        if ( is_singular() ) {
            $post = get_queried_object();
            if ( $post instanceof \WP_Post && ! empty( $post->post_excerpt ) ) {
                $excerpt = wp_trim_words( wp_strip_all_tags( $post->post_excerpt ), 8 );
                return $title . ' — ' . $excerpt;
            }
        }
        return $title;
    }

    private function truncate( $text, $limit = 20, $suffix = '&hellip;' ): string {
        $text  = wp_strip_all_tags( $text );
        $words = preg_split( '/\s+/', trim( $text ) );
        if ( count( $words ) <= $limit ) return $text;
        return implode( ' ', array_slice( $words, 0, $limit ) ) . $suffix;
    }
}
