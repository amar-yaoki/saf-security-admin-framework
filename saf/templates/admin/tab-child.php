<?php
/* ===== Tab: Child Theme ===== */
defined( 'ABSPATH' ) || exit;
$child_dir = get_theme_root() . '/amar-design/';
$exists    = is_dir( $child_dir ) && file_exists( $child_dir . 'style.css' );
$parent    = function_exists( 'saf_auto_parent_theme' ) ? saf_auto_parent_theme() : wp_get_theme()->get( 'Template' );
if ( empty( $parent ) ) $parent = wp_get_theme()->get_stylesheet();
$result    = isset( $_GET['saf_result'] ) ? sanitize_text_field( $_GET['saf_result'] ) : '';
?>
<h2>Child Theme «Amar Design»</h2>
<?php if ( $result === 'success' ): ?>
    <div class="notice notice-success is-dismissible"><p>Child theme creato con successo! Attivalo da <a href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>">Aspetto → Temi</a>.</p></div>
<?php elseif ( $result === 'already_exists' ): ?>
    <div class="notice notice-info is-dismissible"><p>Il child theme esiste già.</p></div>
<?php elseif ( $result ): ?>
    <div class="notice notice-error is-dismissible"><p>Errore: <?php echo esc_html( $result ); ?></p></div>
<?php endif; ?>

<?php if ( $exists ): ?>
    <div class="notice notice-success" style="border-left-color:#46b450"><p>✅ Child theme presente in <code><?php echo esc_html( $child_dir ); ?></code></p></div>
    <p><a href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>">Vai alla gestione temi</a></p>
<?php else: ?>
    <div class="notice notice-warning"><p>⚠️ Child theme non presente. Crea ora per proteggere le tue personalizzazioni.</p></div>
    <p>Il child theme sarà creato con il nome <strong>amar-design</strong> e basato sul tema attivo: <strong><?php echo esc_html( $parent ); ?></strong>.</p>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'saf_create_child', 'saf_child_nonce' ); ?>
        <input type="hidden" name="action" value="saf_create_child">
        <p><label><input type="checkbox" name="force" value="1"> Sovrascrivi se esiste</label></p>
        <?php submit_button( 'Crea Child Theme' ); ?>
    </form>
<?php endif; ?>
