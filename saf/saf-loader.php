<?php
/**
 * saf-loader.php — MU plugin loader.
 *
 * Copia questo file in /wp-content/mu-plugins/saf-loader.php
 * per caricare SAF come MU plugin (senza attivazione manuale).
 */

defined( 'ABSPATH' ) || exit;

$mu_dir = WPMU_PLUGIN_DIR . '/saf/';

if ( file_exists( $mu_dir . 'saf.php' ) ) {
    require_once $mu_dir . 'version.php';
    require_once $mu_dir . 'src/Autoloader.php';
    SAF\Autoloader::register();
    $saf_plugin = SAF\Plugin::getInstance();
    $saf_plugin->init();
} elseif ( file_exists( WP_PLUGIN_DIR . '/saf/saf.php' ) ) {
    // Fallback: plugin standard
    require_once WP_PLUGIN_DIR . '/saf/saf.php';
}
