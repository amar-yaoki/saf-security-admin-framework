<?php
/**
 * guida.php — Pagina 📖 Guida Sito.
 *
 * Sezione 60 — Registrazione pagina
 * Sezione 61 — Salvataggio checklist e note
 * Sezione 62 — Render con tab:
 *              Tab 0: Info (crediti professionali, licenza, disclaimer, guide)
 *              Tab 1: Struttura (mappa file, architettura)
 *              Tab 2: Shortcode Reference
 *              Tab 3: Sicurezza (misure attive)
 *              Tab 4: Checklist Pre-Go-Live
 *              Tab 5: Documento di Progetto (multi-PDF con date)*
 *              Tab 6: Note Sviluppatore
 *
 * * Il tab Progetto può essere nascosto da ⚙️ Dati Sito → Avanzate.
 */

defined( 'ABSPATH' ) || exit;


/* ============================================================
   SEZIONE 60 — REGISTRAZIONE PAGINA GUIDA TEMA
   ============================================================ */

add_action( 'admin_menu', 'saf_register_guida_page' );
function saf_register_guida_page() {
    add_menu_page(
        'Guida Sito',
        '📖 Guida Sito',
        'manage_options',
        'saf-guida',
        'saf_render_guida',
        'dashicons-book-alt',
        4
    );
}
// Rimuove il sottomenu automatico duplicato.
add_action( 'admin_menu', function() {
    remove_submenu_page( 'saf-guida', 'saf-guida' );
}, 99 );


/* ============================================================
   SEZIONE 61 — SALVATAGGIO CHECKLIST E NOTE
   ============================================================ */

add_action( 'admin_init', 'saf_register_guida_settings' );
function saf_register_guida_settings() {
    register_setting( 'saf_guida_group', 'saf_checklist',    'saf_sanitize_checklist' );
    register_setting( 'saf_guida_group', 'saf_dev_notes',    'sanitize_textarea_field' );
    register_setting( 'saf_guida_group', 'saf_project_docs', 'saf_sanitize_project_docs' );
}

function saf_sanitize_checklist( $input ) {
    $out = array();
    if ( is_array( $input ) ) {
        foreach ( $input as $key => $val ) {
            $out[ sanitize_key( $key ) ] = (bool) $val;
        }
    }
    return $out;
}

function saf_sanitize_project_docs( $input ) {
    $docs = array();
    if ( ! empty( $input['docs'] ) && is_array( $input['docs'] ) ) {
        foreach ( $input['docs'] as $doc ) {
            $url   = esc_url_raw( $doc['url']   ?? '' );
            $title = sanitize_text_field( $doc['title'] ?? '' );
            $date  = sanitize_text_field( $doc['date']  ?? '' );
            $notes = sanitize_textarea_field( $doc['notes'] ?? '' );
            if ( $url ) {
                $docs[] = compact( 'url', 'title', 'date', 'notes' );
            }
        }
    }
    return array( 'docs' => $docs );
}


/* ============================================================
   SEZIONE 62 — RENDER PAGINA GUIDA TEMA
   ============================================================ */

