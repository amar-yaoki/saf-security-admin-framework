<?php
/* ===== Tab: Guida ===== */
defined( 'ABSPATH' ) || exit;
?>
<h2>Guida SAF</h2>
<p>SAF — Security & Admin Framework è un plugin WordPress che aggiunge moduli funzionali per la gestione quotidiana del sito.</p>
<h3>Moduli</h3>
<ul>
    <li><strong>Sicurezza:</strong> rimuove tracce della versione WP, XML-RPC, pingback, protegge endpoint REST.</li>
    <li><strong>SEO:</strong> genera automaticamente meta description, supporta excerpt.</li>
    <li><strong>Performance:</strong> rimuove script superflui (emoji, embed, jQuery migrate), differisce CSS/JS.</li>
    <li><strong>Cleanup:</strong> disabilita commenti, pulisce admin bar, mostra contatori CPT nella dashboard.</li>
    <li><strong>Duplicate:</strong> duplica pagine e articoli con meta, tassonomie e thumbnail.</li>
    <li><strong>Post Status:</strong> contatore bozze, colori stato, shortcode version info.</li>
</ul>
<h3>Shortcode</h3>
<table class="wp-list-table widefat fixed striped">
    <thead><tr><th>Shortcode</th><th>Descrizione</th></tr></thead>
    <tbody>
        <tr><td><code>[saf_version_info]</code></td><td>Mostra versioni PHP, WordPress e SAF</td></tr>
    </tbody>
</table>
<h3>Child Theme</h3>
<p>Il child theme <strong>amar-design</strong> viene creato nella cartella <code>/wp-content/themes/amar-design/</code> con i file essenziali (style.css, functions.php) e le sottocartelle inc/, css/, js/.</p>
