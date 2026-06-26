<?php
defined( 'ABSPATH' ) || exit;
$gt = isset( $_GET['gt'] ) ? sanitize_key( $_GET['gt'] ) : 'info';
$checks = (array) get_option( 'saf_checklist', [] );
$notes  = (string) get_option( 'saf_dev_notes', '' );
$proj   = (array) get_option( 'saf_project_docs', [] );
$docs   = $proj['docs'] ?? [];
$adv    = (array) get_option( 'saf_adv_settings', [] );

$g_tabs = [
    'info'      => 'ℹ️ Info', 'struttura' => '🗂 Struttura', 'shortcode' => '📋 Shortcode',
    'sicurezza' => '🔒 Sicurezza', 'checklist' => '✅ Checklist', 'progetto' => '📄 Progetto', 'note' => '🗒 Note Dev',
];
if ( ! empty( $adv['hide_progetto'] ) ) unset( $g_tabs['progetto'] );
?>
<nav class="saf-sub-tabs" style="display:flex;gap:4px;flex-wrap:wrap;margin-bottom:16px;">
<?php foreach ( $g_tabs as $slug => $label ) :
    $url = add_query_arg( [ 'page' => 'saf', 'tab' => 'guida', 'gt' => $slug ], admin_url( 'admin.php' ) );
    $cls = $gt === $slug ? 'button button-primary' : 'button';
?>
    <a href="<?php echo esc_url( $url ); ?>" class="<?php echo $cls; ?>" style="font-size:12px;"><?php echo esc_html( $label ); ?></a>
<?php endforeach; ?>
</nav>

<?php if ( $gt === 'info' ) : ?>
<div style="text-align:center;padding:20px 0;border-bottom:2px solid #2ea3f2;margin-bottom:24px">
<h2 style="font-size:26px;margin:0 0 6px">Amar SAF</h2>
<p style="font-size:15px;color:#555;margin:0">Security &amp; Admin Framework v<?php echo SAF_VERSION; ?></p>
</div>
<table class="widefat" style="max-width:400px;margin-bottom:20px">
<tr><td style="font-weight:600">Autore</td><td>Amar Amoretti</td></tr>
<tr><td style="font-weight:600">Sito</td><td><a href="https://yaoki.academy" target="_blank">yaoki.academy</a></td></tr>
<tr><td style="font-weight:600">Licenza</td><td>GNU GPL v2+</td></tr>
<tr><td style="font-weight:600">PHP Min</td><td>8.2+</td></tr>
<tr><td style="font-weight:600">WP Min</td><td>7.0+</td></tr>
</table>
<p><a href="<?php echo esc_url( SAF_URL . 'guide-IT.html' ); ?>" target="_blank" class="button button-primary">📖 Guida completa (HTML)</a></p>

<?php elseif ( $gt === 'struttura' ) : ?>
<h2>Struttura del Child Theme</h2>
<table class="widefat saf-table-monospace">
<thead><tr><th>File</th><th>Descrizione</th></tr></thead>
<tbody>
<tr><td><code>functions.php</code></td><td>Entry point — carica tutti i moduli /inc/</td></tr>
<tr><td><code>style.css</code></td><td>Header child theme + override CSS</td></tr>
<tr><td><code>inc/theme-setup.php</code></td><td>Enqueue, theme support, cleanup head</td></tr>
<tr><td><code>inc/security.php</code></td><td>Login brandizzato, rate limiting, XML-RPC, headers</td></tr>
<tr><td><code>inc/performance.php</code></td><td>Dequeue, oEmbed, heartbeat, revisioni, lazy load</td></tr>
<tr><td><code>inc/helpers.php</code></td><td>YouTube, truncate, data, social share, paginazione</td></tr>
<tr><td><code>inc/seo.php</code></td><td>JSON-LD, canonical, OG, breadcrumb</td></tr>
<tr><td><code>inc/admin.php</code></td><td>Pagina Dati Sito, org data, SMTP</td></tr>
<tr><td><code>inc/cleanup.php</code></td><td>Commenti, menu, admin bar, footer</td></tr>
<tr><td><code>inc/dashboard.php</code></td><td>Widget dashboard, pulsante frontend</td></tr>
<tr><td><code>inc/guida.php</code></td><td>Guida Sito interattiva</td></tr>
<tr><td><code>css/login.css</code></td><td>Login brandizzata</td></tr>
<tr><td><code>js/main.js</code></td><td>JS frontend (Netflix, copia link, scroll)</td></tr>
<tr><td><code>js/admin.js</code></td><td>JS backend (media picker, checklist)</td></tr>
</tbody>
</table>

