<?php
/**
 * helpers-compat.php
 * Funzioni helper globali per compatibilità con v1.
 * Mantenute come alias che delegano alle nuove classi OOP.
 */

defined( 'ABSPATH' ) || exit;

use SAF\I18n\Translator;

if ( ! function_exists( 'saf_t' ) ) {
    function saf_t( $key ) { return Translator::get( $key ); }
}

if ( ! function_exists( 'saf_get_credits_html' ) ) {
    function saf_get_credits_html(): string {
        $file = SAF_DIR . 'CREDITS.md';
        $author = 'Amar Amoretti';
        $site   = 'https://yaoki.academy';
        if ( file_exists( $file ) ) {
            $content = file_get_contents( $file );
            if ( preg_match( '/\*\*Autore:\*\*\s*(.+)/i', $content, $m ) ) $author = trim( $m[1] );
            if ( preg_match( '/\*\*Sito Web:\*\*\s*(.+)/i', $content, $m ) ) $site = trim( $m[1] );
        }
        return sprintf(
            '⚡ SAF v%s — Sviluppato da <a href="%s" target="_blank" rel="noopener"><strong>%s</strong></a> — GPL v2+',
            esc_html( SAF_VERSION ), esc_url( $site ), esc_html( $author )
        );
    }
}

if ( ! function_exists( 'saf_create_child_theme' ) ) {
    function saf_create_child_theme( $force = false ) {
        $child_dir = get_theme_root() . '/amar-design/';
        $source    = SAF_DIR . 'child-theme/';
        if ( ! is_dir( $source ) ) return 'error_source_missing';
        if ( ! $force && file_exists( $child_dir . 'style.css' ) ) return 'already_exists';
        $created = wp_mkdir_p( $child_dir );
        if ( ! $created && ! is_dir( $child_dir ) ) return 'error_mkdir';
        $files_to_copy = [ 'style.css', 'functions.php' ];
        foreach ( $files_to_copy as $f ) {
            $src = $source . $f; $dst = $child_dir . $f;
            if ( file_exists( $src ) ) { $ok = copy( $src, $dst ); if ( ! $ok ) return 'error_copy_' . $f; @chmod( $dst, 0644 ); }
        }
        $src_s = $source . 'screenshot.png'; $dst_s = $child_dir . 'screenshot.png';
        if ( ! file_exists( $dst_s ) && file_exists( $src_s ) ) { $ok = copy( $src_s, $dst_s ); if ( ! $ok ) return 'error_copy_screenshot.png'; @chmod( $dst_s, 0644 ); }
        $dirs = [ 'inc', 'css', 'js' ];
        foreach ( $dirs as $d ) {
            $s = $source . $d; if ( ! is_dir( $s ) ) continue;
            $d_dst = $child_dir . $d; wp_mkdir_p( $d_dst );
            $items = glob( $s . '/*' );
            if ( $items ) { foreach ( $items as $item ) { $dest = $d_dst . '/' . basename( $item ); if ( is_file( $item ) ) { $ok = copy( $item, $dest ); if ( ! $ok ) return 'error_copy_' . basename( $item ); @chmod( $dest, 0644 ); } } }
        }
        return 'success';
    }
}

if ( ! function_exists( 'saf_auto_parent_theme' ) ) {
    function saf_auto_parent_theme() {
        $theme = wp_get_theme(); $parent = $theme->get( 'Template' );
        return empty( $parent ) ? $theme->get_stylesheet() : $parent;
    }
}

