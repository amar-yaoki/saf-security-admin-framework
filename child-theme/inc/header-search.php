<?php
/**
 * Overlay ricerca fullscreen + icona lente nell'header Divi.
 * Viene caricato SOLO se get_template() === 'Divi'.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_ajax_saf_header_search',        'saf_header_search_handler' );
add_action( 'wp_ajax_nopriv_saf_header_search', 'saf_header_search_handler' );

function saf_header_search_handler() {
    if ( ! check_ajax_referer( 'saf_search_nonce', 'nonce', false ) ) {
        wp_send_json_error( null, 403 );
    }

    $q = sanitize_text_field( wp_unslash( $_GET['q'] ?? '' ) );
    if ( mb_strlen( $q ) < 2 ) {
        wp_send_json_success( [ 'items' => [] ] );
    }

    $pt_param = sanitize_key( $_GET['pt'] ?? '' );
    $allowed  = [ 'post', 'page' ];

    if ( $pt_param && in_array( $pt_param, $allowed, true ) ) {
        $post_types = [ $pt_param ];
    } else {
        $post_types = $allowed;
    }

    add_filter( 'posts_search', 'saf_title_only_search', 10, 2 );

    $query = new WP_Query( [
        'post_type'      => $post_types,
        'post_status'    => 'publish',
        'posts_per_page' => 8,
        'no_found_rows'  => true,
    ] );

    remove_filter( 'posts_search', 'saf_title_only_search' );

    $items = [];
    while ( $query->have_posts() ) {
        $query->the_post();
        $items[] = [
            'title'   => get_the_title(),
            'url'     => get_permalink(),
            'date'    => get_the_date( 'j M Y' ),
            'type'    => get_post_type(),
        ];
    }
    wp_reset_postdata();

    wp_send_json_success( [ 'items' => $items ] );
}

function saf_title_only_search( $search, $wp_query ) {
    global $wpdb;
    if ( empty( $search ) ) return $search;
    $q = $wp_query->query_vars['s'] ?? '';
    if ( ! $q ) return $search;
    $n = ! empty( $wp_query->query_vars['exact'] ) ? '' : '%';
    $q = $wpdb->esc_like( $q );
    $search = $wpdb->prepare( "AND {$wpdb->posts}.post_title LIKE %s", $n . $q . $n );
    return $search;
}

add_action( 'wp_footer', 'saf_header_search_html' );
function saf_header_search_html() {
    ?>
    <div id="saf-search-overlay" class="saf-search-overlay">
        <div class="saf-search-overlay__inner">
            <button class="saf-search-overlay__close" aria-label="Chiudi ricerca">&times;</button>
            <form class="saf-search-overlay__form" role="search" autocomplete="off">
                <input type="text" class="saf-search-overlay__input"
                       placeholder="Cerca nel sito..." aria-label="Cerca">
            </form>
            <div class="saf-search-overlay__results"></div>
        </div>
    </div>

    <script>
    (function() {
        var injectLente = function() {
            var nav = document.getElementById('et_top_search');
            var mobileNav = document.getElementById('et_mobile_nav_menu');
            if (!nav && mobileNav) {
                var a = document.createElement('a');
                a.className = 'saf-header-search-icon';
                a.href = '#';
                a.setAttribute('aria-label', 'Cerca');
                a.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>';
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (window.safOpenSearch) window.safOpenSearch();
                });
                if (mobileNav.firstChild) {
                    mobileNav.insertBefore(a, mobileNav.firstChild);
                } else {
                    mobileNav.appendChild(a);
                }
            }
        };

        var overlay = document.getElementById('saf-search-overlay');
        var input = overlay.querySelector('.saf-search-overlay__input');
        var results = overlay.querySelector('.saf-search-overlay__results');
        var closeBtn = overlay.querySelector('.saf-search-overlay__close');
        var form = overlay.querySelector('.saf-search-overlay__form');
        var timer = null;

        window.safOpenSearch = function() {
            overlay.classList.add('saf-search-overlay--open');
            input.focus();
            document.body.style.overflow = 'hidden';
        };

        var closeSearch = function() {
            overlay.classList.remove('saf-search-overlay--open');
            document.body.style.overflow = '';
            results.innerHTML = '';
            input.value = '';
        };

        closeBtn.addEventListener('click', closeSearch);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeSearch();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.classList.contains('saf-search-overlay--open')) closeSearch();
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); safOpenSearch(); }
        });

        form.addEventListener('submit', function(e) { e.preventDefault(); });

        input.addEventListener('input', function() {
            clearTimeout(timer);
            var q = this.value.trim();
            if (q.length < 2) { results.innerHTML = ''; return; }
            timer = setTimeout(function() {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '<?php echo admin_url( 'admin-ajax.php' ); ?>?action=saf_header_search&q=' + encodeURIComponent(q) + '&nonce=<?php echo wp_create_nonce( 'saf_search_nonce' ); ?>');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var data = JSON.parse(xhr.responseText);
                        if (data.success && data.data.items.length) {
                            var html = '<ul class="saf-search-results-list">';
                            data.data.items.forEach(function(item) {
                                html += '<li><a href="' + item.url + '"><strong>' + item.title + '</strong><span class="saf-search-result-meta">' + item.type + ' · ' + item.date + '</span></a></li>';
                            });
                            html += '</ul>';
                            results.innerHTML = html;
                        } else {
                            results.innerHTML = '<p class="saf-search-no-results">Nessun risultato.</p>';
                        }
                    }
                };
                xhr.send();
            }, 300);
        });

        injectLente();
    })();
    </script>

    <style>
    .saf-search-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,.92); z-index: 999999;
        display: none; align-items: center; justify-content: center;
    }
    .saf-search-overlay--open { display: flex; }
    .saf-search-overlay__inner { width: 90%; max-width: 600px; position: relative; }
    .saf-search-overlay__close {
        position: absolute; top: -50px; right: 0;
        background: none; border: none; color: #fff; font-size: 36px;
        cursor: pointer; line-height: 1; padding: 4px 12px; opacity: .7;
    }
    .saf-search-overlay__close:hover { opacity: 1; }
    .saf-search-overlay__input {
        width: 100%; padding: 18px 24px; font-size: 22px; border: none;
        border-radius: 8px; background: #1a1a1a; color: #fff;
        outline: 2px solid transparent; transition: outline .2s;
    }
    .saf-search-overlay__input:focus { outline-color: #2ea3f2; }
    .saf-search-overlay__results { margin-top: 16px; }
    .saf-search-results-list { list-style: none; margin: 0; padding: 0; }
    .saf-search-results-list li { border-bottom: 1px solid #333; }
    .saf-search-results-list a {
        display: block; padding: 12px 16px; color: #ddd; text-decoration: none;
        transition: background .15s; border-radius: 4px;
    }
    .saf-search-results-list a:hover { background: rgba(255,255,255,.06); }
    .saf-search-result-meta { display: block; font-size: 12px; color: #888; margin-top: 2px; }
    .saf-search-no-results { color: #888; padding: 20px; text-align: center; }
    .saf-header-search-icon {
        display: inline-flex; align-items: center; padding: 6px 10px;
        color: inherit; text-decoration: none !important;
        transition: opacity .15s;
    }
    .saf-header-search-icon:hover { opacity: .7; }
    </style>
    <?php
}
