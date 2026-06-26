<?php
defined( 'ABSPATH' ) || exit;
$credits = (array) get_option( 'saf_credits_settings', [] );
?>
<form method="post" action="options.php">
<?php settings_fields( 'saf_credits_group' ); ?>
<h2>Credits Sviluppatore</h2>
<p class="description">Questi dati appaiono nel footer admin e nel widget dashboard.</p>
<table class="form-table">
<tr><th><label for="credits_author">Autore</label></th>
<td><input type="text" id="credits_author" name="saf_credits_settings[author_name]" value="<?php echo esc_attr( $credits['author_name'] ?? '' ); ?>" class="regular-text" placeholder="Amar Amoretti"></td></tr>
<tr><th><label for="credits_url">URL Autore</label></th>
<td><input type="url" id="credits_url" name="saf_credits_settings[author_url]" value="<?php echo esc_attr( $credits['author_url'] ?? '' ); ?>" class="regular-text" placeholder="https://yaoki.academy"></td></tr>
<tr><th><label for="credits_client">Cliente</label></th>
<td><input type="text" id="credits_client" name="saf_credits_settings[client_name]" value="<?php echo esc_attr( $credits['client_name'] ?? '' ); ?>" class="regular-text" placeholder="Nome del cliente"></td></tr>
<tr><th><label for="credits_created">Data creazione</label></th>
<td><input type="text" id="credits_created" name="saf_credits_settings[created]" value="<?php echo esc_attr( $credits['created'] ?? '' ); ?>" class="regular-text" placeholder="es. 2024"></td></tr>
</table>
<?php submit_button( 'Salva Credits' ); ?>
</form>