if ( ! function_exists( 'saf_write_file' ) ) {
    function saf_write_file( string $path, string $content ): bool {
        $dbg = defined( 'WP_DEBUG' ) && WP_DEBUG;
        if ( $dbg ) { error_log( '[SAF saf_write_file] START | path=' . $path . ' | content_len=' . strlen( $content ) . ' | file_exists=' . var_export( file_exists( $path ), true ) ); }
        if ( file_exists( $path ) ) { $chmod_ok = @chmod( $path, 0644 ); if ( $dbg ) { clearstatcache(); error_log( '[SAF saf_write_file] chmod(0644)=' . var_export( $chmod_ok, true ) . ' | is_writable=' . var_export( is_writable( $path ), true ) . ' | perms=' . substr( sprintf( '%o', @fileperms( $path ) ), -4 ) ); } }
        global $wp_filesystem;
        if ( ! $wp_filesystem ) { require_once ABSPATH . 'wp-admin/includes/file.php'; $fs_init = WP_Filesystem(); if ( $dbg ) { error_log( '[SAF saf_write_file] WP_Filesystem() init=' . var_export( $fs_init, true ) . ' | driver=' . ( $wp_filesystem ? get_class( $wp_filesystem ) : 'NULL' ) ); } }
        if ( $wp_filesystem ) { $result = (bool) $wp_filesystem->put_contents( $path, $content, FS_CHMOD_FILE ); if ( $dbg ) { error_log( '[SAF saf_write_file] put_contents=' . var_export( $result, true ) . ' | driver=' . get_class( $wp_filesystem ) ); } return $result; }
        $bytes = file_put_contents( $path, $content ); return $bytes !== false;
    }
}

if ( ! function_exists( 'saf_sanitize_svg' ) ) {
    function saf_sanitize_svg( $content ) {
        $allowed_tags = [
            'svg' => [ 'xmlns','version','baseProfile','width','height','viewBox','preserveAspectRatio','x','y','enable-background','xml:space','xml:lang','role','aria-label','fill','stroke','stroke-width','stroke-linecap','stroke-linejoin' ],
            'g' => [ 'id','fill','stroke','stroke-width','transform','opacity' ],
            'path' => [ 'd','fill','stroke','stroke-width','transform','opacity' ],
            'circle' => [ 'cx','cy','r','fill','stroke','stroke-width' ],
            'ellipse' => [ 'cx','cy','rx','ry','fill','stroke','stroke-width' ],
            'line' => [ 'x1','y1','x2','y2','fill','stroke','stroke-width' ],
            'rect' => [ 'x','y','width','height','rx','ry','fill','stroke','stroke-width' ],
            'polygon' => [ 'points','fill','stroke','stroke-width' ],
            'polyline' => [ 'points','fill','stroke','stroke-width' ],
            'text' => [ 'x','y','font-family','font-size','fill','text-anchor' ],
            'tspan' => [ 'x','y','dx','dy','text-anchor' ],
            'defs' => [], 'clipPath' => [ 'id' ], 'mask' => [ 'id' ],
            'linearGradient' => [ 'id','x1','y1','x2','y2' ],
            'radialGradient' => [ 'id','cx','cy','r' ],
            'stop' => [ 'offset','stop-color','stop-opacity' ],
            'use' => [ 'href','xlink:href','x','y' ],
            'image' => [ 'href','xlink:href','width','height' ],
            'symbol' => [ 'id','viewBox','width','height' ],
            'filter' => [ 'id','x','y','width','height' ],
            'feDropShadow' => [ 'dx','dy','stdDeviation','flood-color','flood-opacity' ],
            'title' => [], 'desc' => [], 'style' => [ 'type' ],
        ];
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = true;
        libxml_use_internal_errors( true );
        $dom->loadXML( $content, LIBXML_NONET );
        libxml_clear_errors();
        $root = $dom->documentElement;
        if ( ! $root || $root->nodeName !== 'svg' ) return $content;
        $xpath = new \DOMXPath( $dom );
        $nodes = $xpath->query( '//*' );
        foreach ( $nodes as $node ) {
            $tag = $node->nodeName;
            if ( ! isset( $allowed_tags[ $tag ] ) ) { $node->parentNode->removeChild( $node ); continue; }
            $attrs_to_remove = [];
            foreach ( $node->attributes as $attr ) {
                $name = strtolower( $attr->nodeName );
                if ( strpos( $name, 'on' ) === 0 ) { $attrs_to_remove[] = $attr->nodeName; continue; }
                if ( in_array( $name, [ 'href', 'xlink:href' ], true ) ) {
                    $val = strtolower( trim( $attr->nodeValue ) );
                    if ( strpos( $val, 'javascript:' ) === 0 || strpos( $val, 'http://' ) === 0 || strpos( $val, 'https://' ) === 0 ) { $attrs_to_remove[] = $attr->nodeName; continue; }
                }
                if ( ! in_array( $name, $allowed_tags[ $tag ], true ) ) $attrs_to_remove[] = $attr->nodeName;
            }
            foreach ( $attrs_to_remove as $aname ) $node->removeAttribute( $aname );
        }
        return $dom->saveXML( $root );
    }
}

