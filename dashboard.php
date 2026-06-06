<?php
/**
 * inc/dashboard.php
 * Dashboard WordPress — SAF � Security & Admin Framework.
 *
 * Sezione 75 — Widget dashboard "Panoramica Sito"
 *              → URL login corrente
 *              → Link rapidi a ⚙️ Dati Sito e 📖 Guida Sito
 *              → Barra avanzamento checklist pre-go-live
 *              → Versione child theme
 *              → Stato sicurezza (misure attive)
 * Sezione 76 — Pulsante "Visualizza Front-End" nella admin bar
 *              → Azzurro visibile, posizionato dopo "Visita sito"
 * Sezione 77 — CSS inline admin (dashboard widget + pulsante)
 */

defined( 'ABSPATH' ) || exit;


/* ============================================================
   SEZIONE 75 — WIDGET DASHBOARD "PANORAMICA SITO"
   ============================================================ */

add_action( 'wp_dashboard_setup', 'saf_register_dashboard_widget' );
function saf_register_dashboard_widget() {
    wp_add_dashboard_widget(
        'saf_panoramica',
        '🏠 ' . saf_t( 'dash_title' ),
        'saf_render_dashboard_widget'
    );

    // Sposta il widget in cima alla colonna principale
    global $wp_meta_boxes;
    $widget = $wp_meta_boxes['dashboard']['normal']['core']['saf_panoramica'] ?? null;
    if ( $widget ) {
        unset( $wp_meta_boxes['dashboard']['normal']['core']['saf_panoramica'] );
        // Inseriscilo come primo elemento
        $wp_meta_boxes['dashboard']['normal']['high']['saf_panoramica'] = $widget;
    }
}