function saf_render_guida() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $tab      = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'info';
    $checks   = (array) get_option( 'saf_checklist',    array() );
    $notes    = (string) get_option( 'saf_dev_notes',   '' );
    $proj     = (array) get_option( 'saf_project_docs', array() );
    $docs     = $proj['docs'] ?? array();
    $credits  = (array) get_option( 'saf_credits_settings', array() );

    $tabs = array(
        'info'      => 'ℹ️ Info',
        'struttura' => '🗂 Struttura',
        'shortcode' => '📋 Shortcode',
        'sicurezza' => '🔒 Sicurezza',
        'checklist' => '✅ Checklist',
        'progetto'  => '📄 Progetto',
        'note'      => '🗒 Note Dev',
    );
    $adv = (array) get_option( 'saf_adv_settings', array() );
    if ( ! empty( $adv['hide_progetto'] ) ) {
        unset( $tabs['progetto'] );
    }

    $tab_url = function( $slug ) {
        return add_query_arg( array( 'page' => 'saf-guida', 'tab' => $slug ), admin_url( 'admin.php' ) );
    };
    ?>
    <div class="wrap saf-admin-wrap saf-guida-wrap">
        <h1>📖 Guida Sito — Amar SAF</h1>

        <nav class="nav-tab-wrapper">
        <?php foreach ( $tabs as $slug => $label ) :
            $class = $tab === $slug ? 'nav-tab nav-tab-active' : 'nav-tab';
            echo '<a href="' . esc_url( $tab_url( $slug ) ) . '" class="' . $class . '">' . esc_html( $label ) . '</a>';
        endforeach; ?>
        </nav>

        <?php

        /* ---- TAB 0: INFO ---- */
        if ( $tab === 'info' ) : ?>
        <div class="saf-guida-section">
            <div style="text-align:center;padding:24px 0;border-bottom:2px solid #2ea3f2;margin-bottom:32px">
                <h2 style="font-size:28px;margin:0 0 8px">Amar SAF</h2>
                <p style="font-size:16px;color:#555;margin:0 0 4px">Security &amp; Admin Framework</p>
                <p style="font-size:13px;color:#888;margin:0"><?php echo saf_t( 'page_title' ); ?> v<?php echo SAF_VERSION; ?></p>
            </div>

            <table class="widefat saf-table-monospace" style="margin-bottom:24px;max-width:480px">
                <tr><td style="width:140px;font-weight:600"><?php echo saf_t( 'credits_author' ); ?></td><td>Amar Amoretti</td></tr>
                <tr><td style="font-weight:600">Sito Web</td><td><a href="https://yaoki.academy" target="_blank">yaoki.academy</a></td></tr>
                <tr><td style="font-weight:600">Licenza</td><td>GNU General Public License v2 or later</td></tr>
                <tr><td style="font-weight:600">Versione</td><td><?php echo SAF_VERSION; ?></td></tr>
                <tr><td style="font-weight:600">PHP Min</td><td>8.2+</td></tr>
                <tr><td style="font-weight:600">WP Min</td><td>7.0+</td></tr>
            </table>

            <div style="background:#f0f8ff;border-left:4px solid #2ea3f2;padding:16px 20px;margin-bottom:24px;border-radius:4px">
                <h3 style="margin:0 0 8px;color:#2ea3f2">🎯 Perché Amar SAF?</h3>
                <p style="margin:6px 0"><strong>Amar SAF</strong> nasce dall'esperienza sul campo: ogni sito WordPress, indipendentemente dal tema, ha bisogno di un insieme di funzionalità che i temi (anche i più blasonati) non offrono out-of-the-box.</p>
                <p style="margin:6px 0"><strong>Cosa fa che gli altri non fanno?</strong></p>
                <ul style="margin:4px 0 0;padding-left:20px">
                    <li><strong>Modulare:</strong> attiva solo quello che ti serve. Nessun page builder incluso — funziona con qualsiasi tema.</li>
                    <li><strong>Zero jQuery:</strong> tutto vanilla JS — performance e compatibilità garantite.</li>
                    <li><strong>Backend completo:</strong> Dati Sito (8 tab) + Guida Sito (7 tab) — tutto in un unico posto, niente settings sparsi.</li>
                    <li><strong>Safe per il cliente:</strong> puoi nascondere voci di menu, disabilitare commenti, proteggere l'admin. Il cliente non si rompe niente.</li>
                    <li><strong>Child Theme integrato:</strong> SAF include un child theme <code>amar-design</code> preconfigurato — crealo in un clic e personalizza senza paura di perdere tutto agli aggiornamenti del tema.</li>
                    <li><strong>Checklist pre-go-live:</strong> segui i passaggi essenziali (SSL, privacy, SEO, backup) prima di consegnare un sito.</li>
                    <li><strong>Brandizzato:</strong> credits sviluppatore in dashboard e footer — lascia il tuo marchio.</li>
                </ul>
                <p style="margin:8px 0 0"><strong>E per la SEO?</strong> SAF non compete con Rank Math o Yoast. Anzi: è un <em>complemento</em>. Se Rank Math è presente, SAF lascia fare a lui e attiva solo i fallback (OG image, canonical) quando mancano. Insieme danno il meglio: Rank Math gestisce blog e pagine, SAF gestisce i dati strutturati di base e le immagini di fallback.</p>
            </div>

            <div style="background:#fff8e1;border-left:4px solid #f5a623;padding:16px 20px;margin-bottom:24px;border-radius:4px">
                <h3 style="margin:0 0 8px;color:#b8860b">🔒 Proteggi le tue modifiche — usa il Child Theme</h3>
                <p style="margin:6px 0"><strong>Perché un child theme?</strong> Se modifichi direttamente i file del tema (Divi, Astra, Twenty Twenty-Four…), le tue personalizzazioni <strong>vengono sovrascritte a ogni aggiornamento</strong> del tema. Un child theme eredita tutto dal tema principale ma mantiene le tue modifiche separata, al sicuro.</p>
                <p style="margin:6px 0"><strong>Cosa puoi fare con un child theme:</strong></p>
                <ul style="margin:4px 0 0;padding-left:20px">
                    <li>Sovrascrivere template PHP (header, footer, single, page) senza toccare il tema originale</li>
                    <li>Aggiungere CSS e JS personalizzati in <code>style.css</code>, <code>css/</code>, <code>js/</code></li>
                    <li>Aggiungere funzioni personalizzate in <code>functions.php</code> o moduli <code>inc/</code></li>
                    <li>Aggiornare il tema principale senza perdere nulla</li>
                </ul>
                <p style="margin:8px 0 0">SAF include un child theme preconfigurato (<code>amar-design</code>) con cartelle <code>inc/</code>, <code>css/</code>, <code>js/</code> e compatibilità Divi opzionale. <a href="<?php echo esc_url( admin_url( 'admin.php?page=saf-dati-sito&tab=child' ) ); ?>">Crealo ora in un clic →</a></p>
            </div>

            <div style="background:#fff8e1;border-left:4px solid #f5a623;padding:16px 20px;margin-bottom:24px;border-radius:4px">
                <strong>Disclaimer:</strong> Questo software viene fornito "così com'è", senza garanzia di alcun tipo, esplicita o implicita. L'autore non sarà responsabile per danni diretti o indiretti derivanti dall'uso del software. Utilizzare a proprio rischio.
            </div>

            <div style="background:#f0f8ff;border-left:4px solid #2ea3f2;padding:16px 20px;margin-bottom:24px;border-radius:4px">
                <strong>Copyright &copy; <?php echo date('Y'); ?> Amar Amoretti.</strong> Tutti i diritti riservati.<br>
                Questo programma è software libero: puoi ridistribuirlo e/o modificarlo secondo i termini della GNU General Public License pubblicata dalla Free Software Foundation, versione 2 o successive.
            </div>

            <h3>📖 Guida Completa</h3>
            <p>Per una spiegazione dettagliata di ogni modulo, tab, opzione e funzionalità, consulta la guida interattiva:</p>
            <p>
                <a href="<?php echo esc_url( SAF_URL . 'guide-IT.html' ); ?>" target="_blank" class="button button-primary" style="margin-right:8px">🇮🇹 Guida in Italiano</a>
                <a href="<?php echo esc_url( SAF_URL . 'guide-EN.html' ); ?>" target="_blank" class="button">🇬🇧 Guide in English</a>
            </p>
            <p class="description">Le guide si aprono in una nuova scheda. Contengono la documentazione completa di tutti i moduli SAF.</p>
        </div>

        <?php /* ---- TAB 1: STRUTTURA ---- */ elseif ( $tab === 'struttura' ) : ?>
        <div class="saf-guida-section">
            <h2>🗂 Struttura del Child Theme</h2>
            <p>Il child theme è organizzato in moduli indipendenti. Ogni file <code>/inc/*.php</code> ha un dominio preciso e può essere aggiornato senza toccare gli altri.</p>

            <h3>Mappa file</h3>
            <table class="widefat saf-table-monospace">
                <thead><tr><th>File</th><th>Descrizione</th><th>Sezioni</th></tr></thead>
                <tbody>
                    <tr><td><code>functions.php</code></td><td>Entry point — carica tutti i moduli /inc/ nell'ordine corretto</td><td>—</td></tr>
                    <tr><td><code>style.css</code></td><td>Header child theme + override CSS Divi globali</td><td>—</td></tr>
                    <tr class="saf-tr-module"><td><code>inc/theme-setup.php</code></td><td>Enqueue assets, theme support, immagini, cleanup head, emoji, Gutenberg CSS, defer script, timezone</td><td>1–10</td></tr>
                    <tr class="saf-tr-module"><td><code>inc/security.php</code></td><td>Brand login, rate limiting, IP helper, enumerazione utenti, XML-RPC, headers HTTP, file editor, errori login, nonce AJAX, honeypot, login CSS</td><td>11–22</td></tr>
                    <tr class="saf-tr-module"><td><code>inc/performance.php</code></td><td>Dequeue asset inutili, oEmbed, heartbeat, revisioni, lazy load, CF7 ottimizzato</td><td>25–30</td></tr>
                    <tr class="saf-tr-module"><td><code>inc/helpers.php</code></td><td>YouTube ID/thumbnail, truncate, data italiana, social sharing, paginazione standard, paginazione Netflix, reading time, footer info, NAP HTML</td><td>31–39</td></tr>
                    <tr class="saf-tr-module"><td><code>inc/seo.php</code></td><td>JSON-LD Organization, JSON-LD WebSite+SearchAction, canonical, Open Graph, Twitter Card, breadcrumb, helper output JSON-LD</td><td>40–45</td></tr>
                    <tr class="saf-tr-module"><td><code>inc/admin.php</code></td><td>Pagina ⚙️ Dati Sito (Org, SEO, Sicurezza, Robots, NAP, Avanzate), saf_get_org_data(), SMTP, pulizia dashboard</td><td>50–57</td></tr>
                    <tr class="saf-tr-module"><td><code>inc/cleanup.php</code></td><td>Disabilitazione commenti completa, pulizia menu admin per ruoli, admin bar, colonne, footer admin</td><td>70–74</td></tr>
                    <tr class="saf-tr-module"><td><code>inc/dashboard.php</code></td><td>Widget dashboard panoramica, pulsante "Visualizza Front-End" azzurro, CSS admin</td><td>75–77</td></tr>
                    <tr class="saf-tr-module"><td><code>inc/guida.php</code></td><td>Questa pagina — Guida Sito con tab interattivi</td><td>60–62</td></tr>
                    <tr class="saf-tr-module"><td><code>inc/header-search.php</code></td><td>Overlay ricerca AJAX fullscreen con pill (Tutto/Blog/Pagine), shortcut Ctrl+K</td><td>—</td></tr>
                    <tr><td><code>css/login.css</code></td><td>Stile pagina login brandizzata — variabili CSS per personalizzazione rapida</td><td>—</td></tr>
                    <tr><td><code>js/main.js</code></td><td>JS frontend: paginazione Netflix AJAX, copia link, smooth scroll, header scrolled</td><td>—</td></tr>
                    <tr><td><code>js/admin.js</code></td><td>JS backend: media picker universale, checklist save, documento progetto (add/remove/reindex)</td><td>—</td></tr>
                    <tr><td><code>robots.txt</code></td><td>Template ottimizzato (da copiare nella root del sito — non nella cartella tema)</td><td>—</td></tr>
                </tbody>
            </table>

            <h3>Ordine di caricamento (functions.php)</h3>
            <pre class="saf-pre">theme-setup → security → performance → helpers → seo → admin → cleanup → dashboard → guida</pre>
            <p class="description">L'ordine è obbligatorio: <code>security</code> deve girare prima di qualsiasi output, <code>helpers</code> prima di <code>seo</code> (che usa <code>saf_truncate</code>), <code>admin</code> prima di <code>security</code> (che legge <code>saf_get_org_data</code>).</p>

            <h3>Stack richiesto</h3>
            <table class="widefat">
                <tbody>
                    <tr><td><strong>WordPress</strong></td><td>7.0+</td></tr>
                    <tr><td><strong>Divi</strong></td><td>5.x (parent theme)</td></tr>
                    <tr><td><strong>PHP</strong></td><td>8.2+</td></tr>
                    <tr><td><strong>Plugin SEO consigliato</strong></td><td>Rank Math (gestisce blog e pagine; i fallback OG/canonical di questo tema si attivano solo se assente)</td></tr>
                </tbody>
            </table>

            <h3>Aggiungere un nuovo modulo (es. CPT, Event Manager…)</h3>
            <ol>
                <li>Crea <code>inc/nome-modulo.php</code> con <code>defined('ABSPATH') || exit;</code> in testa</li>
                <li>Aggiungi <code>require_once $inc . 'nome-modulo.php';</code> in <code>functions.php</code></li>
                <li>Usa le sezioni numeriche progressive (es. 70, 71…) per documentare le sezioni interne</li>
                <li>Aggiungi la riga alla tabella "Mappa file" in questa guida</li>
            </ol>
        </div>

        <?php /* ---- TAB 2: SHORTCODE ---- */ elseif ( $tab === 'shortcode' ) : ?>
        <div class="saf-guida-section">
            <h2>📋 Shortcode Reference</h2>
            <p>Clicca su uno shortcode per copiarlo negli appunti.</p>

            <?php
            $shortcodes = array(
                array(
                    'tag'    => '[saf_breadcrumb]',
                    'desc'   => 'Breadcrumb semantico con JSON-LD BreadcrumbList. Funziona su qualsiasi tipo di pagina.',
                    'params' => array(
                        'sep="›"'        => 'Separatore tra i livelli (default: ›)',
                        'home_label="Home"' => 'Label del primo elemento (default: Home)',
                    ),
                    'esempi' => array(
                        '[saf_breadcrumb]',
                        '[saf_breadcrumb sep="/" home_label="Inizio"]',
                    ),
                ),
                array(
                    'tag'    => '[condividi_social]',
                    'desc'   => 'Pulsanti social sharing: Facebook, WhatsApp, Telegram, Email, Copia link. Usa URL e titolo del post corrente.',
                    'params' => array(),
                    'esempi' => array( '[condividi_social]' ),
                ),
                array(
                    'tag'    => '[saf_reading_time]',
                    'desc'   => 'Tempo di lettura calcolato automaticamente dal word count del post.',
                    'params' => array(
                        'wpm="200"'     => 'Parole per minuto (default: 200)',
                        'label="Lettura: "' => 'Testo prima del numero',
                        'suffix=" min"' => 'Testo dopo il numero',
                    ),
                    'esempi' => array(
                        '[saf_reading_time]',
                        '[saf_reading_time wpm="150" label="Tempo: " suffix=" minuti"]',
                    ),
                ),
                array(
                    'tag'    => '[saf_footer_info]',
                    'desc'   => 'Mostra i dati aziendali da ⚙️ Dati Sito → Organizzazione. Configurabile per scegliere i campi da visualizzare.',
                    'params' => array(
                        'mostra="nome,piva,email"' => 'Campi da mostrare: nome | piva | email | phone | address',
                    ),
                    'esempi' => array(
                        '[saf_footer_info]',
                        '[saf_footer_info mostra="nome,piva,address,email,phone"]',
                        '[saf_footer_info mostra="nome,piva"]',
                    ),
                ),
                array(
                    'tag'    => '[saf_nap_html]',
                    'desc'   => 'Stampa i dati NAP (Nome, Indirizzo, Telefono) in HTML strutturato. Configurabile da ⚙️ Dati Sito → NAP Footer.',
                    'params' => array(
                        'class=""'    => 'Classe CSS aggiuntiva per il wrapper',
                    ),
                    'esempi' => array(
                        '[saf_nap_html]',
                        '[saf_nap_html class="footer-nap"]',
                    ),
                ),
            );

            foreach ( $shortcodes as $sc ) : ?>
            <div class="saf-sc-card">
                <div class="saf-sc-header">
                    <code class="saf-sc-tag saf-copyable" title="Clicca per copiare"><?php echo esc_html( $sc['tag'] ); ?></code>
                    <span class="saf-sc-copy-hint">📋 clicca per copiare</span>
                </div>
                <p class="saf-sc-desc"><?php echo esc_html( $sc['desc'] ); ?></p>

                <?php if ( ! empty( $sc['params'] ) ) : ?>
                <table class="widefat saf-sc-params">
                    <thead><tr><th>Parametro</th><th>Descrizione</th></tr></thead>
                    <tbody>
                    <?php foreach ( $sc['params'] as $param => $desc ) : ?>
                        <tr>
                            <td><code><?php echo esc_html( $param ); ?></code></td>
                            <td><?php echo esc_html( $desc ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

                <?php if ( ! empty( $sc['esempi'] ) ) : ?>
                <div class="saf-sc-esempi">
                    <strong>Esempi:</strong>
                    <?php foreach ( $sc['esempi'] as $ex ) : ?>
                        <code class="saf-copyable" title="Clicca per copiare"><?php echo esc_html( $ex ); ?></code>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <h3>Funzioni PHP riutilizzabili (inc/helpers.php)</h3>
            <table class="widefat saf-table-monospace">
                <thead><tr><th>Funzione</th><th>Descrizione</th></tr></thead>
                <tbody>
                    <tr><td><code>saf_youtube_id( $url )</code></td><td>Estrae l'ID da un URL YouTube (qualsiasi formato)</td></tr>
                    <tr><td><code>saf_youtube_thumbnail( $url, $quality )</code></td><td>URL thumbnail YouTube — quality: maxresdefault | hqdefault | mqdefault</td></tr>
                    <tr><td><code>saf_truncate( $text, $limit, $suffix )</code></td><td>Tronca a N parole con suffisso personalizzabile</td></tr>
                    <tr><td><code>saf_format_date( $date, $long, $with_year )</code></td><td>Data in italiano — "3 Luglio 2026" o "3 lug 2026"</td></tr>
                    <tr><td><code>saf_paginazione( $query, $echo )</code></td><td>Paginazione WP numerata standard</td></tr>
                    <tr><td><code>saf_paginazione_netflix( $total, $current, $action, $data )</code></td><td>Bottone "Carica altri" per paginazione AJAX</td></tr>
                    <tr><td><code>saf_honeypot_field()</code></td><td>Renderizza campo honeypot in un form</td></tr>
                    <tr><td><code>saf_is_spam()</code></td><td>Verifica honeypot (true = spam)</td></tr>
                    <tr><td><code>saf_verify_ajax_nonce( $action )</code></td><td>Verifica nonce in handler AJAX — risponde 403 e termina se invalido</td></tr>
                    <tr><td><code>saf_get_client_ip()</code></td><td>IP reale del client (Cloudflare-aware)</td></tr>
                    <tr><td><code>saf_get_org_data()</code></td><td>Array con tutti i dati organizzazione da ⚙️ Dati Sito</td></tr>
                    <tr><td><code>saf_output_json_ld( $schema )</code></td><td>Stampa un blocco JSON-LD nel head</td></tr>
                </tbody>
            </table>
        </div>

        <?php /* ---- TAB 3: SICUREZZA ---- */ elseif ( $tab === 'sicurezza' ) : ?>
        <div class="saf-guida-section">
            <h2>🔒 Sicurezza — Dettaglio Misure Attive</h2>

            <?php
            $measures = array(
                array(
                    'title'   => 'Pagina login brandizzata',
                    'file'    => 'inc/security.php — Sezione 22',
                    'status'  => 'attiva',
                    'detail'  => 'La pagina <code>/wp-login.php</code> è completamente brandizzata con tema scuro Amar Design (#121212 sfondo, #f47D39 accenti). Il logo viene caricato da ⚙️ Dati Sito → Organizzazione. Nessuna traccia WordPress visibile. Il link "Torna al sito" è nascosto via CSS.',
                    'config'  => 'Logo: ⚙️ Dati Sito → Tab Organizzazione → Logo Organizzazione',
                ),
                array(
                    'title'   => 'Rate limiting login',
                    'file'    => 'inc/security.php — Sezioni 13–14',
                    'status'  => 'attiva',
                    'detail'  => 'Massimo N tentativi di login per IP in 15 minuti (default: 5). Il contatore viene azzerato al login riuscito. Gestisce IP reali dietro proxy e Cloudflare tramite <code>HTTP_CF_CONNECTING_IP</code>.',
                    'config'  => '⚙️ Dati Sito → Tab Sicurezza → "Max tentativi login" (range 3–20)',
                ),
                array(
                    'title'   => 'Blocco enumerazione utenti',
                    'file'    => 'inc/security.php — Sezione 15',
                    'status'  => 'attiva',
                    'detail'  => '<code>?author=N</code> → redirect 301 homepage. REST API <code>/wp/v2/users</code> e <code>/wp/v2/users/{id}</code> → 403 per utenti non autenticati.',
                    'config'  => 'Nessuna — sempre attiva',
                ),
                array(
                    'title'   => 'XML-RPC disabilitato',
                    'file'    => 'inc/security.php — Sezione 16',
                    'status'  => 'attiva',
                    'detail'  => 'Principale vettore di attacchi brute-force. Disabilitato completamente: hook <code>xmlrpc_enabled</code>, svuotamento <code>xmlrpc_methods</code>, rimozione header <code>X-Pingback</code> e link discovery.',
                    'config'  => 'Nessuna — sempre attiva',
                ),
                array(
                    'title'   => 'Headers HTTP sicurezza',
                    'file'    => 'inc/security.php — Sezione 18',
                    'status'  => 'attiva',
                    'detail'  => 'Inviati su ogni risposta: <code>X-Frame-Options: SAMEORIGIN</code> (anti-clickjacking), <code>X-Content-Type-Options: nosniff</code>, <code>X-XSS-Protection</code>, <code>Referrer-Policy: strict-origin-when-cross-origin</code>, <code>Permissions-Policy</code> (camera, mic, geolocation, payment disabilitati). <br><strong>HSTS commentato</strong>: attivare solo con SSL stabile + Cloudflare Full Strict.',
                    'config'  => 'HSTS: decommentare riga in security.php sezione 18',
                ),
                array(
                    'title'   => 'Versione WP nascosta',
                    'file'    => 'inc/security.php — Sezione 17 + inc/theme-setup.php — Sezione 10',
                    'status'  => 'attiva',
                    'detail'  => 'Rimosso <code>wp_generator</code> dal head. Rimosso parametro <code>?ver=X.X.X</code> dagli URL di script e stili (doppia sicurezza in due sezioni).',
                    'config'  => 'Nessuna — sempre attiva',
                ),
                array(
                    'title'   => 'Messaggi errore login generici',
                    'file'    => 'inc/security.php — Sezione 20',
                    'status'  => 'attiva',
                    'detail'  => 'Il messaggio di errore è sempre "Credenziali non valide." — non rivela se è sbagliato l\'username o la password, rendendo più difficile il credential stuffing.',
                    'config'  => 'Nessuna — sempre attiva',
                ),
                array(
                    'title'   => 'File editor disabilitato',
                    'file'    => 'inc/security.php — Sezione 19',
                    'status'  => 'attiva',
                    'detail'  => '<code>DISALLOW_FILE_EDIT = true</code>: impedisce la modifica di plugin e temi dall\'interfaccia admin WP. <code>DISALLOW_FILE_MODS</code> è false — se impostato true blocca anche gli aggiornamenti automatici.',
                    'config'  => 'security.php sezione 19 — cambiare DISALLOW_FILE_MODS se necessario',
                ),
                array(
                    'title'   => 'Nonce AJAX centralizzato',
                    'file'    => 'inc/security.php — Sezione 21',
                    'status'  => 'attiva',
                    'detail'  => 'Funzione <code>saf_verify_ajax_nonce()</code> da chiamare all\'inizio di ogni handler <code>wp_ajax_*</code>. Il nonce è iniettato nel frontend via <code>wp_localize_script</code> come <code>saf_ajax.nonce</code>.',
                    'config'  => 'Usare saf_verify_ajax_nonce() in ogni handler AJAX custom',
                ),
                array(
                    'title'   => 'Honeypot anti-spam',
                    'file'    => 'inc/security.php — Sezione 22',
                    'status'  => 'disponibile',
                    'detail'  => 'Funzioni <code>saf_honeypot_field()</code> (renderizza campo nascosto) e <code>saf_is_spam()</code> (verifica, true = spam). Da aggiungere manualmente ai form custom del progetto.',
                    'config'  => 'Aggiungere saf_honeypot_field() prima del bottone submit nei form',
                ),
            );

            foreach ( $measures as $m ) :
                $badge = $m['status'] === 'attiva'
                    ? '<span class="saf-badge saf-badge--green">✅ Attiva</span>'
                    : '<span class="saf-badge saf-badge--blue">ℹ️ Disponibile</span>';
            ?>
            <div class="saf-measure-card">
                <div class="saf-measure-header">
                    <strong><?php echo esc_html( $m['title'] ); ?></strong>
                    <?php echo $badge; ?>
                    <code class="saf-measure-file"><?php echo esc_html( $m['file'] ); ?></code>
                </div>
                <p class="saf-measure-detail"><?php echo wp_kses_post( $m['detail'] ); ?></p>
                <?php if ( ! empty( $m['config'] ) ) : ?>
                    <p class="saf-measure-config">⚙️ <em><?php echo esc_html( $m['config'] ); ?></em></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php /* ---- TAB 4: CHECKLIST ---- */ elseif ( $tab === 'checklist' ) : ?>
        <div class="saf-guida-section">
            <h2>✅ Checklist Pre-Go-Live</h2>
            <p class="description">Spunta le voci completate — lo stato viene salvato nel database per questo sito.</p>
            <form method="post" action="options.php">
                <?php settings_fields( 'saf_guida_group' ); ?>
                <?php
                $checklist_items = array(
                    'backend' => array(
                        'label' => '⚙️ Backend — Da completare',
                        'items' => array(
                            'org_name'       => 'Dati Sito → Organizzazione: compilare nome, URL, logo',
                            'org_address'    => 'Dati Sito → Organizzazione: compilare indirizzo, CAP, città',
                            'org_social'     => 'Dati Sito → Organizzazione: inserire URL social network',
                            'seo_og'         => 'Dati Sito → SEO: caricare OG Image Default (1200×630px)',
                            'sec_login'      => 'Testare login brandizzato su /wp-login.php (logo, colori, nessuna traccia WP)',
                            'rankmath'       => 'Rank Math: configurazione completa (sitemap, GSC, permalink categorie)',
                        ),
                    ),
                    'sicurezza' => array(
                        'label' => '🔒 Sicurezza — Da verificare',
                        'items' => array(
                            'check_login'    => 'Verificare pagina login brandizzata (logo, colori, nessuna traccia WP)',
                            'check_author'   => 'Testare che ?author=1 reindirizzi alla homepage',
                            'check_headers'  => 'Verificare headers HTTP su securityheaders.com',
                            'check_xmlrpc'   => 'Verificare che xmlrpc.php risponda 403 o non sia raggiungibile',
                            'hsts'           => 'Valutare HSTS (solo con SSL stabile + Cloudflare Full Strict)',
                            'robots_url'     => 'Aggiornare robots.txt con URL reale del sito',
                            'robots_sitemap' => 'Aggiornare URL sitemap in robots.txt',
                        ),
                    ),
                    'seo' => array(
                        'label' => '🔍 SEO — Da testare',
                        'items' => array(
                            'jld_org'        => 'Testare JSON-LD Organization con Rich Results Test',
                            'jld_website'    => 'Testare JSON-LD WebSite + SearchAction',
                            'breadcrumb'     => 'Verificare [saf_breadcrumb] nei template singolo (Divi Theme Builder)',
                            'og_tags'        => 'Testare OG tags con Facebook Debugger',
                            'canonical'      => 'Verificare canonical corretto su pagine filtrate',
                            'sitemap'        => 'Inviare sitemap a Google Search Console',
                        ),
                    ),
                    'performance' => array(
                        'label' => '⚡ Performance — Da verificare',
                        'items' => array(
                            'pagespeed'      => 'Testare con PageSpeed Insights (target: 90+ mobile)',
                            'gutenberg_css'  => 'Verificare assenza CSS Gutenberg (DevTools → Network → wp-block-library)',
                            'defer'          => 'Verificare defer su script non critici',
                            'images'         => 'Verificare immagini ottimizzate (WebP dove possibile)',
                        ),
                    ),
                    'gdpr' => array(
                        'label' => '🔐 GDPR & Legal',
                        'items' => array(
                            'cookie_banner'  => 'Cookie banner attivo e funzionante (iubenda o equivalente)',
                            'privacy_policy' => 'Privacy Policy — URL funzionante',
                            'terms'          => 'Termini & Condizioni — URL funzionante',
                            'antispam'       => 'Form contatto con anti-spam attivo (CleanTalk, honeypot o reCAPTCHA)',
                        ),
                    ),
                    'finale' => array(
                        'label' => '🚀 Finale',
                        'items' => array(
                            'staging_test'   => 'Test completo su staging prima di andare live',
                            'backup'         => 'Backup completo (file + DB) eseguito prima del go-live',
                            'analytics'      => 'Google Analytics / GA4 configurato',
                            'search_console' => 'Google Search Console verificata',
                            'uptime'         => 'Monitoraggio uptime configurato (UptimeRobot o equivalente)',
                        ),
                    ),
                );

                $total_items = 0;
                $done_items  = 0;
                foreach ( $checklist_items as $group ) {
                    foreach ( $group['items'] as $key => $label ) {
                        $total_items++;
                        if ( ! empty( $checks[ $key ] ) ) $done_items++;
                    }
                }
                $pct = $total_items > 0 ? round( $done_items / $total_items * 100 ) : 0;
                ?>

                <div class="saf-progress-bar-wrap">
                    <div class="saf-progress-bar" style="width:<?php echo $pct; ?>%"></div>
                    <span class="saf-progress-label"><?php printf( saf_t( 'checklist_progress' ), $done_items, $total_items, $pct ); ?></span>
                </div>

                <?php foreach ( $checklist_items as $group_key => $group ) : ?>
                <h3><?php echo esc_html( $group['label'] ); ?></h3>
                <ul class="saf-checklist">
                    <?php foreach ( $group['items'] as $key => $label ) :
                        $checked = ! empty( $checks[ $key ] );
                    ?>
                    <li class="saf-checklist__item<?php echo $checked ? ' saf-checklist__item--done' : ''; ?>">
                        <label>
                            <input type="checkbox" name="saf_checklist[<?php echo esc_attr( $key ); ?>]"
                                   value="1" <?php checked( $checked ); ?>>
                            <?php echo esc_html( $label ); ?>
                        </label>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endforeach; ?>

                <?php submit_button( 'Salva Checklist' ); ?>
            </form>
        </div>

        <?php /* ---- TAB 5: DOCUMENTO PROGETTO ---- */ elseif ( $tab === 'progetto' ) : ?>
        <div class="saf-guida-section">
            <h2>📄 Documento di Progetto</h2>
            <p class="description">
                Carica qui i documenti di sintesi del progetto (PDF). Ogni documento ha titolo, data e note.<br>
                Usato per tenere traccia delle versioni e delle sessioni di sviluppo.
            </p>
            <form method="post" action="options.php">
                <?php settings_fields( 'saf_guida_group' ); ?>

                <div id="saf-docs-list">
                <?php
                if ( empty( $docs ) ) {
                    saf_guida_doc_row( 0, array() );
                } else {
                    foreach ( $docs as $i => $doc ) {
                        saf_guida_doc_row( $i, $doc );
                    }
                }
                ?>
                </div>

                <button type="button" id="saf-add-doc" class="button button-secondary" style="margin:12px 0">
                    + Aggiungi documento
                </button>

                <script type="text/template" id="saf-doc-tpl">
                    <?php saf_guida_doc_row( '__IDX__', array(), true ); ?>
                </script>

                <?php submit_button( 'Salva Documenti' ); ?>
            </form>
        </div>

        <?php /* ---- TAB 6: NOTE SVILUPPATORE ---- */ elseif ( $tab === 'note' ) : ?>
        <div class="saf-guida-section">
            <h2>🗒 Note Sviluppatore</h2>
            <p class="description">
                Campo libero per appunti di sessione, decisioni tecniche, cose da ricordare.<br>
                Visibile solo agli Amministratori. Salvato nel database del sito.
            </p>
            <form method="post" action="options.php">
                <?php settings_fields( 'saf_guida_group' ); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="saf_dev_notes">Appunti</label></th>
                        <td>
                            <textarea id="saf_dev_notes" name="saf_dev_notes"
                                      rows="20" style="width:100%;font-family:monospace;font-size:13px"
                            ><?php echo esc_textarea( $notes ); ?></textarea>
                            <p class="description">
                                Formato libero. Puoi usare Markdown, testo semplice o qualsiasi convenzione tu preferisca.<br>
                                Suggerimento: inizia ogni sessione con una data e un titolo, es: <code>## 27 Maggio 2026 — Integrazione Event Manager</code>
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button( 'Salva Note' ); ?>
            </form>
        </div>
        <?php endif; ?>

    </div>

    <?php saf_guida_styles_and_scripts(); ?>
    <?php
}


