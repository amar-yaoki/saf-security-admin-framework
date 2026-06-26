<?php
defined( 'ABSPATH' ) || exit;
if ( isset( $_GET['saf_result'] ) ) {
    $r = sanitize_key( $_GET['saf_result'] );
    $msgs = [ 'success' => 'Child theme creato con successo.', 'already_exists' => 'Il child theme esiste già.', 'error_source_missing' => 'Errore: sorgente child-theme non trovata.', 'error_mkdir' => 'Errore creazione directory.' ];
    echo '<div class="notice notice-' . ( $r === 'success' ? 'success' : 'error' ) . '"><p>' . esc_html( $msgs[ $r ] ?? 'Operazione completata.' ) . '</p></div>';
}
?>
<h2>Child Theme</h2>
<?php
$child_dir = get_theme_root() . '/amar-design/';
$exists = is_dir( $child_dir ) && file_exists( $child_dir . 'style.css' );
if ( ! $exists ) : ?>
<div class="notice notice-warning"><p><strong>Child theme non presente.</strong> Crealo per proteggere le tue personalizzazioni dagli aggiornamenti del tema.</p>
<form method="post">
<?php wp_nonce_field( 'saf_child_create', 'saf_child_create_nonce' ); ?>
<input type="hidden" name="saf_action" value="child_create">
<?php submit_button( '➕ Crea Child Theme', 'primary', 'saf_create_child_btn' ); ?>
</form></div>
<?php else :
$active_child = ( get_template() !== get_stylesheet() ) ? get_stylesheet() : false;
$managed_name = ( $active_child && $active_child !== 'amar-design' ) ? $active_child : 'amar-design';
$css_file = get_theme_root() . '/' . $managed_name . '/style.css';
$css_body = '';
if ( file_exists( $css_file ) ) {
    $content = file_get_contents( $css_file );
    $parts = explode( '*/', $content, 2 );
    $css_body = isset( $parts[1] ) ? trim( $parts[1] ) : '';
}
?>
<p>Child theme attivo: <strong><?php echo esc_html( $managed_name ); ?></strong></p>
<form method="post">
<?php wp_nonce_field( 'saf_child_css', 'saf_child_nonce' ); ?>
<input type="hidden" name="saf_action" value="child_save_css">
<table class="form-table">
<tr><th>style.css</th><td><textarea name="saf_css_body" rows="18" class="saf-code-editor"><?php echo esc_textarea( $css_body ); ?></textarea></td></tr>
</table>
<?php submit_button( 'Salva style.css' ); ?>
</form>
<?php endif; ?>