function saf_render_dashboard_widget() {
    $org    = function_exists( 'saf_get_org_data' ) ? saf_get_org_data() : array();
    $checks = (array) get_option( 'saf_checklist', array() );

    // Calcola avanzamento checklist
    $all_keys = array(
        'org_name','org_address','org_social','seo_og','sec_slug','rankmath',
        'check_login','check_author','check_headers','check_xmlrpc','hsts','robots_url','robots_sitemap',
        'jld_org','jld_website','breadcrumb','og_tags','canonical','sitemap',
        'pagespeed','gutenberg_css','defer','images',
        'cookie_banner','privacy_policy','terms','antispam',
        'staging_test','backup','analytics','search_console','uptime',
    );
    $total = count( $all_keys );
    $done  = 0;
    foreach ( $all_keys as $k ) {
        if ( ! empty( $checks[ $k ] ) ) $done++;
    }
    $pct   = $total > 0 ? round( $done / $total * 100 ) : 0;
    $color = $pct < 40 ? '#e74c3c' : ( $pct < 80 ? '#f39c12' : '#27ae60' );

    // Theme data
    $theme   = wp_get_theme();
    $version = $theme->get( 'Version' );
    ?>
    <div class="saf-dash-widget">

        <!-- Sezione: Info sito -->
        <div class="saf-dash-row saf-dash-row--info">
            <div class="saf-dash-info-item">
                <span class="saf-dash-label"><?php echo saf_t( 'dash_site' ); ?></span>
                <a href="<?php echo esc_url( home_url('/') ); ?>" target="_blank">
                    <?php echo esc_html( home_url('/') ); ?>
                </a>
            </div>

            <?php if ( ! empty( $org['name'] ) ) : ?>
            <div class="saf-dash-info-item">
                <span class="saf-dash-label"><?php echo saf_t( 'dash_organization' ); ?></span>
                <span><?php echo esc_html( $org['name'] ); ?></span>
            </div>
            <?php endif; ?>
            <div class="saf-dash-info-item">
                <span class="saf-dash-label">🎨 Tema</span>
                <span>Amar Design v<?php echo esc_html( $version ); ?> — Divi <?php echo esc_html( wp_get_theme( get_template() )->get('Version') ?: 'n/a' ); ?></span>
            </div>
        </div>

        <!-- Sezione: Checklist avanzamento -->
        <div class="saf-dash-section">
            <div class="saf-dash-section-title"><?php echo saf_t( 'dash_checklist' ); ?></div>
            <div class="saf-dash-progress-wrap">
                <div class="saf-dash-progress-bar" style="width:<?php echo $pct; ?>%;background:<?php echo $color; ?>"></div>
            </div>
            <div class="saf-dash-progress-label" style="color:<?php echo $color; ?>">
                <?php printf( saf_t( 'checklist_progress' ), $done, $total, $pct ); ?>
            </div>
            <a href="<?php echo esc_url( admin_url('admin.php?page=saf-guida&tab=checklist') ); ?>"
               class="saf-dash-link-small">Vai alla checklist →</a>
        </div>

        <!-- Sezione: Stato sicurezza -->
        <div class="saf-dash-section">
            <div class="saf-dash-section-title">🔒 Sicurezza</div>
            <div class="saf-dash-badges">
<span class="saf-dash-badge saf-dash-badge--green">✅ XML-RPC off</span>
                <span class="saf-dash-badge saf-dash-badge--green">✅ Rate limiting</span>
                <span class="saf-dash-badge saf-dash-badge--green">✅ Headers HTTP</span>
                <span class="saf-dash-badge saf-dash-badge--green">✅ Enum utenti off</span>
            </div>
        </div>

        <!-- Sezione: Pulsanti accesso rapido — stile Inzaion -->
        <div class="saf-dash-section saf-dash-btns">
            <div class="saf-dash-btn-grid">
                <a href="<?php echo esc_url( admin_url('admin.php?page=saf-dati-sito') ); ?>"
                   class="saf-dash-orange-btn">
                    <span class="saf-dash-orange-btn__icon">⚙️</span>
                    <span class="saf-dash-orange-btn__label"><?php echo saf_t( 'dash_btn_dati' ); ?></span>
                </a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=saf-guida') ); ?>"
                   class="saf-dash-orange-btn">
                    <span class="saf-dash-orange-btn__icon">📖</span>
                    <span class="saf-dash-orange-btn__label"><?php echo saf_t( 'dash_btn_guida' ); ?></span>
                </a>
            </div>
        </div>

        <!-- Crediti sviluppatore -->
        <?php
        $user_credits = (array) get_option( 'saf_credits_settings', array() );
        $author_name  = $user_credits['author_name'] ?? '';
        $author_url   = $user_credits['author_url'] ?? '';
        $client_name  = $user_credits['client_name'] ?? '';
        $created      = $user_credits['created'] ?? '';
        if ( $author_name || $client_name ) :
        ?>
        <div class="saf-dash-credits" style="margin-top:16px;padding-top:12px;border-top:1px solid #e2e8f0;font-size:11px;color:#94a3b8;text-align:center">
            <?php if ( $author_name ) : ?>
                <?php echo saf_t( 'dash_credits' ); ?> <?php if ( $author_url ) : ?><a href="<?php echo esc_url( $author_url ); ?>" target="_blank" style="color:#2ea3f2"><?php endif; ?><strong><?php echo esc_html( $author_name ); ?></strong><?php if ( $author_url ) : ?></a><?php endif; ?>
            <?php endif; ?>
            <?php if ( $client_name ) : ?>
                · <?php echo saf_t( 'dash_for' ); ?> <strong><?php echo esc_html( $client_name ); ?></strong>
            <?php endif; ?>
            <?php if ( $created ) : ?>
                · <?php echo esc_html( $created ); ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
    <?php
}


/* ============================================================
   SEZIONE 76 — PULSANTE "VISUALIZZA FRONT-END" NELLA ADMIN BAR
   Azzurro, visibile ma non invasivo. Posizionato a destra
   nella admin bar, dopo "Visita sito".
   ============================================================ */

add_action( 'admin_bar_menu', 'saf_add_frontend_button', 100 );
function saf_add_frontend_button( $wp_admin_bar ) {
    if ( ! is_admin() ) return;
    if ( ! current_user_can( 'edit_posts' ) ) return;

    $wp_admin_bar->add_node( array(
        'id'    => 'saf-view-frontend',
        'title' => saf_t( 'adminbar_frontend' ),
        'href'  => home_url( '/' ),
        'meta'  => array(
            'target' => '_blank',
            'class'  => 'saf-adminbar-frontend',
            'title'  => saf_t( 'adminbar_frontend_title' ),
        ),
    ) );
}


