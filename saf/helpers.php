<?php
/**
 * inc/helpers.php
 * Funzioni condivise e shortcode di utilità generale.
 *
 * Sezione 31 — YouTube: estrazione ID e thumbnail
 * Sezione 32 — Truncate testo
 * Sezione 33 — Formattazione data in italiano
 * Sezione 34 — Social sharing [condividi_social] — pulsanti configurabili da ⚙️ Dati Sito → Shortcode
 * Sezione 35 — Paginazione standard
 * Sezione 36 — Paginazione Netflix (carica altri via AJAX)
 * Sezione 37 — Reading time automatico [saf_reading_time]
 * Sezione 38 — Footer info aziendale [saf_footer_info]
 */

defined( 'ABSPATH' ) || exit;


/* ============================================================
   SEZIONE 31 — YOUTUBE: ESTRAZIONE ID E THUMBNAIL
   ============================================================ */

/**
 * Estrae l'ID video da qualsiasi formato URL YouTube.
 *
 * Supporta:
 *   https://www.youtube.com/watch?v=ID
 *   https://youtu.be/ID
 *   https://www.youtube.com/embed/ID
 *   https://www.youtube.com/shorts/ID
 *
 * @param  string $url
 * @return string|false ID (11 caratteri) o false
 */
function saf_youtube_id( $url ) {
    if ( empty( $url ) ) return false;
    $pattern = '/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/|live\/)|youtu\.be\/)([a-zA-Z0-9_\-]{11})/';
    preg_match( $pattern, $url, $matches );
    return ! empty( $matches[1] ) ? $matches[1] : false;
}

/**
 * URL thumbnail YouTube.
 *
 * @param  string $url     URL video YouTube
 * @param  string $quality maxresdefault | hqdefault | mqdefault | sddefault
 * @return string|false
 */
function saf_youtube_thumbnail( $url, $quality = 'hqdefault' ) {
    $id = saf_youtube_id( $url );
    if ( ! $id ) return false;
    $allowed = array( 'maxresdefault', 'hqdefault', 'mqdefault', 'sddefault', 'default' );
    if ( ! in_array( $quality, $allowed, true ) ) $quality = 'hqdefault';
    return 'https://img.youtube.com/vi/' . $id . '/' . $quality . '.jpg';
}


/* ============================================================
   SEZIONE 32 — TRUNCATE TESTO
   ============================================================ */

/**
 * Tronca un testo a N parole con suffisso.
 *
 * @param  string $text
 * @param  int    $limit  Numero parole (default: 20)
 * @param  string $suffix Suffisso (default: …)
 * @return string
 */
function saf_truncate( $text, $limit = 20, $suffix = '&hellip;' ) {
    $text  = wp_strip_all_tags( $text );
    $words = preg_split( '/\s+/', trim( $text ) );
    if ( count( $words ) <= $limit ) return $text;
    return implode( ' ', array_slice( $words, 0, $limit ) ) . $suffix;
}


/* ============================================================
   SEZIONE 33 — FORMATTAZIONE DATA IN ITALIANO
   ============================================================ */

/**
 * Formatta una data in italiano.
 *
 * @param  string|int $date_raw  Data in formato Y-m-d, d/m/Y o timestamp Unix
 * @param  bool       $long      true → "3 Luglio 2026" | false → "3 lug 2026"
 * @param  bool       $with_year true → include anno | false → omette anno
 * @return string
 */
