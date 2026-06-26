<?php
defined( 'ABSPATH' ) || exit;
?>
<h2>Diagnostica Sistema</h2>
<table class="widefat striped" style="max-width:700px;">
<thead><tr><th style="width:250px">Parametro</th><th>Valore</th></tr></thead>
<tbody>
<tr><td colspan="2" style="background:#f0f0f1;font-weight:bold">WordPress</td></tr>
<tr><td>Versione WP</td><td><?php echo esc_html( get_bloginfo('version') ); ?></td></tr>
<tr><td>PHP</td><td><?php echo esc_html( phpversion() ); ?></td></tr>
<tr><td>Memoria limite</td><td><?php echo esc_html( WP_MEMORY_LIMIT ); ?></td></tr>
<tr><td>Tema attivo</td><td><?php echo esc_html( wp_get_theme()->get('Name') . ' v' . wp_get_theme()->get('Version') ); ?></td></tr>
<tr><td>SAF</td><td><?php echo esc_html( defined('SAF_VERSION') ? SAF_VERSION : '—' ); ?></td></tr>

<tr><td colspan="2" style="background:#f0f0f1;font-weight:bold">wp-config.php</td></tr>
<tr><td>WP_DEBUG</td><td><?php echo ( defined('WP_DEBUG') && WP_DEBUG ) ? '✅ true' : '❌ false'; ?></td></tr>
<tr><td>WP_DEBUG_LOG</td><td><?php echo ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) ? '✅ true' : '❌ false'; ?></td></tr>
<tr><td>WP_DEBUG_DISPLAY</td><td><?php echo ( defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ) ? '⚠️ true' : '✅ false'; ?></td></tr>
<tr><td>WP_CACHE</td><td><?php echo ( defined('WP_CACHE') && WP_CACHE ) ? '⚠️ true (cache attiva)' : '✅ false'; ?></td></tr>
<tr><td>DISALLOW_FILE_EDIT</td><td><?php echo ( defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT ) ? '✅ true (sicuro)' : '⚠️ false'; ?></td></tr>

<tr><td colspan="2" style="background:#f0f0f1;font-weight:bold">SEO Plugin</td></tr>
<tr><td>Rank Math</td><td><?php echo defined('RANK_MATH_VERSION') ? '✅ v' . esc_html(RANK_MATH_VERSION) : '❌ Non attivo'; ?></td></tr>
<tr><td>Yoast SEO</td><td><?php echo defined('WPSEO_VERSION') ? '✅ v' . esc_html(WPSEO_VERSION) : '❌ Non attivo'; ?></td></tr>
<tr><td>SAF SEO Fallback</td><td><?php echo ( !defined('RANK_MATH_VERSION') && !defined('WPSEO_VERSION') ) ? '✅ Attivo (canonical + OG)' : 'ℹ️ Disattivato (gestito dal plugin SEO)'; ?></td></tr>

<tr><td colspan="2" style="background:#f0f0f1;font-weight:bold">Server</td></tr>
<?php
$opcache = function_exists('opcache_get_status') ? @opcache_get_status(false) : false;
$ssl = is_ssl();
?>
<tr><td>Server</td><td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ?? 'Sconosciuto' ); ?></td></tr>
<tr><td>OPcache</td><td><?php echo $opcache ? '✅ Attivo' : '❌ Non attivo'; ?></td></tr>
<tr><td>SSL / HTTPS</td><td><?php echo $ssl ? '✅ Attivo' : '⚠️ Non attivo'; ?></td></tr>
<tr><td>max_execution_time</td><td><?php echo esc_html( ini_get('max_execution_time') ); ?>s</td></tr>
<tr><td>upload_max_filesize</td><td><?php echo esc_html( ini_get('upload_max_filesize') ); ?></td></tr>
<tr><td>post_max_size</td><td><?php echo esc_html( ini_get('post_max_size') ); ?></td></tr>

<tr><td colspan="2" style="background:#f0f0f1;font-weight:bold">Sicurezza SAF</td></tr>
<tr><td>Rate limiting login</td><td>✅ Attivo (max <?php echo esc_html( get_option('saf_max_login_attempts', 5) ); ?> tentativi)</td></tr>
<tr><td>Security headers</td><td>✅ Attivo</td></tr>
<tr><td>Enum utenti bloccato</td><td>✅ Attivo</td></tr>
<tr><td>XML-RPC</td><td><?php echo has_filter('xmlrpc_enabled', '__return_false') ? '✅ Disabilitato' : '⚠️ Attivo'; ?></td></tr>
</tbody>
</table>

<?php if ( defined('WP_DEBUG') && WP_DEBUG ) : ?>
<h3 style="margin-top:20px;">🐛 Debug Log</h3>
<?php
$log_file = WP_CONTENT_DIR . '/debug.log';
if ( file_exists( $log_file ) ) {
    $size = filesize( $log_file );
    $lines = $size > 0 ? array_slice( file( $log_file ), -30 ) : [];
    echo '<p>Dimensione: <strong>' . esc_html( size_format( $size ) ) . '</strong> — Ultime 30 righe:</p>';
    echo '<textarea class="saf-code-editor" rows="15" readonly>' . esc_textarea( implode( '', $lines ) ) . '</textarea>';
} else {
    echo '<p>⚠️ debug.log non esiste ancora — verrà creato al primo errore con WP_DEBUG_LOG=true.</p>';
}
?>
<?php endif; ?>
