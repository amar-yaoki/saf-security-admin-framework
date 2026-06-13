<?php
/**
 * SAF Italian strings.
 * @package SAF
 */

return array(
    /* Tabs */
    'tab_org'      => '🏢 Organizzazione',
    'tab_seo'      => '🔍 SEO & Immagini',
    'tab_security' => '🔒 Sicurezza',
    'tab_robots'   => '🤖 Robots.txt',
    'tab_nap'       => '📍 NAP Footer',
    'tab_shortcode' => '📋 Shortcode',
    'tab_advanced'  => '⚙️ Avanzate',
    'tab_child'    => '🎨 Child Theme',
    'tab_credits'  => '📝 Crediti',
    'tab_sistema'  => '🖥 Sistema',
    'tab_plugins'  => '🛠 Strumenti',
    'sistema_title' => '🖥 Informazioni di Sistema',
    'sistema_desc'  => 'Panoramica completa dell\'ambiente server, PHP, WordPress e SAF. Visibile solo agli amministratori.',

    'page_title'   => 'Dati Sito',
    'site_data'    => 'Dati Sito',
    'menu_page_title' => 'Dati Sito',
    'menu_title'    => '⚙️ Dati Sito',

    /* Org tab */
    'org_title'    => '🏢 Dati Organizzazione',
    'org_desc'     => 'Alimentano il JSON-LD <code>Organization</code> in homepage e lo shortcode <code>[saf_footer_info]</code>.',
    'org_name'     => 'Nome organizzazione',
    'org_url'      => 'URL sito web',
    'org_logo'     => 'URL Logo',
    'org_logo_btn' => 'Carica Logo',
    'org_address'  => 'Indirizzo',
    'org_zip'      => 'CAP',
    'org_city'     => 'Città',
    'org_vat'      => 'Partita IVA',
    'org_email'    => 'Email',
    'org_phone'    => 'Telefono',
    'org_url_desc'  => 'Default: URL WordPress corrente.',
    'org_logo_desc' => 'Ideale: 512×512px, PNG trasparente. Usato nel JSON-LD e nella pagina login.',
    'org_vat_desc' => 'Non incluso nel JSON-LD pubblico — riferimento interno e <code>[saf_footer_info]</code>.',
    'org_social'   => 'Profili Social',
    'org_social_desc' => 'Inserire URL completi (es. https://facebook.com/pagina). Usati come sameAs nel JSON-LD Organization.',

    /* SEO tab */
    'seo_title'    => '🔍 SEO & Immagini Default',
    'seo_desc'     => 'Immagini di default per Open Graph e condivisioni social.',
    'seo_img1'     => 'Immagine OG Default',
    'seo_img2'     => 'Immagine OG Secondaria',

    /* Security tab */
    'sec_title'    => '🔒 Impostazioni Sicurezza',
    'sec_access'   => 'Accesso al backend via <code>/wp-login.php</code> standard.',
    'sec_login_brand' => 'La pagina login è brandizzata con il logo e i colori del sito.',
    'sec_max_attempts' => 'Tentativi login massimi',
    'sec_max_desc' => 'Tra 3 e 20. Superato il limite, l\'IP viene bloccato per 15 minuti.',
    'sec_active'   => 'Misure attive',
    'sec_list_rate' => '✅ Rate limiting login (max tentativi per IP)',
    'sec_list_xmlrpc' => '✅ XML-RPC disabilitato',
    'sec_list_enum' => '✅ Enumerazione utenti bloccata (?author=N &rarr; 301)',
    'sec_list_rest' => '✅ REST /wp/v2/users bloccata per ospiti',
    'sec_list_headers' => '✅ Headers HTTP sicurezza (X-Frame, X-Content-Type…)',
    'sec_list_wp_version' => '✅ Versione WordPress nascosta',
    'sec_list_login_errors' => '✅ Messaggi errore login generici',
    'sec_list_file_edit' => '✅ File editor backend disabilitato',
    'sec_list_spam' => '✅ Spam commenti REST bloccato',
    'sec_list_login_brand' => '✅ Pagina login brandizzata',

    /* Robots tab */
    'robots_title' => '🤖 Editor Robots.txt',
    'robots_txt_label' => 'Contenuto robots.txt',
    'robots_warn_seo' => '⚠️ <strong>Rank Math</strong> o <strong>Yoast SEO</strong> è attivo.<br>SAF non gestisce il robots.txt &mdash; lascia fare al plugin SEO. Il contenuto viene salvato ma non visibile su <code>%s</code> finché Rank Math/Yoast sono attivi.<br>Puoi usare il pulsante <strong>Esporta nella root</strong> per creare un file fisico.',
    'robots_warn_info' => 'ℹ️ WordPress serve questo contenuto su <code>%s</code>.<br><strong>{{SITE_URL}}</strong> viene sostituito automaticamente con l\'URL del sito.<br>⚠️ Se esiste un file robots.txt fisico, questo editor non ha effetto &mdash; rimuovi prima il file fisico.',
    'robots_view_live' => '↗ Visualizza robots.txt live',
    'robots_export_title' => '📁 Export file fisico',
    'robots_export_desc' => 'Se usi CloudFlare, CDN o caching aggressivo, WordPress potrebbe non servire il robots.txt virtuale. Crea un file robots.txt reale nella root del sito.',
    'robots_export_btn' => '📁 Esporta robots.txt nella root',
    'robots_export_empty' => 'Nessun contenuto robots.txt da esportare. Salva prima un contenuto nel tab Robots.txt.',
    'robots_export_ok' => '✔ File robots.txt creato in <code>%s</code>',
    'robots_export_err' => '❌ Impossibile scrivere il file. Verifica i permessi di scrittura della root del sito.',

    /* NAP tab */
    'nap_title'    => '📍 NAP Footer',
    'nap_desc'     => 'HTML personalizzato per il footer NAP (Nome, Indirizzo, Telefono). Shortcode: <code>[saf_nap_html]</code>.',
    'nap_content_label' => 'HTML Footer NAP',
    'nap_preview'  => 'Anteprima live:',
    'nap_show_preview' => 'mostra anteprima',
    'nap_no_content' => 'nessun contenuto ancora',
    'nap_shortcode' => 'Shortcode da usare nel tema: <code>[saf_nap_html]</code>.',

    /* Avanzate tab */
    'adv_title'    => '⚙️ Impostazioni Avanzate',
    'adv_email'    => '📧 Email &mdash; Mittente',
    'adv_email_desc' => 'Imposta nome e indirizzo mittente per tutte le email WP. Per SMTP host/port/auth usa <strong>WP Mail SMTP</strong>.',
    'adv_from_name' => 'Nome mittente',
    'adv_from_name_ph' => 'Nome Sito',
    'adv_from_name_desc' => 'Default: nome organizzazione da Dati Sito, poi nome WordPress.',
    'adv_from_email' => 'Email mittente',
    'adv_from_email_ph' => 'noreply@dominio.it',
    'adv_from_email_desc' => 'Default: WordPress usa wordpress@dominio.it &mdash; spesso finisce nello spam.',
    'adv_comments' => '💬 Commenti',
    'adv_comments_label' => 'Disabilita commenti',
    'adv_comments_label_check' => 'Disabilita i commenti su tutto il sito',
    'adv_comments_desc' => 'Rimuove commenti, trackback, menu commenti, colonne e admin bar. Consigliato per siti che non usano commenti.',
    'adv_hsts'     => '🔒 HTTP Strict Transport Security (HSTS)',
    'adv_hsts_label' => 'Attiva HSTS',
    'adv_hsts_label_check' => 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload',
    'adv_hsts_warn' => '⚠️ Attivare SOLO con SSL stabile + Cloudflare Full (Strict). Il browser forzerà HTTPS per un anno.',
    'adv_guide_hide' => 'Guida Sito',
    'adv_guide_hide_check' => 'Nascondi il tab Progetto dalla Guida Sito',
    'adv_guide_hide_desc' => 'Nasconde il tab Progetto — utile dopo la consegna per non mostrare documenti interni ai clienti.',
    'adv_menu'     => '🗂 Pulizia Menu Admin',
    'adv_menu_desc' => 'Nasconde le voci selezionate dal menu admin per tutti i ruoli <strong>tranne Amministratori</strong>. Utile per semplificare il backend per i clienti.',
    'adv_menu_hide_for' => 'Nascondi "%s" per Editor, Autore, Collaboratore',
    'adv_custom_hide' => 'Slug aggiuntivi',
    'adv_custom_ph' => 'edit.php?post_type=projects',
    'adv_custom_desc' => 'Slug menu da nascondere (uno per riga). Utile per CPT di plugin.<br>Esempi: edit.php?post_type=projects &middot; edit.php?post_type=portfolio &middot; tools.php',

    /* SVG Upload */
    'adv_svg'      => 'Upload SVG',
    'adv_svg_label' => 'Abilita upload SVG',
    'adv_svg_check' => 'Abilita upload di file SVG nella Media Library',
    'adv_svg_warn'  => '⚠️ I file SVG possono contenere script. SAF rimuove automaticamente script, event handler (onclick, onload...) e javascript: href. Per sicurezza massima installa <code>enshrined/svg-sanitize</code>.',

    /* Menu item labels for admin cleanup */
    'menu_tools'    => 'Strumenti',
    'menu_comments' => 'Commenti',
    'menu_themes'   => 'Aspetto / Temi',
    'menu_plugins'  => 'Plugin',
    'menu_users'    => 'Utenti',
    'menu_settings' => 'Impostazioni',
    'menu_projects' => 'Progetti (Divi)',

    /* Child tab */
    'child_title'  => '🎨 Child Theme <code>amar-design</code>',
    'child_create_btn' => 'Crea child theme amar-design',
    'child_exists'  => 'Il child theme esiste in <code>%s</code>. Il tuo <code>screenshot.png</code> rimane intatto.',
    'child_css_warn' => '<strong>Attenzione:</strong> la riga <code>Template:</code> deve corrispondere al nome della cartella del tuo tema principale.',
    'child_save_css' => 'Salva style.css',
    'child_css_header' => 'Intestazione style.css',
    'child_css_header_desc' => 'Ogni campo ha una funzione specifica per WordPress. Il campo <strong>Template</strong> è obbligatorio.',
    'child_css_shortcuts' => 'Usa Personalizza &rarr; CSS aggiuntivo per regole rapide. Qui inserisci solo override strutturali del tema.',
    'child_save_divi'  => 'Salva impostazioni Divi',

    /* Credits tab */
    'credits_title' => '📝 Crediti',
    'credits_desc'  => 'Questi dati appaiono nel widget Dashboard, nel footer admin e in altre aree. Utile per lasciare il tuo marchio sui siti che sviluppi.',
    'credits_author' => '👤 Autore / Sviluppatore',
    'credits_author_name' => 'Nome autore',
    'credits_author_url' => 'Sito web autore',
    'credits_author_url_ph' => 'tuosito.it',
    'credits_client' => '🤝 Cliente',
    'credits_client_name' => 'Nome cliente',
    'credits_client_url' => 'Sito web cliente',
    'credits_client_url_ph' => 'situocliente.it',
    'credits_notes_title' => '📓 Note di sviluppo',
    'credits_notes' => 'Note',
    'credits_notes_desc' => 'Visibile solo nel backend Dati Sito &rarr; Crediti.',
    'credits_created' => 'Data creazione',
    'credits_created_ph' => 'es. Giugno 2026',
    'credits_save'   => 'Salva Crediti',

    /* Buttons */
    'btn_save_org'  => 'Salva Organizzazione',
    'btn_save_seo'  => 'Salva SEO & Immagini',
    'btn_save_sec'  => 'Salva Sicurezza',
    'btn_save_robots' => 'Salva Robots.txt',
    'btn_save_nap'  => 'Salva NAP Footer',
    'btn_save_adv'  => 'Salva Impostazioni Avanzate',
    'btn_cleanup'      => 'Pulisci tutte le opzioni SAF',
    'btn_cleanup_desc' => 'Elimina tutte le opzioni <code>saf_*</code> dal database. Utile per disinstallazione pulita. Dati non recuperabili.',
    'btn_save_tools' => 'Salva checklist strumenti',

    /* Dashboard */
    'dash_title'     => '🌐 Panoramica Sito',
    'dash_checklist' => '📋 Avanzamento checklist',
    'checklist_progress' => '%1$d/%2$d completate (%3$d%%)',
    'dash_btn_dati'  => 'Dati Sito',
    'dash_btn_guida' => 'Guida Sito',
    'dash_credits'   => 'Sviluppato da',
    'dash_for'       => 'per',
    'dash_organization' => 'Organizzazione',
    'dash_site'    => 'Sito',

    /* Admin bar */
    'adminbar_frontend' => 'Visualizza Front-End',
    'adminbar_frontend_title' => 'Apri il sito in una nuova scheda',

    'footer_dev_by'  => 'Sviluppato da',
    'url_placeholder_domain' => 'dominio.it',

    /* Login */
    'login_welcome_title' => 'Accedi',
    'login_welcome_desc' => 'Inserisci le tue credenziali per accedere.',

    /* Errors & feedback */
    'err_permission' => 'Permesso negato.',
    'err_saved'                => '✔ Salvato.',
    'err_too_many_attempts'    => 'Troppi tentativi di accesso. Attendi 15 minuti e riprova.',
    'err_invalid_credentials'  => 'Credenziali non valide. Riprova.',
    'err_cleaned'     => '✔ Tutte le opzioni SAF rimosse dal database.',
    'err_css_updated'  => '✔ style.css aggiornato.',
    'err_css_write_fail' => '❌ Impossibile scrivere %s. Verifica permessi.',
    'err_child_created' => '✔ Child theme <strong>amar-design</strong> creato.',
    'err_ajax_unauthorized' => 'Richiesta non autorizzata.',
    'child_activate_warn' => 'Ricordati di attivare il child theme da <strong>Aspetto → Temi</strong> dopo aver creato e configurato i parametri (Template, Nome, Autore) nella sezione sottostante.',
    'child_detected'     => 'SAF ha rilevato un child theme attivo e lo gestisce automaticamente. Puoi modificare style.css e visualizzare functions.php qui sotto.',
    'child_functions_title' => 'functions.php',
    'child_functions_desc'  => 'Solo lettura — usa un editor FTP o il plugin per modificarlo.',

    /* Plugin & Tools tab */
    'plugins_title' => 'Plugin e Strumenti',
    'plugins_desc'  => 'Rilevamento e checklist dei principali plugin e strumenti del sito.',

    /* Shortcode tab */
    'sc_social_share_title' => '📤 Condivisione Social',
    'sc_social_share_desc'  => 'Scegli quali pulsanti mostrare nello shortcode <code>[condividi_social]</code>. Instagram e TikTok copiano il link negli appunti (non hanno un URL di condivisione nativo).',
    'sc_shortcode_usage'    => 'Usa lo shortcode:',
    'sc_active_buttons'     => 'Pulsanti attivi',
    'sc_copy_note'          => '(copia link)',
    'sc_copy_label'         => 'Copia link',
    'sc_default_note'       => 'Se nessuna spunta è salvata, tutti i pulsanti sono visibili (comportamento predefinito).',


    /* Shortcode — Developer section */
    'sc_social_section' => 'Social',
    'sc_dev_section'    => 'Developer',
    'sc_dev_desc'       => 'Profili sviluppatore. Si attivano con <code>[condividi_social type="dev"]</code>.',
    'sc_dev_profiles'   => 'Profili Dev',
    'sc_dev_url_note'   => 'Inserisci URL completo del profilo (es. https://github.com/tuo-username).',
);
