<?php
defined( 'ABSPATH' ) || exit;
$st = isset( $_GET['st'] ) ? sanitize_key( $_GET['st'] ) : 'org';
$org = (array) get_option( 'saf_org_settings', [] );
$seo = (array) get_option( 'saf_seo_settings', [] );
$sec = (array) get_option( 'saf_sec_settings', [] );
$adv = (array) get_option( 'saf_adv_settings', [] );
$robots = (string) get_option( 'saf_robots_content', '' );
$nap = (string) get_option( 'saf_nap_html', '' );
$credits = (array) get_option( 'saf_credits_settings', [] );
$sc_opts = (array) get_option( 'saf_sc_settings', [] );
$sc_enabled = $sc_opts['social_share'] ?? null;
$dev_enabled = $sc_opts['dev_enabled'] ?? null;
$dev_urls = $sc_opts['dev_urls'] ?? [];
$all_on = ( $sc_enabled === null );
$all_dev_on = ( $dev_enabled === null );

$sub_tabs = [
    'org'      => 'Organizzazione',
    'seo'      => 'SEO & NAP',
    'security' => 'Sicurezza',
    'robots'   => 'Robots.txt',
    'nap'      => 'NAP Footer',
    'shortcode' => 'Shortcode',
    'advanced' => 'Avanzate',
    'child'    => 'Child Theme',
];
?>
<nav class="saf-sub-tabs" style="display:flex;gap:4px;flex-wrap:wrap;margin-bottom:16px;">
<?php foreach ( $sub_tabs as $slug => $label ) :
    $url = add_query_arg( [ 'page' => 'saf', 'tab' => 'settings', 'st' => $slug ], admin_url( 'admin.php' ) );
    $cls = $st === $slug ? 'button button-primary' : 'button';
?>
    <a href="<?php echo esc_url( $url ); ?>" class="<?php echo $cls; ?>" style="font-size:12px;"><?php echo esc_html( $label ); ?></a>
<?php endforeach; ?>
</nav>

<?php if ( $st === 'org' ) : ?>
<form method="post" action="options.php">
<?php settings_fields( 'saf_org_group' ); ?>
<h2>Organizzazione</h2>
<p class="description">Dati principali dell'azienda / organizzazione.</p>
<table class="form-table">
<tr><th><label for="saf_name">Nome <span style="color:red">*</span></label></th>
<td><input type="text" id="saf_name" name="saf_org_settings[name]" value="<?php echo esc_attr( $org['name'] ?? '' ); ?>" class="regular-text" required></td></tr>
<tr><th><label for="saf_url">URL</label></th>
<td><input type="url" id="saf_url" name="saf_org_settings[url]" value="<?php echo esc_attr( $org['url'] ?? home_url('/') ); ?>" class="regular-text"></td></tr>
<tr><th><label for="saf_logo">Logo</label></th>
<td><input type="text" id="saf_logo" name="saf_org_settings[logo]" value="<?php echo esc_attr( $org['logo'] ?? '' ); ?>" class="regular-text saf-media-input" data-preview="saf_logo_preview">
<button type="button" class="button saf-media-btn" data-target="saf_logo">📎 Media</button>
<?php if ( ! empty( $org['logo'] ) ) : ?><div style="margin-top:6px"><img src="<?php echo esc_url( $org['logo'] ); ?>" style="max-height:60px;border-radius:4px"></div><?php endif; ?></td></tr>
<tr><th>Indirizzo</th>
<td><input type="text" name="saf_org_settings[address]" value="<?php echo esc_attr( $org['address'] ?? '' ); ?>" class="regular-text" placeholder="Via Roma 1"><br>
<input type="text" name="saf_org_settings[cap]" value="<?php echo esc_attr( $org['cap'] ?? '' ); ?>" style="width:80px" placeholder="CAP">
<input type="text" name="saf_org_settings[city]" value="<?php echo esc_attr( $org['city'] ?? '' ); ?>" style="width:200px" placeholder="Città">
<input type="text" name="saf_org_settings[country]" value="<?php echo esc_attr( $org['country'] ?? 'IT' ); ?>" style="width:50px" placeholder="IT"></td></tr>
<tr><th><label for="saf_piva">P.IVA</label></th><td><input type="text" id="saf_piva" name="saf_org_settings[piva]" value="<?php echo esc_attr( $org['piva'] ?? '' ); ?>" class="regular-text"></td></tr>
<tr><th><label for="saf_email">Email</label></th><td><input type="email" id="saf_email" name="saf_org_settings[email]" value="<?php echo esc_attr( $org['email'] ?? '' ); ?>" class="regular-text"></td></tr>
<tr><th><label for="saf_phone">Telefono</label></th><td><input type="text" id="saf_phone" name="saf_org_settings[phone]" value="<?php echo esc_attr( $org['phone'] ?? '' ); ?>" class="regular-text" placeholder="+39 02 1234567"></td></tr>
</table>
<h3>📱 Social</h3>
<table class="form-table">
<?php foreach ( [ 'facebook'=>'Facebook', 'instagram'=>'Instagram', 'youtube'=>'YouTube', 'linkedin'=>'LinkedIn', 'twitter'=>'X / Twitter' ] as $k => $l ) : ?>
<tr><th><label for="saf_<?php echo $k; ?>"><?php echo esc_html( $l ); ?></label></th>
<td><input type="url" id="saf_<?php echo $k; ?>" name="saf_org_settings[<?php echo $k; ?>]" value="<?php echo esc_attr( $org[$k] ?? '' ); ?>" class="regular-text" placeholder="https://"></td></tr>
<?php endforeach; ?>
</table>
<?php submit_button( 'Salva Organizzazione' ); ?>
</form>