<?php elseif ( $gt === 'shortcode' ) : ?>
<h2>Shortcode Reference</h2>
<?php $scs = [
    ['tag'=>'[saf_breadcrumb]','desc'=>'Breadcrumb semantico con JSON-LD.','params'=>['sep="›"' => 'Separatore','home_label="Home"'=>'Label home'],'esempi'=>['[saf_breadcrumb]','[saf_breadcrumb sep="/"]']],
    ['tag'=>'[condividi_social]','desc'=>'Pulsanti social sharing (configurabili da Impostazioni → Shortcode).','params'=>[],'esempi'=>['[condividi_social]']],
    ['tag'=>'[saf_reading_time]','desc'=>'Tempo di lettura dal word count.','params'=>['wpm="200"'=>'Parole/min','label="Lettura: "'=>'Testo','suffix=" min"'=>'Suffisso'],'esempi'=>['[saf_reading_time]','[saf_reading_time wpm="150"]']],
    ['tag'=>'[saf_footer_info]','desc'=>'Dati organizzazione.','params'=>['mostra="nome,piva,email"'=>'Campi'],'esempi'=>['[saf_footer_info]','[saf_footer_info mostra="nome,piva,address,email,phone"]']],
    ['tag'=>'[saf_nap_html]','desc'=>'NAP HTML da Impostazioni → NAP Footer.','params'=>['class=""'=>'Classe wrapper'],'esempi'=>['[saf_nap_html]','[saf_nap_html class="footer-nap"]']],
    ['tag'=>'[saf_youtube]','desc'=>'Embed YouTube senza cookie.','params'=>['url=""'=>'URL video','title=""'=>'Titolo','width="560"'=>'Larghezza','height="315"'=>'Altezza'],'esempi'=>['[saf_youtube url="https://youtu.be/ID"]']],
];
foreach ( $scs as $sc ) : ?>
<div style="background:#fff;border:1px solid #ddd;border-left:4px solid #2271b1;border-radius:6px;padding:14px 18px;margin-bottom:12px;">
<code style="font-size:14px;background:#f0f4ff;padding:3px 8px;border-radius:3px;cursor:pointer;" onclick="navigator.clipboard.writeText(this.textContent);this.style.background='#c3e6cb';setTimeout(()=>this.style.background='#f0f4ff',800)"><?php echo esc_html( $sc['tag'] ); ?></code>
<p style="margin:8px 0 4px;color:#444;font-size:13px;"><?php echo esc_html( $sc['desc'] ); ?></p>
<?php if ( $sc['params'] ) : ?><table class="widefat" style="margin-top:6px;"><thead><tr><th>Parametro</th><th>Descrizione</th></tr></thead><tbody>
<?php foreach ( $sc['params'] as $k => $v ) : ?><tr><td><code><?php echo esc_html( $k ); ?></code></td><td><?php echo esc_html( $v ); ?></td></tr><?php endforeach; ?>
</tbody></table><?php endif; ?>
<?php if ( $sc['esempi'] ) : ?><div style="margin-top:6px;"><strong>Esempi:</strong> <?php foreach ( $sc['esempi'] as $ex ) : ?><code style="cursor:pointer;background:#f6f6f6;padding:2px 6px;border-radius:3px;margin-right:4px;" onclick="navigator.clipboard.writeText(this.textContent);this.style.background='#c3e6cb';setTimeout(()=>this.style.background='#f6f6f6',800)"><?php echo esc_html( $ex ); ?></code><?php endforeach; ?></div><?php endif; ?>
</div>
<?php endforeach; ?>

<?php elseif ( $gt === 'sicurezza' ) : ?>
<h2>Misure di Sicurezza Attive</h2>
<?php
$measures = [
    ['title'=>'Rate limiting login','detail'=>'Max N tentativi per IP in 15 minuti. Configurabile da Impostazioni → Sicurezza.','status'=>'attiva'],
    ['title'=>'Blocco enumerazione utenti','detail'=>'?author=N → redirect 301. REST API users → 403 per non autenticati.','status'=>'attiva'],
    ['title'=>'XML-RPC disabilitato','detail'=>'xmlrpc_enabled=false, metodi svuotati, header X-Pingback rimosso.','status'=>'attiva'],
    ['title'=>'Headers HTTP sicurezza','detail'=>'X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, HSTS (opzionale).','status'=>'attiva'],
    ['title'=>'Versione WP nascosta','detail'=>'wp_generator rimosso, parametro ?ver dagli asset.','status'=>'attiva'],
    ['title'=>'Messaggio errore login generico','detail'=>'Sempre "Credenziali non valide." — non rivela username/password.','status'=>'attiva'],
    ['title'=>'File editor disabilitato','detail'=>'DISALLOW_FILE_EDIT = true.','status'=>'attiva'],
    ['title'=>'Honeypot anti-spam','detail'=>'Funzioni saf_honeypot_field() e saf_is_spam() per form custom.','status'=>'attiva'],
];
foreach ( $measures as $m ) : ?>
<div style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:12px 16px;margin-bottom:10px;">
<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;"><strong><?php echo esc_html( $m['title'] ); ?></strong> <span style="font-size:11px;background:#e6f4ea;color:#137333;padding:2px 8px;border-radius:10px;font-weight:600;">✅ <?php echo esc_html( $m['status'] ); ?></span></div>
<p style="margin:0;color:#444;font-size:13px;"><?php echo esc_html( $m['detail'] ); ?></p>
</div>
<?php endforeach; ?>

