<?php
/* ===== Tab: Moduli ===== */
defined( 'ABSPATH' ) || exit;
$modules = [
    'Sicurezza' => [
        'desc' => 'Rimuove generator tag, XML-RPC, pingback, endpoint REST anonimi, notifiche WP_DEBUG.',
        'file' => 'Modules/Security.php',
    ],
    'SEO' => [
        'desc' => 'Meta description automatica, excerpt support, title filter.',
        'file' => 'Modules/SEO.php',
    ],
    'Performance' => [
        'desc' => 'Rimuove emoji, embed, jQuery migrate, defer CSS/JS opzionale.',
        'file' => 'Modules/Performance.php',
    ],
    'Cleanup' => [
        'desc' => 'Disabilita commenti, pulisce admin bar, rimuove menu default, contatori CPT.',
        'file' => 'Modules/Cleanup.php',
    ],
    'Duplicate' => [
        'desc' => 'Aggiunge link "Clona" a pagine e articoli con copia di meta, tassonomie e thumbnail.',
        'file' => 'Modules/Duplicate.php',
    ],
    'Post Status' => [
        'desc' => 'Contatore bozze admin bar, stati colore, shortcode version info.',
        'file' => 'Modules/PostStatus.php',
    ],
];
?>
<h2>Moduli SAF</h2>
<p>Tutti i moduli sono caricati all'attivazione del plugin. Ogni modulo segue il pattern OOP <?php echo 'SAF\Modules\*'; ?>.</p>
<table class="wp-list-table widefat fixed striped">
    <thead><tr><th>Modulo</th><th>Descrizione</th><th>Classe</th></tr></thead>
    <tbody>
        <?php foreach ( $modules as $name => $info ): ?>
            <tr>
                <td><strong><?php echo esc_html( $name ); ?></strong></td>
                <td><?php echo esc_html( $info['desc'] ); ?></td>
                <td><code><?php echo esc_html( $info['file'] ); ?></code></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