function saf_format_date( $date_raw, $long = true, $with_year = true ) {
    if ( empty( $date_raw ) ) return '';

    $mesi_lunghi = array( 1=>'Gennaio',2=>'Febbraio',3=>'Marzo',4=>'Aprile',
        5=>'Maggio',6=>'Giugno',7=>'Luglio',8=>'Agosto',
        9=>'Settembre',10=>'Ottobre',11=>'Novembre',12=>'Dicembre' );
    $mesi_corti  = array( 1=>'gen',2=>'feb',3=>'mar',4=>'apr',
        5=>'mag',6=>'giu',7=>'lug',8=>'ago',
        9=>'set',10=>'ott',11=>'nov',12=>'dic' );

    $ts     = is_numeric( $date_raw ) ? (int)$date_raw : strtotime( $date_raw );
    $giorno = (int) date( 'j', $ts );
    $mese   = (int) date( 'n', $ts );
    $anno   = date( 'Y', $ts );

    $nome = $long ? $mesi_lunghi[ $mese ] : $mesi_corti[ $mese ];
    return $giorno . ' ' . $nome . ( $with_year ? ' ' . $anno : '' );
}


/* ============================================================
   SEZIONE 34 — SOCIAL SHARING [condividi_social]
   I pulsanti attivi si configurano in ⚙️ Dati Sito → Shortcode.
   Instagram e TikTok copiano il link (nessun URL di condivisione nativo).
   ============================================================ */

add_shortcode( 'condividi_social', 'saf_sc_condividi_social' );
function saf_sc_condividi_social( $atts ) {
    global $post;
    if ( ! $post ) return '';

    $url   = get_permalink( $post );
    $title = get_the_title( $post );
    $enc_u = rawurlencode( $url );
    $enc_t = rawurlencode( $title );

    // Legge le spunte da ⚙️ Dati Sito → Shortcode (default: tutti attivi)
    $sc_opts = (array) get_option( 'saf_sc_settings', array() );
    $enabled = $sc_opts['social_share'] ?? array();
    $all_on  = empty( $enabled ); // se non ancora configurato, mostra tutti

    // Definizione piattaforme nell'ordine corretto
    $platforms = array(
        'facebook' => array(
            'label'  => 'Facebook',
            'href'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $enc_u,
            'target' => '_blank',
            'color'  => '#1877F2',
            'copy'   => false,
            'icon'   => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
        ),
        'whatsapp' => array(
            'label'  => 'WhatsApp',
            'href'   => 'https://wa.me/?text=' . $enc_t . '%20' . $enc_u,
            'target' => '_blank',
            'color'  => '#25D366',
            'copy'   => false,
            'icon'   => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>',
        ),
        'telegram' => array(
            'label'  => 'Telegram',
            'href'   => 'https://t.me/share/url?url=' . $enc_u . '&text=' . $enc_t,
            'target' => '_blank',
            'color'  => '#0088cc',
            'copy'   => false,
            'icon'   => '<path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>',
        ),
        'instagram' => array(
            'label'  => 'Instagram',
            'href'   => '#',
            'target' => '_self',
            'color'  => '#C13584',
            'copy'   => true,
            'icon'   => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>',
        ),
        'tiktok' => array(
            'label'  => 'TikTok',
            'href'   => '#',
            'target' => '_self',
            'color'  => '#010101',
            'copy'   => true,
            'icon'   => '<path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>',
        ),
        'email' => array(
            'label'  => 'Email',
            'href'   => 'mailto:?subject=' . $enc_t . '&body=' . $enc_u,
            'target' => '_self',
            'color'  => '#f47D39',
            'copy'   => false,
            'icon'   => '<path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>',
        ),
        'copy' => array(
            'label'  => 'Copia link',
            'href'   => '#',
            'target' => '_self',
            'color'  => '#6c757d',
            'copy'   => true,
            'icon'   => '<path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>',
        ),
    );

    ob_start();
    ?>
    <div class="saf-social-share">
      <span class="saf-social-share__label">Condividi:</span>
      <div class="saf-social-share__btns">
        <?php foreach ( $platforms as $key => $p ) :
            if ( ! $all_on && ! in_array( $key, $enabled, true ) ) continue;
            $is_copy = $p['copy'];
        ?>
          <a
            href="<?php echo esc_url( $p['href'] ); ?>"
            class="saf-share-btn saf-share-btn--<?php echo esc_attr( $key ); ?><?php echo $is_copy ? ' saf-copy-link' : ''; ?>"
            style="--share-color:<?php echo esc_attr( $p['color'] ); ?>;"
            target="<?php echo esc_attr( $p['target'] ); ?>"
            rel="noopener noreferrer"
            title="<?php echo $is_copy ? 'Copia link per ' . esc_attr( $p['label'] ) : 'Condividi su ' . esc_attr( $p['label'] ); ?>"
            <?php if ( $is_copy ) : ?>data-url="<?php echo esc_url( $url ); ?>"<?php endif; ?>
            aria-label="<?php echo esc_attr( $p['label'] ); ?>"
          >
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"><?php echo $p['icon']; ?></svg>
            <span><?php echo esc_html( $p['label'] ); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="saf-share-toast" id="saf-share-toast">&#10003; Link copiato!</div>
    </div>
    <?php
    return ob_get_clean();
}