/* ============================================================
   HELPER — ROW DOCUMENTO DI PROGETTO
   ============================================================ */

function saf_guida_doc_row( $index, $doc = array(), $template = false ) {
    $idx   = $template ? '__IDX__' : (int) $index;
    $url   = esc_attr( $doc['url']   ?? '' );
    $title = esc_attr( $doc['title'] ?? '' );
    $date  = esc_attr( $doc['date']  ?? '' );
    $notes = esc_textarea( $doc['notes'] ?? '' );
    ?>
    <div class="saf-doc-row">
        <button type="button" class="saf-doc-remove" title="Rimuovi documento">✕</button>
        <table style="width:100%">
            <tr>
                <td style="width:130px;font-weight:600;color:#555;padding:5px 8px"><label>File (URL / PDF)</label></td>
                <td style="padding:5px 8px">
                    <input type="text" name="saf_project_docs[docs][<?php echo $idx; ?>][url]"
                           value="<?php echo $url; ?>" class="regular-text saf-media-input"
                           placeholder="URL del documento o PDF">
                    <button type="button" class="button saf-media-btn" data-target-row>📎 Media</button>
                    <?php if ( $url && ! $template ) : ?>
                        <a href="<?php echo esc_url( $doc['url'] ); ?>" target="_blank" class="button">↗ Apri</a>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="font-weight:600;color:#555;padding:5px 8px"><label>Titolo</label></td>
                <td style="padding:5px 8px"><input type="text" name="saf_project_docs[docs][<?php echo $idx; ?>][title]"
                       value="<?php echo $title; ?>" class="regular-text" placeholder="es. Documento Sintesi v1.0"></td>
            </tr>
            <tr>
                <td style="font-weight:600;color:#555;padding:5px 8px"><label>Data</label></td>
                <td style="padding:5px 8px"><input type="text" name="saf_project_docs[docs][<?php echo $idx; ?>][date]"
                       value="<?php echo $date; ?>" style="width:180px" placeholder="es. 27 Maggio 2026"></td>
            </tr>
            <tr>
                <td style="font-weight:600;color:#555;padding:5px 8px"><label>Note sessione</label></td>
                <td style="padding:5px 8px"><textarea name="saf_project_docs[docs][<?php echo $idx; ?>][notes]"
                          rows="2" class="regular-text"
                          placeholder="Modifiche principali, decisioni prese..."><?php echo $notes; ?></textarea></td>
            </tr>
        </table>
    </div>
    <?php
}