<?php elseif ( $st === 'seo' ) : ?>
<form method="post" action="options.php">
<?php settings_fields( 'saf_seo_group' ); ?>
<h2>SEO & NAP Footer</h2>
<p class="description">Immagini di fallback per Open Graph.</p>
<table class="form-table">
<?php foreach ( [ 'og_default' => 'OG Image Default (1200×630px)', 'og_default_2' => 'OG Image Secondaria' ] as $k => $l ) : $val = $seo[ $k ] ?? ''; ?>
<tr><th><label for="saf_<?php echo $k; ?>"><?php echo esc_html( $l ); ?></label></th>
<td><input type="text" id="saf_<?php echo $k; ?>" name="saf_seo_settings[<?php echo $k; ?>]" value="<?php echo esc_attr( $val ); ?>" class="regular-text saf-media-input" data-preview="saf_<?php echo $k; ?>_preview">
<button type="button" class="button saf-media-btn" data-target="saf_<?php echo $k; ?>">📎 Media</button>
<?php if ( $val ) : ?><div style="margin-top:6px"><img src="<?php echo esc_url( $val ); ?>" style="max-width:200px;border-radius:4px;border:1px solid #ddd"></div><?php endif; ?></td></tr>
<?php endforeach; ?>
</table>
<?php submit_button( 'Salva SEO' ); ?>
</form>

<?php elseif ( $st === 'security' ) : ?>
<form method="post" action="options.php">
<?php settings_fields( 'saf_sec_group' ); ?>
<h2>Sicurezza</h2>
<p class="description">Rate limiting login e misure di sicurezza.</p>
<table class="form-table">
<tr><th><label for="saf_max_attempts">Max tentativi login</label></th>
<td><input type="number" id="saf_max_attempts" name="saf_sec_settings[max_attempts]" value="<?php echo esc_attr( $sec['max_attempts'] ?? 5 ); ?>" min="3" max="20" style="width:70px">
<p class="description">Tentativi falliti prima del blocco IP (3-20).</p></td></tr>
<tr><th>Misure attive</th>
<td><ul style="margin:0;padding:0;list-style:none;line-height:2">
<li>✅ Rate limiting login</li><li>✅ XML-RPC disabilitato</li><li>✅ Blocco enumerazione utenti</li>
<li>✅ REST API protetta</li><li>✅ Headers HTTP sicurezza</li><li>✅ Versione WP nascosta</li>
<li>✅ Messaggio errore login generico</li><li>✅ File editor disabilitato</li>
<li>✅ Honeypot anti-spam</li></ul></td></tr>
</table>
<?php submit_button( 'Salva Sicurezza' ); ?>
</form>