// JS copia link + toast — no dipendenze esterne
add_action( 'wp_footer', 'saf_social_share_js' );
function saf_social_share_js() {
    if ( ! is_singular() ) return;
    ?>
    <script>
    (function(){
        document.querySelectorAll('.saf-copy-link').forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.preventDefault();
                var url   = this.getAttribute('data-url');
                var toast = document.getElementById('saf-share-toast');
                function showToast(){
                    toast.classList.add('saf-share-toast--show');
                    setTimeout(function(){ toast.classList.remove('saf-share-toast--show'); }, 2500);
                }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(showToast);
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = url; document.body.appendChild(ta);
                    ta.select(); document.execCommand('copy');
                    document.body.removeChild(ta);
                    showToast();
                }
            });
        });
    })();
    </script>
    <?php
}


/* ============================================================
   SEZIONE 35 — PAGINAZIONE STANDARD
   ============================================================ */

/**
 * Renderizza paginazione WP numerata.
 *
 * @param WP_Query|null $query  Query (null = globale)
 * @param bool          $echo   true = stampa, false = return string
 * @return string|void
 */
function saf_paginazione( $query = null, $echo = true ) {
    global $wp_query;
    if ( ! $query ) $query = $wp_query;

    $total   = (int) $query->max_num_pages;
    $current = max( 1, (int) get_query_var( 'paged' ) );

    if ( $total <= 1 ) return $echo ? null : '';

    $links = paginate_links( array(
        'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
        'format'    => '?paged=%#%',
        'current'   => $current,
        'total'     => $total,
        'prev_text' => '&laquo; Precedente',
        'next_text' => 'Successivo &raquo;',
        'type'      => 'list',
    ) );

    $output = '<nav class="saf-pagination" aria-label="Paginazione">' . $links . '</nav>';
    if ( $echo ) { echo $output; } else { return $output; }
}


/* ============================================================
   SEZIONE 36 — PAGINAZIONE NETFLIX (CARICA ALTRI)
   ============================================================ */

/**
 * Renderizza il bottone "Carica altri" per paginazione infinita.
 * Il relativo handler AJAX va registrato nel file del CPT/archivio.
 *
 * @param int    $total_pages
 * @param int    $current_page
 * @param string $ajax_action  Azione wp_ajax da richiamare
 * @param array  $extra_data   Dati aggiuntivi passati al JS
 */
function saf_paginazione_netflix( $total_pages, $current_page, $ajax_action = 'saf_load_more', $extra_data = array() ) {
    if ( $current_page >= $total_pages ) return;

    $data = array_merge( array(
        'action'       => sanitize_key( $ajax_action ),
        'current_page' => (int) $current_page,
        'total_pages'  => (int) $total_pages,
    ), $extra_data );

    echo '<div class="saf-load-more-wrap">';
    printf(
        '<button class="saf-load-more" data-params="%s">Carica altri</button>',
        esc_attr( wp_json_encode( $data ) )
    );
    echo '</div>';
}


/* ============================================================
   SEZIONE 37 — READING TIME AUTOMATICO [saf_reading_time]
   ============================================================ */

