<?php
/**
 * Amar SAF — Security & Admin Framework v2.0
 *
 * Plugin Name: Amar SAF
 * Plugin URI:  https://yaoki.academy
 * Description: Moduli funzionali per WordPress: sicurezza, admin, SEO, performance, dashboard, child theme. Di Amar Amoretti.
 * Version:     2.0.0
 * Author:      Amar Amoretti
 * Author URI:  https://yaoki.academy
 * License:     GPL v2 or later
 * Text Domain: saf
 *
 * ISTRUZIONI:
 * 1. Copia l'intera cartella /saf/ in /wp-content/plugins/saf/ e attiva il plugin.
 * 2. Oppure come MU plugin: copia in /wp-content/mu-plugins/saf/.
 *    saf-loader.php va in /wp-content/mu-plugins/saf-loader.php
 */

defined( 'ABSPATH' ) || exit;

$saf_dir = __DIR__ . '/';
if ( ! file_exists( $saf_dir . 'version.php' ) ) {
    $saf_dir = __DIR__ . '/saf/';
}

require_once $saf_dir . 'version.php';
require_once $saf_dir . 'src/Autoloader.php';

SAF\Autoloader::register();

$saf_plugin = SAF\Plugin::getInstance();
$saf_plugin->init();
