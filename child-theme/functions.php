<?php
/**
 * SAF Child Theme — funzioni.
 *
 * ATTENZIONE: Questo file NON viene sovrascritto dal plugin.
 * Se il tema child esiste già, il plugin non tocca nulla.
 */

defined( 'ABSPATH' ) || exit;

// Carica header search — funziona solo su Divi
// Puoi forzarlo da ⚙️ Dati Sito → Child Theme → Funzionalità Divi
$saf_adv   = (array) get_option( 'saf_adv_settings', array() );
$is_divi   = get_template() === 'Divi';
$force_on  = ! empty( $saf_adv['enable_divi'] );

if ( $is_divi || $force_on ) {
    require_once __DIR__ . '/inc/header-search.php';
}
