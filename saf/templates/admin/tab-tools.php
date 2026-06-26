<?php
/* ===== Tab: Strumenti ===== */
defined( 'ABSPATH' ) || exit;
?>
<h2>Strumenti</h2>
<div class="saf-grid-2col">
    <div class="saf-card">
        <h3>Duplica Contenuti</h3>
        <p>Vai alla lista di pagine o articoli e usa il link <strong>Clona</strong> sotto ogni elemento.</p>
        <p><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>" class="button">Vai alle Pagine</a>
        <a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>" class="button">Vai agli Articoli</a></p>
    </div>
    <div class="saf-card">
        <h3>Shortcode disponibili</h3>
        <ul>
            <li><code>[saf_version_info]</code> — mostra versioni PHP, WP, SAF</li>
        </ul>
    </div>
</div>