add_shortcode( 'saf_reading_time', 'saf_sc_reading_time' );
function saf_sc_reading_time( $atts ) {
    global $post;
    if ( ! $post ) return '';

    $atts = shortcode_atts( array(
        'wpm'    => 200,   // Parole per minuto
        'label'  => 'Lettura: ',
        'suffix' => ' min',
    ), $atts, 'saf_reading_time' );

    $content  = get_the_content( null, false, $post );
    $text     = wp_strip_all_tags( $content );
    preg_match_all( '/\p{L}+/u', $text, $matches );
    $words = count( $matches[0] );
    $minutes  = max( 1, (int) ceil( $words / (int) $atts['wpm'] ) );

    return '<span class="saf-reading-time">'
         . esc_html( $atts['label'] )
         . '<strong>' . $minutes . esc_html( $atts['suffix'] ) . '</strong>'
         . '</span>';
}


/* ============================================================
   SEZIONE 38 — FOOTER INFO AZIENDALE [saf_footer_info]
   Legge i dati da ⚙️ Dati Sito — richiede admin.php
   ============================================================ */

add_shortcode( 'saf_footer_info', 'saf_sc_footer_info' );
function saf_sc_footer_info( $atts ) {
    if ( ! function_exists( 'saf_get_org_data' ) ) return '';

    $atts = shortcode_atts( array(
        'mostra' => 'nome,piva,email', // nome | piva | email | phone | address
    ), $atts, 'saf_footer_info' );

    $org    = saf_get_org_data();
    $mostra = array_map( 'trim', explode( ',', $atts['mostra'] ) );
    $parts  = array();

    if ( in_array( 'nome', $mostra ) && ! empty( $org['name'] ) ) {
        $parts[] = '<strong>' . esc_html( $org['name'] ) . '</strong>';
    }
    if ( in_array( 'piva', $mostra ) && ! empty( $org['piva'] ) ) {
        $parts[] = 'P.IVA ' . esc_html( $org['piva'] );
    }
    if ( in_array( 'address', $mostra ) && ! empty( $org['address'] ) ) {
        $addr = esc_html( $org['address'] );
        if ( $org['cap'] || $org['city'] ) {
            $addr .= ', ' . esc_html( trim( $org['cap'] . ' ' . $org['city'] ) );
        }
        $parts[] = $addr;
    }
    if ( in_array( 'email', $mostra ) && ! empty( $org['email'] ) ) {
        $parts[] = '<a href="mailto:' . esc_attr( $org['email'] ) . '">' . esc_html( $org['email'] ) . '</a>';
    }
    if ( in_array( 'phone', $mostra ) && ! empty( $org['phone'] ) ) {
        $parts[] = '<a href="tel:' . esc_attr( preg_replace( '/\s+/', '', $org['phone'] ) ) . '">' . esc_html( $org['phone'] ) . '</a>';
    }

    if ( empty( $parts ) ) return '';
    return '<span class="saf-footer-info">' . implode( ' &mdash; ', $parts ) . '</span>';
}


/* ============================================================
   SEZIONE 39 — NAP FOOTER HTML [saf_nap_html]
   Restituisce l'HTML personalizzato scritto in
   ⚙️ Dati Sito → Tab NAP Footer.
   Uso: [saf_nap_html] nel footer di Divi o in qualsiasi template.
   ============================================================ */

add_shortcode( 'saf_nap_html', 'saf_sc_nap_html' );
function saf_sc_nap_html( $atts ) {
    $html = get_option( 'saf_nap_html', '' );
    if ( empty( trim( $html ) ) ) return '';

    // Wrapping class opzionale via parametro shortcode
    $atts = shortcode_atts( array( 'class' => '' ), $atts, 'saf_nap_html' );
    $safe_html = wp_kses_post( $html );
    if ( ! empty( $atts['class'] ) ) {
        return '<div class="' . esc_attr( $atts['class'] ) . '">' . $safe_html . '</div>';
    }
    return $safe_html;
}