<?php elseif ( $gt === 'checklist' ) : ?>
<h2>Checklist Pre-Go-Live</h2>
<form method="post" action="options.php">
<?php settings_fields( 'saf_guida_group' ); ?>
<?php
$groups = [
    'backend' => [ 'label'=>'⚙️ Backend', 'items'=>[ 'org_name'=>'Compilare nome organizzazione', 'org_address'=>'Compilare indirizzo', 'org_social'=>'Inserire social network', 'seo_og'=>'Caricare OG Image', 'rankmath'=>'Configurare Rank Math' ] ],
    'sicurezza' => [ 'label'=>'🔒 Sicurezza', 'items'=>[ 'check_login'=>'Login brandizzato', 'check_author'=>'?author=1 redirect', 'check_headers'=>'Headers HTTP', 'check_xmlrpc'=>'XML-RPC disabilitato', 'hsts'=>'Valutare HSTS' ] ],
    'seo' => [ 'label'=>'🔍 SEO', 'items'=>[ 'jld_org'=>'JSON-LD Organization', 'jld_website'=>'JSON-LD WebSite', 'breadcrumb'=>'Verificare breadcrumb', 'og_tags'=>'Testare OG tags', 'canonical'=>'Verificare canonical', 'sitemap'=>'Sitemap a GSC' ] ],
    'performance' => [ 'label'=>'⚡ Performance', 'items'=>[ 'pagespeed'=>'PageSpeed 90+', 'gutenberg_css'=>'CSS Gutenberg assente', 'images'=>'Immagini ottimizzate' ] ],
    'gdpr' => [ 'label'=>'🔐 GDPR', 'items'=>[ 'cookie_banner'=>'Cookie banner', 'privacy_policy'=>'Privacy Policy', 'terms'=>'Termini', 'antispam'=>'Anti-spam' ] ],
    'finale' => [ 'label'=>'🚀 Finale', 'items'=>[ 'staging_test'=>'Test su staging', 'backup'=>'Backup completo', 'analytics'=>'GA4 configurato', 'search_console'=>'GSC verificata', 'uptime'=>'Uptime monitoring' ] ],
];
$total_items = 0; $done_items = 0;
foreach ( $groups as $g ) { foreach ( $g['items'] as $k => $l ) { $total_items++; if ( ! empty( $checks[ $k ] ) ) $done_items++; } }
$pct = $total_items > 0 ? round( $done_items / $total_items * 100 ) : 0;
?>
<div style="background:#e2e8f0;border-radius:8px;height:22px;margin-bottom:16px;position:relative;overflow:hidden;">
<div style="background:linear-gradient(90deg,#2271b1,#00a32a);height:100%;border-radius:8px;width:<?php echo $pct; ?>%;transition:width .4s;"></div>
<span style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);font-size:11px;font-weight:700;color:#fff;text-shadow:0 1px 2px rgba(0,0,0,.3);"><?php echo $done_items; ?>/<?php echo $total_items; ?> (<?php echo $pct; ?>%)</span>
</div>
<?php foreach ( $groups as $g ) : ?>
<h3><?php echo esc_html( $g['label'] ); ?></h3>
<ul style="margin:0 0 12px;padding:0;list-style:none;">
<?php foreach ( $g['items'] as $k => $l ) : $checked = ! empty( $checks[ $k ] ); ?>
<li style="padding:5px 8px;border-radius:4px;margin-bottom:2px;<?php echo $checked ? 'text-decoration:line-through;color:#999;background:#f0faf0;' : ''; ?>">
<label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
<input type="checkbox" name="saf_checklist[<?php echo esc_attr( $k ); ?>]" value="1" <?php checked( $checked ); ?>>
<?php echo esc_html( $l ); ?></label></li>
<?php endforeach; ?>
</ul>
<?php endforeach; ?>
<?php submit_button( 'Salva Checklist' ); ?>
</form>