<?php elseif ( $st === 'robots' ) : ?>
<form method="post" action="options.php">
<?php settings_fields( 'saf_robots_group' ); ?>
<h2>Robots.txt</h2>
<?php if ( class_exists( 'RankMath\\Robots_Txt' ) || defined( 'WPSEO_VERSION' ) ) : ?>
<div class="notice notice-warning inline" style="margin:12px 0"><p>Un plugin SEO è attivo — potrebbe sovrascrivere il robots.txt.</p></div>
<?php endif; ?>
<table class="form-table"><tr><td><textarea id="saf_robots" name="saf_robots_content" rows="25" style="width:100%;font-family:monospace;font-size:12px;line-height:1.6"><?php echo esc_textarea( $robots ); ?></textarea>
<p><a href="<?php echo esc_url( home_url('/robots.txt') ); ?>" target="_blank">Visualizza robots.txt live</a></p></td></tr></table>
<?php submit_button( 'Salva Robots.txt' ); ?>
</form>
<hr>
<h3>Esporta in file robots.txt</h3>
<form method="post">
<?php wp_nonce_field( 'saf_export_robots', 'saf_export_robots_nonce' ); ?>
<input type="hidden" name="saf_export_robots" value="1">
<?php submit_button( 'Esporta robots.txt', 'secondary' ); ?>
</form>

<?php elseif ( $st === 'nap' ) : ?>
<form method="post" action="options.php">
<?php settings_fields( 'saf_nap_group' ); ?>
<h2>NAP Footer</h2>
<p class="description">Contenuto HTML per il shortcode <code>[saf_nap_html]</code>.</p>
<table class="form-table"><tr><td>
<textarea id="saf_nap_html" name="saf_nap_html" rows="12" style="width:100%;font-family:monospace;font-size:13px;line-height:1.6" placeholder="<address class=&quot;footer-nap&quot;>…</address>"><?php echo esc_textarea( $nap ); ?></textarea>
<p class="description">Usa <code>[saf_nap_html]</code> nel footer. <a href="#" onclick="jQuery('#saf-nap-preview').toggle();return false;">Anteprima</a></p>
<div id="saf-nap-preview" style="display:none;padding:12px;background:#f8f8f8;border:1px solid #ddd;border-radius:4px"><?php echo wp_kses_post( $nap ); ?></div>
</td></tr></table>
<?php submit_button( 'Salva NAP' ); ?>
</form>

<?php elseif ( $st === 'shortcode' ) : ?>
<form method="post" action="options.php">
<?php settings_fields( 'saf_sc_group' ); ?>
<h2>Shortcode — Pulsanti Social Share</h2>
<h3>Social</h3>
<table class="form-table"><tr><th>Piattaforme attive</th><td>
<?php
$sc_platforms = [ 'facebook'=>['Facebook','#1877F2'], 'whatsapp'=>['WhatsApp','#25D366'], 'telegram'=>['Telegram','#0088cc'], 'instagram'=>['Instagram (copia link)','#C13584'], 'tiktok'=>['TikTok (copia link)','#010101'], 'email'=>['Email','#f47D39'], 'copy'=>['Copia link','#6c757d'] ];
foreach ( $sc_platforms as $key => $meta ) :
    $checked = $all_on || in_array( $key, (array) $sc_enabled, true );
?>
<label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;cursor:pointer;">
<input type="checkbox" name="saf_sc_settings[social_share][]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $checked ); ?>>
<span style="width:10px;height:10px;border-radius:50%;background:<?php echo $meta[1]; ?>;display:inline-block;"></span>
<strong><?php echo esc_html( $meta[0] ); ?></strong></label>
<?php endforeach; ?>
<p class="description">Shortcode: <code>[condividi_social]</code></p>
</td></tr></table>
<?php submit_button( 'Salva Shortcode' ); ?>
</form>