/* ============================================================
   SEZIONE 77 — CSS ADMIN (widget dashboard + pulsante)
   ============================================================ */

add_action( 'admin_head', 'saf_admin_dashboard_css' );
function saf_admin_dashboard_css() {
    $screen = get_current_screen();
    ?>
    <style>
    /* ---- PULSANTE FRONT-END — sempre visibile ---- */
    #wpadminbar #wp-admin-bar-saf-view-frontend > .ab-item {
        background: #3b9dd2 !important;
        color: #fff !important;
        border-radius: 4px;
        padding: 0 12px !important;
        margin: 6px 8px 0 0;
        height: 22px !important;
        line-height: 22px !important;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: .02em;
        transition: background .18s;
    }
    #wpadminbar #wp-admin-bar-saf-view-frontend > .ab-item:hover {
        background: #2980b2 !important;
    }

    <?php if ( $screen && $screen->id === 'dashboard' ) : ?>
    /* ---- WIDGET DASHBOARD ---- */
    .saf-dash-widget { font-size: 13px; color: #3c434a; }

    .saf-dash-row--info {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 12px 14px;
        margin-bottom: 14px;
    }
    .saf-dash-info-item {
        display: flex;
        align-items: baseline;
        gap: 8px;
        padding: 4px 0;
        border-bottom: 1px solid #f0f4f8;
    }
    .saf-dash-info-item:last-child { border-bottom: none; }
    .saf-dash-label {
        min-width: 130px;
        font-weight: 600;
        color: #64748b;
        font-size: 12px;
        flex-shrink: 0;
    }
    .saf-dash-info-item a { color: #2271b1; text-decoration: none; }
    .saf-dash-info-item a:hover { text-decoration: underline; }
    .saf-dash-info-item code {
        font-size: 11px;
        background: #f0f4f8;
        padding: 1px 6px;
        border-radius: 3px;
        color: #c0392b;
    }

    .saf-dash-section {
        margin-bottom: 14px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f0f4f8;
    }
    .saf-dash-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .saf-dash-section-title {
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        margin-bottom: 8px;
    }

    /* Progress bar */
    .saf-dash-progress-wrap {
        background: #e2e8f0;
        border-radius: 8px;
        height: 10px;
        overflow: hidden;
        margin-bottom: 5px;
    }
    .saf-dash-progress-bar {
        height: 100%;
        border-radius: 8px;
        transition: width .4s ease;
    }
    .saf-dash-progress-label {
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .saf-dash-link-small {
        font-size: 12px;
        color: #2271b1;
        text-decoration: none;
    }
    .saf-dash-link-small:hover { text-decoration: underline; }

    /* Badges sicurezza */
    .saf-dash-badges { display: flex; flex-wrap: wrap; gap: 5px; }
    .saf-dash-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 600;
    }
    .saf-dash-badge--green { background: #e6f4ea; color: #137333; }
    .saf-dash-badge--orange { background: #fff3e0; color: #e65100; }
    .saf-dash-badge--red { background: #fce8e6; color: #c5221f; }

    /* Pulsanti accesso rapido — stile Inzaion */
    .saf-dash-btn-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 4px;
    }
    .saf-dash-orange-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: linear-gradient(135deg, #f5960a 0%, #e07b00 100%);
        border-radius: 8px;
        padding: 18px 12px 14px;
        text-decoration: none !important;
        color: #fff !important;
        font-weight: 700;
        transition: transform .15s, box-shadow .15s, filter .15s;
        box-shadow: 0 3px 10px rgba(230,130,12,.35);
        min-height: 80px;
    }
    .saf-dash-orange-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(230,130,12,.45);
        filter: brightness(1.08);
        color: #fff !important;
    }
    .saf-dash-orange-btn__icon { font-size: 26px; line-height: 1; }
    .saf-dash-orange-btn__label { font-size: 13px; letter-spacing: .02em; text-align: center; }
    <?php endif; ?>
    </style>
    <?php
}