<?php elseif ( $gt === 'progetto' ) : ?>
<h2>Documento di Progetto</h2>
<form method="post" action="options.php">
<?php settings_fields( 'saf_guida_group' ); ?>
<div id="saf-docs-list">
<?php if ( empty( $docs ) ) : ?>
<div class="saf-doc-row"><button type="button" class="saf-doc-remove" onclick="this.closest('.saf-doc-row').remove()">✕</button>
<table style="width:100%"><tr><td style="width:120px;font-weight:600;padding:4px 6px;">URL</td><td><input type="text" name="saf_project_docs[docs][0][url]" class="regular-text saf-media-input" placeholder="URL documento"></td></tr>
<tr><td style="font-weight:600;padding:4px 6px;">Titolo</td><td><input type="text" name="saf_project_docs[docs][0][title]" class="regular-text" placeholder="Titolo"></td></tr>
<tr><td style="font-weight:600;padding:4px 6px;">Data</td><td><input type="text" name="saf_project_docs[docs][0][date]" style="width:160px" placeholder="Data"></td></tr>
<tr><td style="font-weight:600;padding:4px 6px;">Note</td><td><textarea name="saf_project_docs[docs][0][notes]" rows="2" class="regular-text"></textarea></td></tr></table></div>
<?php else : foreach ( $docs as $i => $doc ) : ?>
<div class="saf-doc-row"><button type="button" class="saf-doc-remove" onclick="this.closest('.saf-doc-row').remove()">✕</button>
<table style="width:100%"><tr><td style="width:120px;font-weight:600;padding:4px 6px;">URL</td><td><input type="text" name="saf_project_docs[docs][<?php echo $i; ?>][url]" value="<?php echo esc_attr( $doc['url'] ?? '' ); ?>" class="regular-text saf-media-input"><?php if ( ! empty( $doc['url'] ) ) : ?> <a href="<?php echo esc_url( $doc['url'] ); ?>" target="_blank" class="button">↗</a><?php endif; ?></td></tr>
<tr><td style="font-weight:600;padding:4px 6px;">Titolo</td><td><input type="text" name="saf_project_docs[docs][<?php echo $i; ?>][title]" value="<?php echo esc_attr( $doc['title'] ?? '' ); ?>" class="regular-text"></td></tr>
<tr><td style="font-weight:600;padding:4px 6px;">Data</td><td><input type="text" name="saf_project_docs[docs][<?php echo $i; ?>][date]" value="<?php echo esc_attr( $doc['date'] ?? '' ); ?>" style="width:160px"></td></tr>
<tr><td style="font-weight:600;padding:4px 6px;">Note</td><td><textarea name="saf_project_docs[docs][<?php echo $i; ?>][notes]" rows="2" class="regular-text"><?php echo esc_textarea( $doc['notes'] ?? '' ); ?></textarea></td></tr></table></div>
<?php endforeach; endif; ?>
</div>
<button type="button" id="saf-add-doc" class="button" style="margin:8px 0">+ Aggiungi documento</button>
<script>
document.getElementById('saf-add-doc')?.addEventListener('click',function(){
    var idx = document.querySelectorAll('#saf-docs-list .saf-doc-row').length;
    var html = '<div class="saf-doc-row"><button type="button" class="saf-doc-remove" onclick="this.closest(\'.saf-doc-row\').remove()">✕</button><table style="width:100%">';
    ['url','title','date','notes'].forEach(function(f){
        var label = f==='url'?'URL':f==='title'?'Titolo':f==='date'?'Data':'Note';
        html += '<tr><td style="width:120px;font-weight:600;padding:4px 6px;">'+label+'</td><td>';
        if (f==='notes') html += '<textarea name="saf_project_docs[docs]['+idx+'][notes]" rows="2" class="regular-text"></textarea>';
        else if (f==='url') html += '<input type="text" name="saf_project_docs[docs]['+idx+'][url]" class="regular-text saf-media-input">';
        else if (f==='date') html += '<input type="text" name="saf_project_docs[docs]['+idx+'][date]" style="width:160px">';
        else html += '<input type="text" name="saf_project_docs[docs]['+idx+'][title]" class="regular-text">';
        html += '</td></tr>';
    });
    html += '</table></div>';
    document.getElementById('saf-docs-list').insertAdjacentHTML('beforeend',html);
});
</script>
<?php submit_button( 'Salva Documenti' ); ?>
</form>

<?php elseif ( $gt === 'note' ) : ?>
<h2>Note Sviluppatore</h2>
<form method="post" action="options.php">
<?php settings_fields( 'saf_guida_group' ); ?>
<textarea name="saf_dev_notes" rows="20" style="width:100%;font-family:monospace;font-size:13px;line-height:1.6"><?php echo esc_textarea( $notes ); ?></textarea>
<p class="description">Appunti di sessione, decisioni tecniche.</p>
<?php submit_button( 'Salva Note' ); ?>
</form>
<?php endif; ?>
