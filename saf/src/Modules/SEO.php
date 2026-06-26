<?php
namespace SAF\Modules;
defined( 'ABSPATH' ) || exit;

class SEO {
    public function init(): void {
        $settings = get_option( 'saf_adv_settings', [] );
        if ( empty( $settings['enable_seo'] ) ) return;

        add_action( 'init', [ $this, 'addExcerptSupport' ] );
        add_action( 'wp_head', [ $this, 'renderMetaDesc' ], 1 );
        add_filter( 'pre_get_document_title', [ $this, 'filterTitle' ], 20 );
    }

    public function addExcerptSupport(): void {
        $types = get_post_types( [ 'public' => true ], 'names' );
        foreach ( $types as $type ) {
            if ( ! post_type_supports( $type, 'excerpt' ) ) {
                add_post_type_support( $type, 'excerpt' );
            }
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
                if ( ! empty( $content ) ) {
                    $this->meta_desc_cache = $content;
                    return $this->meta_desc_cache;
                }
            }
        } elseif ( is_archive() || is_home() || is_front_page() ) {
            $desc = get_option( 'blogdescription' );
            if ( ! empty( $desc ) ) {
                $this->meta_desc_cache = $desc;
                return $this->meta_desc_cache;
            }
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

    public function renderMetaDesc(): void {
        $desc = $this->getMetaDesc();
        if ( ! empty( $desc ) ) {
            echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
        }
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
}