/* ============================================================
   CSS E JS INLINE PAGINA GUIDA
   ============================================================ */

function saf_guida_styles_and_scripts() {
    ?>
    <style>
    .saf-guida-wrap { max-width: 960px; }
    .saf-guida-section { margin-top: 24px; }

    /* Tabelle */
    .saf-table-monospace td code { font-size: 12px; }
    .saf-tr-module td:first-child { font-weight: 600; }
    .saf-pre { background: #f6f6f6; border: 1px solid #ddd; padding: 10px 14px; border-radius: 4px; font-size: 13px; }

    /* Shortcode cards */
    .saf-sc-card { background: #fff; border: 1px solid #ddd; border-left: 4px solid #2271b1; border-radius: 6px; padding: 16px 20px; margin-bottom: 16px; }
    .saf-sc-header { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
    .saf-sc-tag { font-size: 15px; background: #f0f4ff; padding: 4px 10px; border-radius: 4px; cursor: pointer; transition: background .15s; }
    .saf-sc-tag:hover { background: #dde8ff; }
    .saf-sc-copy-hint { color: #999; font-size: 12px; }
    .saf-sc-desc { margin: 0 0 10px; color: #444; }
    .saf-sc-params { margin-bottom: 10px; }
    .saf-sc-esempi { margin-top: 10px; }
    .saf-sc-esempi code { cursor: pointer; background: #f6f6f6; padding: 3px 8px; border-radius: 3px; margin-right: 6px; display: inline-block; margin-top: 4px; }
    .saf-sc-esempi code:hover { background: #e8f0fe; }
    .saf-copyable { cursor: pointer; }

    /* Sicurezza cards */
    .saf-measure-card { background: #fff; border: 1px solid #ddd; border-radius: 6px; padding: 14px 18px; margin-bottom: 12px; }
    .saf-measure-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
    .saf-measure-file { font-size: 11px; color: #888; margin-left: auto; }
    .saf-measure-detail { margin: 0 0 6px; color: #444; font-size: 13px; }
    .saf-measure-config { margin: 0; font-size: 12px; color: #666; }
    .saf-badge { font-size: 12px; padding: 2px 8px; border-radius: 12px; font-weight: 600; }
    .saf-badge--green { background: #e6f4ea; color: #137333; }
    .saf-badge--blue  { background: #e8f0fe; color: #1a56db; }

    /* Checklist */
    .saf-progress-bar-wrap { background: #e2e8f0; border-radius: 8px; height: 22px; margin: 0 0 20px; position: relative; overflow: hidden; }
    .saf-progress-bar { background: linear-gradient(90deg, #2271b1, #00a32a); height: 100%; border-radius: 8px; transition: width .4s; }
    .saf-progress-label { position: absolute; left: 50%; top: 50%; transform: translate(-50%,-50%); font-size: 12px; font-weight: 700; color: #fff; text-shadow: 0 1px 2px rgba(0,0,0,.3); }
    .saf-checklist { margin: 0 0 16px; padding: 0; list-style: none; }
    .saf-checklist__item { padding: 7px 10px; border-radius: 4px; margin-bottom: 3px; transition: background .1s; }
    .saf-checklist__item:hover { background: #f8f8f8; }
    .saf-checklist__item--done { text-decoration: line-through; color: #999; background: #f0faf0; }
    .saf-checklist__item label { display: flex; align-items: center; gap: 10px; cursor: pointer; }

    /* Documento progetto */
    .saf-doc-row { background: #fff; border: 1px solid #ddd; border-radius: 6px; padding: 14px 18px; margin-bottom: 10px; position: relative; }
    .saf-doc-remove { position: absolute; top: 10px; right: 12px; color: #c00; background: none; border: none; font-size: 18px; cursor: pointer; padding: 0; line-height: 1; }
    .saf-doc-remove:hover { color: #f00; }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // Copia shortcode/esempi al click
        document.addEventListener('click', function(e){
            var copyable = e.target.closest('.saf-copyable');
            if ( ! copyable ) return;
            var text = copyable.textContent.trim();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text);
            }
            var origBg = copyable.style.background;
            copyable.style.background = '#c3e6cb';
            setTimeout(function(){ copyable.style.background = origBg; }, 800);
        });

        // Media picker universale
        var mediaFrame;
        document.addEventListener('click', function(e){
            var btn = e.target.closest('.saf-media-btn');
            if ( ! btn ) return;
            e.preventDefault();
            var target = btn.dataset.target;
            var input  = target ? document.getElementById(target) : btn.closest('.saf-doc-row').querySelector('.saf-media-input');
            var prev   = target ? document.getElementById(target + '_preview') : null;

            mediaFrame = wp.media({ title: 'Seleziona file', button: { text: 'Usa questo file' }, multiple: false });
            mediaFrame.on('select', function(){
                var att = mediaFrame.state().get('selection').first().toJSON();
                if ( input ) input.value = att.url;
                if ( prev ) {
                    prev.innerHTML = '<img src="' + att.url + '" style="max-height:70px;border-radius:4px;margin-top:6px">';
                }
            });
            mediaFrame.open();
        });

        // Documento progetto — aggiungi riga
        document.getElementById('saf-add-doc').addEventListener('click', function(){
            var tpl = document.getElementById('saf-doc-tpl').innerHTML;
            var idx = document.querySelectorAll('#saf-docs-list .saf-doc-row').length;
            document.getElementById('saf-docs-list').insertAdjacentHTML('beforeend', tpl.replace(/__IDX__/g, idx));
            safReindex();
        });

        // Rimuovi riga
        document.addEventListener('click', function(e){
            var rm = e.target.closest('.saf-doc-remove');
            if ( ! rm ) return;
            rm.closest('.saf-doc-row').remove();
            safReindex();
        });

        function safReindex(){
            document.querySelectorAll('#saf-docs-list .saf-doc-row').forEach(function(row, i){
                row.querySelectorAll('[name]').forEach(function(el){
                    el.setAttribute('name', el.getAttribute('name').replace(/\[docs\]\[([^\]]+)\]/, '[docs][' + i + ']'));
                });
            });
        }
    });
    </script>
    <?php
}