if ( ! function_exists( 'saf_sync_child_option' ) ) {
    add_action( 'admin_init', 'saf_sync_child_option' );
    function saf_sync_child_option() {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
        if ( ! current_user_can( 'manage_options' ) ) return;
        $child_dir = get_theme_root() . '/amar-design/';
        $exists = is_dir( $child_dir ) && file_exists( $child_dir . 'style.css' );
        $option = get_option( 'saf_child_auto_created', false );
        if ( $exists && ! $option ) update_option( 'saf_child_auto_created', true );
        elseif ( ! $exists && $option ) delete_option( 'saf_child_auto_created' );
    }
}

add_filter( 'upload_mimes', 'saf_svg_mime' );
if ( ! function_exists( 'saf_svg_mime' ) ) {
    function saf_svg_mime( $mimes ) {
        $adv = get_option( 'saf_adv_settings', [] );
        if ( ! empty( $adv['enable_svg'] ) ) { $mimes['svg'] = 'image/svg+xml'; $mimes['svgz'] = 'image/svg+xml'; }
        return $mimes;
    }
}

add_filter( 'wp_check_filetype_and_ext', 'saf_svg_check', 10, 5 );
if ( ! function_exists( 'saf_svg_check' ) ) {
    function saf_svg_check( $data, $file, $filename, $mimes, $real_mime ) {
        if ( substr( $filename, -4 ) !== '.svg' ) return $data;
        $adv = get_option( 'saf_adv_settings', [] );
        if ( empty( $adv['enable_svg'] ) ) { $data['ext'] = false; $data['type'] = false; return $data; }
        $data['ext'] = 'svg'; $data['type'] = 'image/svg+xml';
        return $data;
    }
}

add_filter( 'wp_handle_upload_prefilter', 'saf_svg_sanitize_upload' );
if ( ! function_exists( 'saf_svg_sanitize_upload' ) ) {
    function saf_svg_sanitize_upload( $file ) {
        if ( $file['type'] !== 'image/svg+xml' ) return $file;
        $content = file_get_contents( $file['tmp_name'] );
        $clean = saf_sanitize_svg( $content );
        file_put_contents( $file['tmp_name'], $clean );
        return $file;
    }
}

if ( ! function_exists( 'saf_check_child_theme' ) ) {
    add_action( 'admin_init', 'saf_check_child_theme' );
    function saf_check_child_theme() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        $child_dir = get_theme_root() . '/amar-design/';
        if ( ! is_dir( $child_dir ) ) {
            add_action( 'admin_notices', function() {
                $config_url = admin_url( 'admin.php?page=saf&tab=child' );
                $create_url = wp_nonce_url( add_query_arg( [ 'page' => 'saf', 'tab' => 'child', 'saf_auto_create' => '1' ], admin_url( 'admin.php' ) ), 'saf_auto_create', 'saf_auto_create_nonce' );
                echo '<div class="notice notice-warning is-dismissible" style="border-left-color:#e68a2e"><p><strong>🔒 Proteggi le tue personalizzazioni — crea il Child Theme</strong></p>'
                   . '<p style="margin:4px 0">Se modifichi direttamente i file del tema (Divi, Astra, Twenty Twenty-Four…), perderai tutto quando il tema si aggiorna. '
                   . 'Un <strong>child theme</strong> eredita tutto dal tema principale ma tiene le tue modifiche al sicuro.</p>'
                   . '<p><a href="' . esc_url( $create_url ) . '" class="button button-primary">➕ Crea child theme ora</a> &nbsp; '
                   . '<a href="' . esc_url( $config_url ) . '" class="button button-secondary">⚙️ Configura parametri</a></p></div>';
            } );
        }
    }
}