<?php elseif ( $st === 'advanced' ) : ?>
<form method="post" action="options.php">
<?php settings_fields( 'saf_adv_group' ); ?>
<h2>Impostazioni Avanzate</h2>
<h3>📧 SMTP</h3>
<table class="form-table">
<tr><th><label for="saf_smtp_name">From Name</label></th><td><input type="text" id="saf_smtp_name" name="saf_adv_settings[smtp_from_name]" value="<?php echo esc_attr( $adv['smtp_from_name'] ?? '' ); ?>" class="regular-text"></td></tr>
<tr><th><label for="saf_smtp_email">From Email</label></th><td><input type="email" id="saf_smtp_email" name="saf_adv_settings[smtp_from_email]" value="<?php echo esc_attr( $adv['smtp_from_email'] ?? '' ); ?>" class="regular-text"></td></tr>
</table>
<h3>💬 Commenti</h3>
<table class="form-table"><tr><th>Disabilita commenti</th><td><label><input type="checkbox" name="saf_adv_settings[disable_comments]" value="1" <?php checked( ! empty( $adv['disable_comments'] ) ); ?>> Disabilita completamente i commenti</label></td></tr></table>
<h3>🔒 HSTS</h3>
<table class="form-table"><tr><th>HTTP Strict Transport Security</th><td><label><input type="checkbox" name="saf_adv_settings[hsts_enabled]" value="1" <?php checked( ! empty( $adv['hsts_enabled'] ) ); ?>> Abilita HSTS</label><p class="description">⚠️ Attivare solo con SSL stabile.</p></td></tr></table>
<h3>🗂 Menu Admin</h3>
<p class="description">Nascondi voci di menu per utenti non amministratori.</p>
<table class="form-table">
<?php
$menu_items = [ 'tools'=>'Strumenti', 'comments'=>'Commenti', 'themes'=>'Temi', 'plugins'=>'Plugin', 'users'=>'Utenti', 'settings'=>'Impostazioni', 'projects'=>'Progetti' ];
$hide = $adv['hide_menu_items'] ?? [];
foreach ( $menu_items as $k => $l ) : ?>
<tr><th><?php echo esc_html( $l ); ?></th>
<td><label><input type="checkbox" name="saf_adv_settings[hide_menu_items][<?php echo $k; ?>]" value="1" <?php checked( ! empty( $hide[ $k ] ) ); ?>> Nascondi <?php echo esc_html( $l ); ?></label></td></tr>
<?php endforeach; ?>
</table>
<h3>➕ Nascondi voci personalizzate</h3>
<table class="form-table"><tr><td><textarea name="saf_adv_settings[custom_hide]" rows="3" class="large-text code" placeholder="edit.php?post_type=acf-field-group"><?php echo esc_textarea( $adv['custom_hide'] ?? '' ); ?></textarea>
<p class="description">Slug menu, uno per riga.</p></td></tr></table>
<h3>🖼 SVG</h3>
<table class="form-table"><tr><th>Abilita SVG</th><td><label><input type="checkbox" name="saf_adv_settings[enable_svg]" value="1" <?php checked( ! empty( $adv['enable_svg'] ) ); ?>> Abilita upload SVG</label></td></tr></table>
<?php submit_button( 'Salva Avanzate' ); ?>
</form>
<hr>
<h3>🗑 Pulisci opzioni SAF</h3>
<form method="post">
<?php wp_nonce_field( 'saf_cleanup', 'saf_cleanup_nonce' ); ?>
<input type="hidden" name="saf_action" value="cleanup_options">
<?php submit_button( 'Pulisci opzioni SAF', 'delete' ); ?>
</form>

<?php elseif ( $st === 'child' ) : ?>
<h2>Child Theme</h2>
<?php
$child_dir = get_theme_root() . '/amar-design/';
$exists = is_dir( $child_dir ) && file_exists( $child_dir . 'style.css' );
if ( ! $exists ) : ?>
<div class="notice notice-warning"><p>Il child theme amar-design non esiste ancora. Crealo ora per proteggere le tue personalizzazioni.</p>
<p><a href="<?php echo esc_url( wp_nonce_url( add_query_arg( [ 'page'=>'saf', 'tab'=>'settings', 'st'=>'child', 'saf_auto_create'=>'1' ], admin_url('admin.php') ), 'saf_auto_create', 'saf_auto_create_nonce' ) ); ?>" class="button button-primary">➕ Crea child theme ora</a></p></div>
<?php else :
$css_file = $child_dir . 'style.css';
$css_body = '';
if ( file_exists( $css_file ) ) {
    $content = file_get_contents( $css_file );
    $parts = explode( '*/', $content, 2 );
    $css_body = isset( $parts[1] ) ? trim( $parts[1] ) : '';
}
?>
<div class="notice notice-info" style="border-left-color:#f47D39;font-size:12px;margin:10px 0;">
    <p><strong>⏳ Nota:</strong> Dopo il salvataggio, OPcache e la cache PHP possono ritardare la propagazione. Ricarica la pagina se non vedi le modifiche.</p>
</div>
<form method="post">
<?php wp_nonce_field( 'saf_child_css', 'saf_child_nonce' ); ?>
<input type="hidden" name="saf_action" value="child_save_css">
<textarea name="saf_css_body" rows="20" class="saf-code-editor"><?php echo esc_textarea( $css_body ); ?></textarea>
<?php submit_button( 'Salva style.css' ); ?>
</form>
<?php endif; ?><?php endif; ?>
