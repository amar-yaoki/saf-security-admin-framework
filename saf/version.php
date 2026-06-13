<?php
/**
 * SAF — version.php
 *
 * Costanti globali: versione, path, URL, crediti.
 * Caricato per primo da saf-loader.php.
 *
 * CREDITS — Modifica CREDITS.md per personalizzare autore e sito.
 */

defined( 'ABSPATH' ) || exit;

define( 'SAF_VERSION', '1.2.1' );
define( 'SAF_DIR',     __DIR__ . '/' );
define( 'SAF_URL',     plugin_dir_url( SAF_DIR . 'saf-loader.php' ) );

/**
 * Traduzione semplice SAF — restituisce stringa nella lingua corrente.
 * Se la chiave non esiste per la lingua corrente, usa italiano (default).
 *
 * @param string $key Chiave della stringa.
 * @return string Testo tradotto.
 */
function saf_t( $key ) {
    static $strings = null;
    static $loaded  = false;

    if ( ! $loaded ) {
        $locale = get_locale(); // es. 'it_IT', 'en_US', 'en_GB'

        // Carica prima italiano come fallback
        $strings = require SAF_DIR . 'languages/it_IT.php';

        // Se la lingua non è italiano, sovrascrivi con la lingua corrente
        if ( strpos( $locale, 'it_' ) !== 0 ) {
            $lang_file = SAF_DIR . 'languages/' . $locale . '.php';
            if ( file_exists( $lang_file ) ) {
                $lang_strings = require $lang_file;
                $strings = array_merge( $strings, $lang_strings );
            }
        }

        $loaded = true;
    }

    return $strings[ $key ] ?? $key;
}

/**
 * Restituisce l'HTML del credit "Sviluppato da".
 * Legge i dati da CREDITS.md — se il file non esiste, fallback hardcoded.
 */
function saf_get_credits_html(): string {
    $file = SAF_DIR . 'CREDITS.md';
    $author = 'Amar Amoretti';
    $site   = 'https://yaoki.academy';

    if ( file_exists( $file ) ) {
        $content = file_get_contents( $file );
        if ( preg_match( '/\*\*Autore:\*\*\s*(.+)/i', $content, $m ) ) {
            $author = trim( $m[1] );
        }
        if ( preg_match( '/\*\*Sito Web:\*\*\s*(.+)/i', $content, $m ) ) {
            $site = trim( $m[1] );
        }
    }

    return sprintf(
        '⚡ SAF v%s — Sviluppato da <a href="%s" target="_blank" rel="noopener"><strong>%s</strong></a> — GPL v2+',
        esc_html( SAF_VERSION ),
        esc_url( $site ),
        esc_html( $author )
    );
}
