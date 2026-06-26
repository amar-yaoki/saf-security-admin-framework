<?php
/* ===== Tab: Dashboard ===== */
defined( 'ABSPATH' ) || exit;
$child_exists = is_dir( get_theme_root() . '/amar-design/' ) && file_exists( get_theme_root() . '/amar-design/style.css' );
$posts  = wp_count_posts();
$drafts = (int) ( $posts->draft ?? 0 ) + (int) ( $posts->pending ?? 0 );
$users  = count_users();
?>
<div class="saf-grid-2col">
    <div class="saf-card">
        <h3>Stato Generale</h3>
        <ul>
            <li><strong>Bozze totali:</strong> <?php echo (int) $drafts; ?></li>
            <li><strong>Utenti registrati:</strong> <?php echo (int) $users['total_users']; ?></li>
            <li><strong>Child Theme:</strong> <?php echo $child_exists ? '✅ Attivo' : '❌ Non presente'; ?></li>
            <li><strong>Versione SAF:</strong> <?php echo esc_html( SAF_VERSION ); ?></li>
            <li><strong>Versione WP:</strong> <?php echo esc_html( get_bloginfo( 'version' ) ); ?></li>
            <li><strong>Versione PHP:</strong> <?php echo esc_html( phpversion() ); ?></li>
        </ul>
    </div>
    <div class="saf-card">
        <h3>Azioni Rapide</h3>
        <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=saf&tab=child' ) ); ?>" class="button">Gestisci Child Theme</a></p>
        <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=saf&tab=tools' ) ); ?>" class="button">Strumenti</a></p>
        <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=saf&tab=guida' ) ); ?>" class="button">Guida</a></p>
    </div>
</div>
