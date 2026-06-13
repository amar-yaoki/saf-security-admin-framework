<?php
/**
 * admin.php — Sezioni 50-57
 *
 * 50 — Pagina opzioni menu admin
 * 51 — saf_get_org_data()
 * 52 — Render tab Organizzazione
 * 53 — Render tab SEO & NAP Footer
 * 54 — Render tab Sicurezza & Avanzate
 * 55 — Pulizia widget dashboard
 * 56 — Tab robots.txt
 * 57 — SMTP From Name / From Email + sanitizzatori settings
 */

defined( 'ABSPATH' ) || exit;


/* ============================================================
   SEZIONE 50 — PAGINA OPZIONI MENU ADMIN
   ============================================================ */

add_action( 'admin_menu', 'saf_register_admin_pages' );
function saf_register_admin_pages() {
    add_menu_page(
        saf_t( 'menu_page_title' ),
        saf_t( 'menu_title' ),
        'manage_options',
        'saf-dati-sito',
        'saf_render_dati_sito',
        'dashicons-admin-settings',
        3
    );
}
// Rimuove il sottomenu automatico duplicato (doppia icona).
add_action( 'admin_menu', function() {
    remove_submenu_page( 'saf-dati-sito', 'saf-dati-sito' );
}, 99 );


/* ============================================================
   SEZIONE 50/B — COLORE E STILE VOCI MENU DATI SITO E GUIDA
   CSS iniettato via admin_head (style.css non viene caricato in backend)
   ============================================================ */

add_action( 'admin_head', 'saf_admin_styles' );
function saf_admin_styles() {
    ?>
    <style>
    /* Menu: nascondi Dashicon, emoji nel testo fa da icona */
    #adminmenu #toplevel_page_saf-dati-sito .wp-menu-image,
    #adminmenu #toplevel_page_saf-guida .wp-menu-image {
        display: none !important;
    }
    #adminmenu #toplevel_page_saf-dati-sito .wp-menu-name,
    #adminmenu #toplevel_page_saf-guida .wp-menu-name {
        padding-left: 8px !important;
    }

    /* Colore menu: blu */
    #adminmenu #toplevel_page_saf-dati-sito > a,
    #adminmenu #toplevel_page_saf-guida > a {
        color: #2ea3f2 !important;
        font-weight: 700 !important;
    }
    #adminmenu #toplevel_page_saf-dati-sito > a:hover,
    #adminmenu #toplevel_page_saf-guida > a:hover,
    #adminmenu #toplevel_page_saf-dati-sito.current > a,
    #adminmenu #toplevel_page_saf-guida.current > a {
        color: #ffffff !important;
    }

    /* Tab admin brandizzati: Amar */
    .nav-tab-wrapper {
        display: grid !important;
        grid-template-columns: repeat(5, 1fr) !important;
        gap: 4px !important;
        border-bottom: none !important;
        margin: 0 0 6px 0 !important;
        padding: 0 !important;
        background: transparent !important;
        border-radius: 0 !important;
    }
    .nav-tab {
        font-variant: all-petite-caps !important;
        letter-spacing: 0.03em !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        padding: 10px 4px !important;
        text-align: center !important;
        transition: all 0.2s ease !important;
        background: #2a2a2a !important;
        border: 1px solid #3a3a3a !important;
        border-radius: 4px !important;
        color: #888 !important;
        margin: 0 !important;
        outline: none !important;
        box-shadow: none !important;
    }
    .nav-tab:hover {
        background: #333 !important;
        color: #ddd !important;
        border-color: #555 !important;
    }
    .nav-tab:focus,
    .nav-tab:active {
        outline: none !important;
        box-shadow: none !important;
        border-color: #f47D39 !important;
    }
    .nav-tab-active,
    .nav-tab-active:hover {
        background: #f47D39 !important;
        border-color: #f47D39 !important;
        color: #121212 !important;
        box-shadow: none !important;
        outline: none !important;
    }
    .wrap.saf-admin-wrap h1 {
        font-size: 22px !important;
        margin-bottom: 16px !important;
        color: #121212 !important;
        font-weight: 700 !important;
    }
    .saf-tab-content {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-top: 2px solid #f47D39;
        border-radius: 0 0 6px 6px;
        padding: 24px;
        margin-top: 0;
    }
    .saf-credits {
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
        font-size: 12px;
        color: #6b7280;
        text-align: center;
    }

    /* Editor stile Notepad++ per style.css */
    .saf-code-editor {
        background: #1e1e1e !important;
        color: #d4d4d4 !important;
        font-family: 'Cascadia Code', 'Fira Code', 'Consolas', monospace !important;
        font-size: 13px !important;
        line-height: 1.6 !important;
        padding: 12px 16px !important;
        border: 1px solid #333 !important;
        border-radius: 6px !important;
        tab-size: 4 !important;
        resize: vertical !important;
        min-height: 300px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .saf-code-editor:focus {
        outline: none !important;
        border-color: #2ea3f2 !important;
        box-shadow: 0 0 0 2px rgba(46,163,242,0.2) !important;
    }
    </style>
    <?php
}


/* ============================================================
   SEZIONE 51 — REGISTRAZIONE SETTINGS E SANITIZZATORI
   ============================================================ */

add_action( 'admin_init', 'saf_register_settings' );
function saf_register_settings() {
    register_setting( 'saf_org_group', 'saf_org_settings', array(
        'sanitize_callback' => 'saf_sanitize_org',
    ) );
    register_setting( 'saf_seo_group', 'saf_seo_settings', array(
        'sanitize_callback' => 'saf_sanitize_seo',
    ) );
    register_setting( 'saf_sec_group', 'saf_sec_settings', array(
        'sanitize_callback' => 'saf_sanitize_sec',
    ) );
    register_setting( 'saf_sc_group', 'saf_sc_settings', array(
        'sanitize_callback' => 'saf_sanitize_sc',
    ) );
}

function saf_sanitize_org( $input ) {
    return array(
        'name'      => sanitize_text_field(   $input['name']      ?? '' ),
        'url'       => esc_url_raw(           $input['url']       ?? '' ),
        'logo'      => esc_url_raw(           $input['logo']      ?? '' ),
        'address'   => sanitize_text_field(   $input['address']   ?? '' ),
        'cap'       => sanitize_text_field(   $input['cap']       ?? '' ),
        'city'      => sanitize_text_field(   $input['city']      ?? '' ),
        'country'   => strtoupper( sanitize_text_field( $input['country'] ?? 'IT' ) ),
        'piva'      => sanitize_text_field(   $input['piva']      ?? '' ),
        'email'     => sanitize_email(        $input['email']     ?? '' ),
        'phone'     => sanitize_text_field(   $input['phone']     ?? '' ),
        'facebook'  => esc_url_raw(           $input['facebook']  ?? '' ),
        'instagram' => esc_url_raw(           $input['instagram'] ?? '' ),
        'youtube'   => esc_url_raw(           $input['youtube']   ?? '' ),
        'linkedin'  => esc_url_raw(           $input['linkedin']  ?? '' ),
        'twitter'   => esc_url_raw(           $input['twitter']   ?? '' ),
    );
}

function saf_sanitize_seo( $input ) {
    return array(
        'og_default'   => esc_url_raw( $input['og_default']   ?? '' ),
        'og_default_2' => esc_url_raw( $input['og_default_2'] ?? '' ),
    );
}

function saf_sanitize_sec( $input ) {
    $attempts = absint( $input['max_attempts'] ?? 5 );
    $attempts = max( 3, min( 20, $attempts ) );

    // Aggiorna l'opzione letta direttamente da security.php
    update_option( 'saf_max_login_attempts', $attempts );

    return array(
        'max_attempts' => $attempts,
    );
}


function saf_sanitize_sc( $input ) {
    $allowed_social = array( 'facebook', 'whatsapp', 'telegram', 'instagram', 'tiktok', 'email', 'copy' );
    $allowed_dev    = array( 'github', 'gitlab', 'stackoverflow', 'reddit', 'devto', 'medium', 'linkedin', 'amazon_author', 'x', 'mastodon', 'youtube', 'codepen', 'personal_site' );
    $clean = array();

    if ( ! empty( $input['social_share'] ) && is_array( $input['social_share'] ) ) {
        $clean['social_share'] = array_values( array_intersect( $input['social_share'], $allowed_social ) );
    } else {
        $clean['social_share'] = array();
    }

    if ( ! empty( $input['dev_enabled'] ) && is_array( $input['dev_enabled'] ) ) {
        $clean['dev_enabled'] = array_values( array_intersect( $input['dev_enabled'], $allowed_dev ) );
    } else {
        $clean['dev_enabled'] = array();
    }

    $clean['dev_urls'] = array();
    if ( ! empty( $input['dev_urls'] ) && is_array( $input['dev_urls'] ) ) {
        foreach ( $input['dev_urls'] as $key => $url ) {
            if ( in_array( $key, $allowed_dev, true ) && ! empty( trim( $url ) ) ) {
                $clean['dev_urls'][ $key ] = esc_url_raw( trim( $url ) );
            }
        }
    }

    return $clean;
}


/* ============================================================
   SEZIONE 52 — HELPER saf_get_org_data()
   Restituisce array con tutti i dati organizzazione.
   Usato da seo.php, helpers.php e security.php.
   ============================================================ */

function saf_get_org_data() {
    static $cache = null;
    if ( $cache !== null ) return $cache;

    $org = (array) get_option( 'saf_org_settings', array() );
    $seo = (array) get_option( 'saf_seo_settings', array() );

    $cache = array(
        'name'       => $org['name']      ?? '',
        'url'        => $org['url']       ?? home_url( '/' ),
        'logo'       => $org['logo']      ?? '',
        'address'    => $org['address']   ?? '',
        'cap'        => $org['cap']       ?? '',
        'city'       => $org['city']      ?? '',
        'piva'       => $org['piva']      ?? '',
        'email'      => $org['email']     ?? '',
        'phone'      => $org['phone']     ?? '',
        'facebook'   => $org['facebook']  ?? '',
        'instagram'  => $org['instagram'] ?? '',
        'youtube'    => $org['youtube']   ?? '',
        'linkedin'   => $org['linkedin']  ?? '',
        'twitter'    => $org['twitter']   ?? '',
        'og_default' => $seo['og_default'] ?? '',
        'og_default_2' => $seo['og_default_2'] ?? '',
    );

    return $cache;
}


/* ============================================================
   SEZIONE 53 — ENQUEUE SCRIPT BACKEND
   ============================================================ */

add_action( 'admin_enqueue_scripts', 'saf_admin_enqueue' );
function saf_admin_enqueue( $hook ) {
    $pages = array( 'toplevel_page_saf-dati-sito', 'dati-sito_page_saf-guida' );
    if ( ! in_array( $hook, $pages, true ) ) return;

    wp_enqueue_media();
    wp_enqueue_script(
        'saf-admin',
        SAF_URL . 'admin.js',
        array( 'jquery' ),
        file_exists( SAF_DIR . 'admin.js' ) ? filemtime( SAF_DIR . 'admin.js' ) : SAF_VERSION,
        true
    );
    wp_localize_script( 'saf-admin', 'saf_admin', array(
        'nonce' => wp_create_nonce( 'saf_admin_nonce' ),
        'dismiss_nonce' => wp_create_nonce( 'saf_dismiss_notice' ),
    ) );
}


/* ============================================================
   SEZIONE 54 — RENDER PAGINA ⚙️ DATI SITO
   ============================================================ */

function saf_render_dati_sito() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $tab    = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'org';
    $org    = (array) get_option( 'saf_org_settings', array() );
    $seo    = (array) get_option( 'saf_seo_settings', array() );
    $sec    = (array) get_option( 'saf_sec_settings', array() );
    $adv    = (array) get_option( 'saf_adv_settings', array() );
    $robots = (string) get_option( 'saf_robots_content', '' );
    $nap     = (string) get_option( 'saf_nap_html', '' );
    $credits = (array) get_option( 'saf_credits_settings', array() );

    $tabs = array(
        'org'       => saf_t( 'tab_org' ),
        'seo'       => saf_t( 'tab_seo' ),
        'security'  => saf_t( 'tab_security' ),
        'robots'    => saf_t( 'tab_robots' ),
        'nap'       => saf_t( 'tab_nap' ),
        'shortcode' => saf_t( 'tab_shortcode' ),
        'advanced'  => saf_t( 'tab_advanced' ),
        'child'     => saf_t( 'tab_child' ),
        'sistema'   => saf_t( 'tab_sistema' ),
        'plugins'   => saf_t( 'tab_plugins' ),
        'credits'   => saf_t( 'tab_credits' ),
    );
    ?>
    <div class="wrap saf-admin-wrap">
        <h1><?php echo saf_t( 'site_data' ); ?></h1>

        <nav class="nav-tab-wrapper">
        <?php foreach ( $tabs as $slug => $label ) :
            $url   = add_query_arg( array( 'page' => 'saf-dati-sito', 'tab' => $slug ), admin_url( 'admin.php' ) );
            $class = $tab === $slug ? 'nav-tab nav-tab-active' : 'nav-tab';
        ?>
            <a href="<?php echo esc_url( $url ); ?>" class="<?php echo $class; ?>"><?php echo esc_html( $label ); ?></a>
        <?php endforeach; ?>
        </nav>

        <div class="saf-tab-content">

        <?php if ( $tab === 'org' ) : ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'saf_org_group' ); ?>
            <h2><?php echo saf_t( 'org_title' ); ?></h2>
            <p class="description"><?php echo saf_t( 'org_desc' ); ?></p>
            <table class="form-table">
                <tr>
                    <th><label for="saf_name"><?php echo saf_t( 'org_name' ); ?> <span style="color:red">*</span></label></th>
                    <td><input type="text" id="saf_name" name="saf_org_settings[name]"
                               value="<?php echo esc_attr( $org['name'] ?? '' ); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="saf_url"><?php echo saf_t( 'org_url' ); ?></label></th>
                    <td>
                        <input type="url" id="saf_url" name="saf_org_settings[url]"
                               value="<?php echo esc_attr( $org['url'] ?? home_url('/') ); ?>" class="regular-text">
                        <p class="description"><?php echo saf_t( 'org_url_desc' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="saf_logo"><?php echo saf_t( 'org_logo' ); ?></label></th>
                    <td>
                        <input type="text" id="saf_logo" name="saf_org_settings[logo]"
                               value="<?php echo esc_attr( $org['logo'] ?? '' ); ?>" class="regular-text saf-media-input" data-preview="saf_logo_preview">
                        <button type="button" class="button saf-media-btn" data-target="saf_logo">📎 <?php echo saf_t( 'org_logo_btn' ); ?></button>
                        <div id="saf_logo_preview" style="margin-top:8px">
                            <?php if ( ! empty( $org['logo'] ) ) : ?>
                                <img src="<?php echo esc_url( $org['logo'] ); ?>" style="max-height:70px;border-radius:4px">
                            <?php endif; ?>
                        </div>
                        <p class="description"><?php echo saf_t( 'org_logo_desc' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label><?php echo saf_t( 'org_address' ); ?></label></th>
                    <td>
                        <input type="text" name="saf_org_settings[address]"
                               value="<?php echo esc_attr( $org['address'] ?? '' ); ?>" class="regular-text" placeholder="Via Roma 1">
                        <br><br>
                        <input type="text" name="saf_org_settings[cap]"
                                value="<?php echo esc_attr( $org['cap'] ?? '' ); ?>" style="width:80px" placeholder="<?php echo esc_attr( saf_t( 'org_zip' ) ); ?>">
                        &nbsp;
                        <input type="text" name="saf_org_settings[city]"
                                value="<?php echo esc_attr( $org['city'] ?? '' ); ?>" style="width:220px" placeholder="<?php echo esc_attr( saf_t( 'org_city' ) ); ?>">
                        &nbsp;
                        <input type="text" name="saf_org_settings[country]"
                                value="<?php echo esc_attr( $org['country'] ?? 'IT' ); ?>" style="width:50px" placeholder="IT">
                    </td>
                </tr>
                <tr>
                    <th><label for="saf_piva"><?php echo saf_t( 'org_vat' ); ?></label></th>
                    <td>
                        <input type="text" id="saf_piva" name="saf_org_settings[piva]"
                               value="<?php echo esc_attr( $org['piva'] ?? '' ); ?>" class="regular-text">
                        <p class="description"><?php echo saf_t( 'org_vat_desc' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="saf_email"><?php echo saf_t( 'org_email' ); ?></label></th>
                    <td><input type="email" id="saf_email" name="saf_org_settings[email]"
                               value="<?php echo esc_attr( $org['email'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="saf_phone"><?php echo saf_t( 'org_phone' ); ?></label></th>
                    <td><input type="text" id="saf_phone" name="saf_org_settings[phone]"
                               value="<?php echo esc_attr( $org['phone'] ?? '' ); ?>" class="regular-text" placeholder="+39 02 1234567"></td>
                </tr>
            </table>

            <h2>📱 <?php echo saf_t( 'org_social' ); ?></h2>
            <p class="description"><?php echo saf_t( 'org_social_desc' ); ?></p>
            <table class="form-table">
            <?php
            $socials = array(
                'facebook'  => 'Facebook',
                'instagram' => 'Instagram',
                'youtube'   => 'YouTube',
                'linkedin'  => 'LinkedIn',
                'twitter'   => 'X / Twitter',
            );
            foreach ( $socials as $key => $label ) :
            ?>
                <tr>
                    <th><label for="saf_<?php echo $key; ?>"><?php echo esc_html( $label ); ?></label></th>
                    <td><input type="url" id="saf_<?php echo $key; ?>"
                               name="saf_org_settings[<?php echo $key; ?>]"
                               value="<?php echo esc_attr( $org[ $key ] ?? '' ); ?>"
                                class="regular-text" placeholder="https://<?php echo saf_t( 'url_placeholder_domain' ); ?>"></td>
                </tr>
            <?php endforeach; ?>
            </table>

            <?php submit_button( saf_t( 'btn_save_org' ) ); ?>
        </form>

        <?php elseif ( $tab === 'seo' ) : ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'saf_seo_group' ); ?>
            <h2><?php echo saf_t( 'seo_title' ); ?></h2>
            <p class="description"><?php echo saf_t( 'seo_desc' ); ?></p>
            <table class="form-table">
                <?php
                $og_fields = array(
                    'og_default'   => saf_t( 'seo_img1' ),
                    'og_default_2' => saf_t( 'seo_img2' ),
                );
                foreach ( $og_fields as $key => $label ) :
                $val = $seo[ $key ] ?? '';
                ?>
                <tr>
                    <th><label for="saf_<?php echo $key; ?>"><?php echo esc_html( $label ); ?></label></th>
                    <td>
                        <input type="text" id="saf_<?php echo $key; ?>"
                               name="saf_seo_settings[<?php echo $key; ?>]"
                               value="<?php echo esc_attr( $val ); ?>"
                               class="regular-text saf-media-input" data-preview="saf_<?php echo $key; ?>_preview">
                        <button type="button" class="button saf-media-btn"
                                 data-target="saf_<?php echo $key; ?>">📎 <?php echo saf_t( 'org_logo_btn' ); ?></button>
                        <div id="saf_<?php echo $key; ?>_preview" style="margin-top:8px">
                            <?php if ( $val ) : ?>
                                <img src="<?php echo esc_url( $val ); ?>" style="max-width:300px;border-radius:4px;border:1px solid #ddd">
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php submit_button( saf_t( 'btn_save_seo' ) ); ?>
        </form>

        <?php elseif ( $tab === 'security' ) : ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'saf_sec_group' ); ?>
            <h2><?php echo saf_t( 'sec_title' ); ?></h2>
            <p class="description">
                <?php echo saf_t( 'sec_access' ); ?>
                <?php echo saf_t( 'sec_login_brand' ); ?>
            </p>
            <table class="form-table">
                <tr>
                    <th><label for="saf_max_attempts"><?php echo saf_t( 'sec_max_attempts' ); ?></label></th>
                    <td>
                        <input type="number" id="saf_max_attempts" name="saf_sec_settings[max_attempts]"
                               value="<?php echo esc_attr( $sec['max_attempts'] ?? 5 ); ?>"
                               min="3" max="20" style="width:70px">
                        <p class="description"><?php echo saf_t( 'sec_max_desc' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php echo saf_t( 'sec_active' ); ?></th>
                    <td>
                        <ul style="margin:0;padding:0;list-style:none;line-height:2.2">
                            <li><?php echo saf_t( 'sec_list_rate' ); ?></li>
                            <li><?php echo saf_t( 'sec_list_xmlrpc' ); ?></li>
                            <li><?php echo saf_t( 'sec_list_enum' ); ?></li>
                            <li><?php echo saf_t( 'sec_list_rest' ); ?></li>
                            <li><?php echo saf_t( 'sec_list_headers' ); ?></li>
                            <li><?php echo saf_t( 'sec_list_wp_version' ); ?></li>
                            <li><?php echo saf_t( 'sec_list_login_errors' ); ?></li>
                            <li><?php echo saf_t( 'sec_list_file_edit' ); ?></li>
                            <li><?php echo saf_t( 'sec_list_spam' ); ?></li>
                            <li><?php echo saf_t( 'sec_list_login_brand' ); ?></li>
                        </ul>
                    </td>
                </tr>
            </table>
            <?php submit_button( saf_t( 'btn_save_sec' ) ); ?>
        </form>

        <?php elseif ( $tab === 'robots' ) : ?>
        <?php
        $seo_active = class_exists( 'RankMath\\Robots_Txt' ) || defined( 'WPSEO_VERSION' );
        ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'saf_robots_group' ); ?>
            <h2><?php echo saf_t( 'robots_title' ); ?></h2>
            <?php if ( $seo_active ) : ?>
            <div class="notice notice-warning inline" style="margin:12px 0"><p>
                <?php echo sprintf( saf_t( 'robots_warn_seo' ), esc_html( home_url('/robots.txt') ) ); ?>
            </p></div>
            <?php else : ?>
            <div class="notice notice-info inline" style="margin:12px 0"><p>
                <?php echo sprintf( saf_t( 'robots_warn_info' ), esc_html( home_url('/robots.txt') ) ); ?>
            </p></div>
            <?php endif; ?>
            <table class="form-table">
                <tr>
                    <th><label for="saf_robots"><?php echo saf_t( 'robots_txt_label' ); ?></label></th>
                    <td>
                        <textarea id="saf_robots" name="saf_robots_content"
                                  rows="35" style="width:100%;font-family:monospace;font-size:12px;line-height:1.6"
                        ><?php echo esc_textarea( $robots ); ?></textarea>
                        <p class="description">
                            <a href="<?php echo esc_url( home_url('/robots.txt') ); ?>" target="_blank"><?php echo saf_t( 'robots_view_live' ); ?></a>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button( saf_t( 'btn_save_robots' ) ); ?>
        </form>

        <hr style="margin-top:16px">

        <h3><?php echo saf_t( 'robots_export_title' ); ?></h3>
        <p class="description"><?php echo saf_t( 'robots_export_desc' ); ?></p>
        <form method="post">
            <?php wp_nonce_field( 'saf_export_robots', 'saf_export_robots_nonce' ); ?>
            <input type="hidden" name="saf_export_robots" value="1">
            <?php submit_button( saf_t( 'robots_export_btn' ), 'secondary', '', false ); ?>
        </form>

        <?php
        // Gestione export
        if ( ! empty( $_POST['saf_export_robots'] ) && check_admin_referer( 'saf_export_robots', 'saf_export_robots_nonce' ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                $content = get_option( 'saf_robots_content', '' );
                if ( empty( trim( $content ) ) ) {
                    echo '<div class="notice notice-warning is-dismissible"><p>' . saf_t( 'robots_export_empty' ) . '</p></div>';
                } else {
                    // Sostituisce placeholder
                    $site_url = home_url( '/' );
                    $content  = str_replace(
                        array( 'https://www.tuosito.it/', 'https://www.tuosito.it', '{{SITE_URL}}' ),
                        $site_url,
                        $content
                    );
                    $file = ABSPATH . 'robots.txt';
                    $written = saf_write_file( $file, $content );
                    if ( $written ) {
                        echo '<div class="notice notice-success is-dismissible"><p>' . sprintf( saf_t( 'robots_export_ok' ), esc_html( $file ) ) . '</p></div>';
                    } else {
                        echo '<div class="notice notice-error is-dismissible"><p>' . saf_t( 'robots_export_err' ) . '</p></div>';
                    }
                }
            }
        }
        ?>

        <?php elseif ( $tab === 'nap' ) : ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'saf_nap_group' ); ?>
            <h2><?php echo saf_t( 'nap_title' ); ?></h2>
            <p class="description"><?php echo saf_t( 'nap_desc' ); ?></p>
            <table class="form-table">
                <tr>
                    <th><label for="saf_nap_html"><?php echo saf_t( 'nap_content_label' ); ?></label></th>
                    <td>
                        <textarea id="saf_nap_html" name="saf_nap_html"
                                  rows="14" style="width:100%;font-family:monospace;font-size:13px;line-height:1.6"
                                  placeholder="<address class=&quot;footer-nap&quot;>&#10;  <strong>Nome Azienda Srl</strong>&#10;  Via Roma 1, 20100 Milano&#10;  <a href=&quot;tel:+390211234567&quot;>02 1234567</a>&#10;  <a href=&quot;mailto:info@sito.it&quot;>info@sito.it</a>&#10;  P.IVA 12345678901&#10;</address>"
                        ><?php echo esc_textarea( $nap ); ?></textarea>
                        <p class="description">
                            <?php echo saf_t( 'nap_preview' ); ?> <?php if ( $nap ) : ?>
                                <a href="#" onclick="document.getElementById('saf-nap-preview').style.display='block';return false;"><?php echo saf_t( 'nap_show_preview' ); ?></a>
                                <div id="saf-nap-preview" style="display:none;margin-top:10px;padding:12px;background:#f8f8f8;border:1px solid #ddd;border-radius:4px">
                                    <?php echo wp_kses_post( $nap ); ?>
                                </div>
                            <?php else : ?>
                                <em><?php echo saf_t( 'nap_no_content' ); ?></em>
                            <?php endif; ?>
                        </p>
                        <p class="description"><?php echo saf_t( 'nap_shortcode' ); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button( saf_t( 'btn_save_nap' ) ); ?>
        </form>

        <?php elseif ( $tab === 'shortcode' ) : ?>
        <?php
        $sc_opts = (array) get_option( 'saf_sc_settings', array() );
        $sc_enabled = $sc_opts['social_share'] ?? null;
        $dev_enabled = $sc_opts['dev_enabled'] ?? null;
        $dev_urls = $sc_opts['dev_urls'] ?? array();
        // Se l'opzione non è mai stata salvata, default = tutti attivi
        $all_on = ( $sc_enabled === null );
        $all_dev_on = ( $dev_enabled === null );

        $sc_platforms = array(
            'facebook'  => array( 'label' => 'Facebook',  'color' => '#1877F2' ),
            'whatsapp'  => array( 'label' => 'WhatsApp',  'color' => '#25D366' ),
            'telegram'  => array( 'label' => 'Telegram',  'color' => '#0088cc' ),
            'instagram' => array( 'label' => 'Instagram', 'color' => '#C13584', 'note' => saf_t( 'sc_copy_note' ) ),
            'tiktok'    => array( 'label' => 'TikTok',    'color' => '#010101', 'note' => saf_t( 'sc_copy_note' ) ),
            'email'     => array( 'label' => 'Email',     'color' => '#f47D39' ),
            'copy'      => array( 'label' => saf_t( 'sc_copy_label' ), 'color' => '#6c757d' ),
        );

        $dev_platforms = array(
            'github'         => array( 'label' => 'GitHub',          'color' => '#181717', 'placeholder' => 'https://github.com/username' ),
            'gitlab'         => array( 'label' => 'GitLab',          'color' => '#FC6D26', 'placeholder' => 'https://gitlab.com/username' ),
            'stackoverflow'  => array( 'label' => 'Stack Overflow',  'color' => '#F58025', 'placeholder' => 'https://stackoverflow.com/users/12345/username' ),
            'reddit'         => array( 'label' => 'Reddit',          'color' => '#FF4500', 'placeholder' => 'https://reddit.com/user/username' ),
            'devto'          => array( 'label' => 'Dev.to',          'color' => '#0A0A0A', 'placeholder' => 'https://dev.to/username' ),
            'medium'         => array( 'label' => 'Medium',          'color' => '#000000', 'placeholder' => 'https://medium.com/@username' ),
            'linkedin'       => array( 'label' => 'LinkedIn',        'color' => '#0A66C2', 'placeholder' => 'https://linkedin.com/in/username' ),
            'amazon_author'  => array( 'label' => 'Amazon Authors',  'color' => '#FF9900', 'placeholder' => 'https://amazon.com/author/username' ),
            'x'              => array( 'label' => 'X / Twitter',     'color' => '#000000', 'placeholder' => 'https://x.com/username' ),
            'mastodon'       => array( 'label' => 'Mastodon',        'color' => '#6364FF', 'placeholder' => 'https://mastodon.social/@username' ),
            'youtube'        => array( 'label' => 'YouTube',         'color' => '#FF0000', 'placeholder' => 'https://youtube.com/@channel' ),
            'codepen'        => array( 'label' => 'CodePen',         'color' => '#000000', 'placeholder' => 'https://codepen.io/username' ),
            'personal_site'  => array( 'label' => 'Sito personale',  'color' => '#f47D39', 'placeholder' => 'https://tuosito.it' ),
        );
        ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'saf_sc_group' ); ?>
            <h2><?php echo saf_t( 'sc_social_share_title' ); ?></h2>
            <p class="description"><?php echo saf_t( 'sc_social_share_desc' ); ?></p>
            <p class="description"><?php echo saf_t( 'sc_shortcode_usage' ); ?> <code>[condividi_social]</code> &middot; <code>[condividi_social type="dev"]</code> &middot; <code>[condividi_social type="all"]</code></p>

            <h3 style="margin-top:24px;"><?php echo saf_t( 'sc_social_section' ); ?></h3>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php echo saf_t( 'sc_active_buttons' ); ?></th>
                    <td>
                        <?php foreach ( $sc_platforms as $key => $meta ) :
                            $checked = $all_on || in_array( $key, (array) $sc_enabled, true );
                        ?>
                        <label style="display:flex;align-items:center;gap:8px;margin-bottom:10px;cursor:pointer;">
                            <input type="checkbox"
                                   name="saf_sc_settings[social_share][]"
                                   value="<?php echo esc_attr( $key ); ?>"
                                   <?php checked( $checked ); ?>>
                            <span style="display:inline-flex;align-items:center;gap:6px;">
                                <span style="width:12px;height:12px;border-radius:50%;background:<?php echo esc_attr( $meta['color'] ); ?>;display:inline-block;flex-shrink:0;"></span>
                                <strong><?php echo esc_html( $meta['label'] ); ?></strong>
                                <?php if ( ! empty( $meta['note'] ) ) : ?>
                                    <span style="color:#888;font-size:12px;"><?php echo esc_html( $meta['note'] ); ?></span>
                                <?php endif; ?>
                            </span>
                        </label>
                        <?php endforeach; ?>
                        <p class="description" style="margin-top:8px;"><?php echo saf_t( 'sc_default_note' ); ?></p>
                    </td>
                </tr>
            </table>

            <h3 style="margin-top:32px;padding-top:16px;border-top:1px solid #c3c4c7;"><?php echo saf_t( 'sc_dev_section' ); ?></h3>
            <p class="description"><?php echo saf_t( 'sc_dev_desc' ); ?></p>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php echo saf_t( 'sc_dev_profiles' ); ?></th>
                    <td>
                        <?php foreach ( $dev_platforms as $key => $meta ) :
                            $checked = $all_dev_on || in_array( $key, (array) $dev_enabled, true );
                            $url_val = $dev_urls[ $key ] ?? '';
                        ?>
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;min-width:160px;">
                                <input type="checkbox"
                                       name="saf_sc_settings[dev_enabled][]"
                                       value="<?php echo esc_attr( $key ); ?>"
                                       <?php checked( $checked ); ?>>
                                <span style="display:inline-flex;align-items:center;gap:6px;">
                                    <span style="width:12px;height:12px;border-radius:50%;background:<?php echo esc_attr( $meta['color'] ); ?>;display:inline-block;flex-shrink:0;"></span>
                                    <strong><?php echo esc_html( $meta['label'] ); ?></strong>
                                </span>
                            </label>
                            <input type="url"
                                   name="saf_sc_settings[dev_urls][<?php echo esc_attr( $key ); ?>]"
                                   value="<?php echo esc_url( $url_val ); ?>"
                                   placeholder="<?php echo esc_attr( $meta['placeholder'] ); ?>"
                                   class="regular-text" style="flex:1;">
                        </div>
                        <?php endforeach; ?>
                        <p class="description" style="margin-top:4px;"><?php echo saf_t( 'sc_dev_url_note' ); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button( saf_t( 'btn_save' ) ); ?>
        </form>

        <?php elseif ( $tab === 'advanced' ) : ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'saf_adv_group' ); ?>
            <h2><?php echo saf_t( 'adv_title' ); ?></h2>

            <h3>📧 <?php echo saf_t( 'adv_email' ); ?></h3>
            <p class="description"><?php echo saf_t( 'adv_email_desc' ); ?></p>
            <table class="form-table">
                <tr>
                    <th><label for="saf_smtp_name"><?php echo saf_t( 'adv_from_name' ); ?></label></th>
                    <td>
                        <input type="text" id="saf_smtp_name"
                               name="saf_adv_settings[smtp_from_name]"
                               value="<?php echo esc_attr( $adv['smtp_from_name'] ?? '' ); ?>"
                                class="regular-text" placeholder="<?php echo esc_attr( saf_t( 'adv_from_name_ph' ) ); ?>">
                         <p class="description"><?php echo saf_t( 'adv_from_name_desc' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="saf_smtp_email"><?php echo saf_t( 'adv_from_email' ); ?></label></th>
                    <td>
                        <input type="email" id="saf_smtp_email"
                               name="saf_adv_settings[smtp_from_email]"
                               value="<?php echo esc_attr( $adv['smtp_from_email'] ?? '' ); ?>"
                                class="regular-text" placeholder="<?php echo esc_attr( saf_t( 'adv_from_email_ph' ) ); ?>">
                        <p class="description"><?php echo saf_t( 'adv_from_email_desc' ); ?></p>
                    </td>
                </tr>
            </table>

            <h3>💬 <?php echo saf_t( 'adv_comments' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="saf_disable_comments"><?php echo saf_t( 'adv_comments_label' ); ?></label></th>
                    <td>
                        <label>
                            <input type="checkbox" id="saf_disable_comments"
                                   name="saf_adv_settings[disable_comments]"
                                   value="1" <?php checked( ! empty( $adv['disable_comments'] ) ); ?>>
                            <?php echo saf_t( 'adv_comments_label_check' ); ?>
                        </label>
                        <p class="description"><?php echo saf_t( 'adv_comments_desc' ); ?></p>
                    </td>
                </tr>
            </table>

            <h3>🔒 <?php echo saf_t( 'adv_hsts' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="saf_hsts"><?php echo saf_t( 'adv_hsts_label' ); ?></label></th>
                    <td>
                        <label>
                            <input type="checkbox" id="saf_hsts"
                                   name="saf_adv_settings[hsts_enabled]"
                                   value="1" <?php checked( ! empty( $adv['hsts_enabled'] ) ); ?>>
                            <?php echo saf_t( 'adv_hsts_label_check' ); ?>
                        </label>
                        <p class="description"><?php echo saf_t( 'adv_hsts_warn' ); ?></p>
                    </td>
                </tr>
            </table>

            <h3><?php echo saf_t( 'adv_guide_hide' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="saf_hide_progetto"><?php echo saf_t( 'adv_guide_hide' ); ?></label></th>
                    <td>
                        <label>
                            <input type="checkbox" id="saf_hide_progetto"
                                   name="saf_adv_settings[hide_progetto]"
                                   value="1" <?php checked( ! empty( $adv['hide_progetto'] ) ); ?>>
                            <?php echo saf_t( 'adv_guide_hide_check' ); ?>
                        </label>
                        <p class="description"><?php echo saf_t( 'adv_guide_hide_desc' ); ?></p>
                    </td>
                </tr>
            </table>

            <h3>🗂 <?php echo saf_t( 'adv_menu' ); ?></h3>
            <p class="description"><?php echo saf_t( 'adv_menu_desc' ); ?></p>
            <table class="form-table">
                <?php
                $menu_items = array(
                    'tools'    => saf_t( 'menu_tools' ),
                    'comments' => saf_t( 'menu_comments' ),
                    'themes'   => saf_t( 'menu_themes' ),
                    'plugins'  => saf_t( 'menu_plugins' ),
                    'users'    => saf_t( 'menu_users' ),
                    'settings' => saf_t( 'menu_settings' ),
                    'projects' => saf_t( 'menu_projects' ),
                );
                $hide = $adv['hide_menu_items'] ?? array();
                foreach ( $menu_items as $key => $label ) :
                ?>
                <tr>
                    <th><?php echo esc_html( $label ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox"
                                   name="saf_adv_settings[hide_menu_items][<?php echo $key; ?>]"
                                   value="1" <?php checked( ! empty( $hide[ $key ] ) ); ?>>
                            <?php echo sprintf( saf_t( 'adv_menu_hide_for' ), esc_html( $label ) ); ?>
                        </label>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

            <h3>➕ <?php echo saf_t( 'adv_custom_hide' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="saf_custom_hide"><?php echo saf_t( 'adv_custom_hide' ); ?></label></th>
                    <td>
                        <textarea id="saf_custom_hide" name="saf_adv_settings[custom_hide]"
                                  rows="3" class="large-text code"
                                  placeholder="<?php echo esc_attr( saf_t( 'adv_custom_ph' ) ); ?>"><?php
                            echo esc_textarea( $adv['custom_hide'] ?? '' );
                        ?></textarea>
                        <p class="description"><?php echo saf_t( 'adv_custom_desc' ); ?></p>
                    </td>
                </tr>
            </table>

            <h3>🖼 <?php echo saf_t( 'adv_svg' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="saf_enable_svg"><?php echo saf_t( 'adv_svg_label' ); ?></label></th>
                    <td>
                        <label>
                            <input type="checkbox" id="saf_enable_svg"
                                   name="saf_adv_settings[enable_svg]"
                                   value="1" <?php checked( ! empty( $adv['enable_svg'] ) ); ?>>
                            <?php echo saf_t( 'adv_svg_check' ); ?>
                        </label>
                        <p class="description" style="color:#856404;background:#fff3cd;padding:6px 10px;border-radius:4px"><?php echo saf_t( 'adv_svg_warn' ); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button( saf_t( 'btn_save_adv' ) ); ?>
        </form>

        <!-- Sezione disinstallazione (fuori dal form principale) -->
        <hr style="margin-top:24px">

        <h3>🗑 <?php echo saf_t( 'btn_cleanup' ); ?></h3>
        <p class="description"><?php echo saf_t( 'btn_cleanup_desc' ); ?></p>
        <form method="post">
            <?php wp_nonce_field( 'saf_cleanup', 'saf_cleanup_nonce' ); ?>
            <input type="hidden" name="saf_action" value="cleanup_options">
            <?php submit_button( saf_t( 'btn_cleanup' ), 'delete', 'saf_cleanup_btn', false ); ?>
        </form>

        <?php
        // Gestione cleanup manuale
        if ( ! empty( $_POST['saf_action'] ) && 'cleanup_options' === $_POST['saf_action'] ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( saf_t( 'err_permission' ) );
            }
            check_admin_referer( 'saf_cleanup', 'saf_cleanup_nonce' );
            saf_cleanup_options();
            echo '<div class="notice notice-success"><p>' . saf_t( 'err_cleaned' ) . '</p></div>';
        }
        ?>
        <?php elseif ( $tab === 'child' ) : ?>
        <?php
        // Child theme attivo (se diverso dal parent)
        $active_child = ( get_template() !== get_stylesheet() ) ? get_stylesheet() : false;

        // Se c'è un child theme attivo diverso da amar-design, SAF lo gestisce
        if ( $active_child && $active_child !== 'amar-design' ) {
            $child_dir      = get_theme_root() . '/' . $active_child . '/';
            $child_url      = get_theme_root_uri() . '/' . $active_child . '/';
            $managed_name   = $active_child;
            $is_detected    = true;
        } else {
            $child_dir      = get_theme_root() . '/amar-design/';
            $child_url      = get_theme_root_uri() . '/amar-design/';
            $managed_name   = 'amar-design';
            $is_detected    = false;
        }
        $exists    = is_dir( $child_dir );
        $css_file  = $child_dir . 'style.css';

        // Salvataggio style.css — header fields + CSS body
        if ( ! empty( $_POST['saf_action'] ) && 'child_save_css' === $_POST['saf_action'] && check_admin_referer( 'saf_child_css', 'saf_child_nonce' ) ) {
            // Usiamo manage_options invece di edit_themes perché DISALLOW_FILE_EDIT
            // (attivo in security.php) rimuove edit_themes anche agli amministratori.
            if ( current_user_can( 'manage_options' ) ) {
                $h = array_map( 'sanitize_text_field', wp_unslash( $_POST['saf_css_h'] ?? array() ) );
                $body = wp_unslash( $_POST['saf_css_body'] ?? '' );
                $css = "/*\n";
                $css .= " Theme Name:     " . ( $h['theme_name'] ?? 'SAF Child Theme' ) . "\n";
                $css .= " Theme URI:      " . ( $h['theme_uri'] ?? home_url('/') ) . "\n";
                $css .= " Description:    " . ( $h['description'] ?? '' ) . "\n";
                $css .= " Author:         " . ( $h['author'] ?? '' ) . "\n";
                $css .= " Author URI:     " . ( $h['author_uri'] ?? '' ) . "\n";
                $css .= " Template:       " . ( $h['template'] ?? 'YOUR-THEME' ) . "\n";
                $css .= " Version:        " . ( $h['version'] ?? '1.0.0' ) . "\n";
                $css .= " Text Domain:    " . ( $h['text_domain'] ?? 'saf-child' ) . "\n";
                $css .= "*/\n\n";
                $css .= $body;
                clearstatcache();
                wp_mkdir_p( $child_dir );
                $written = saf_write_file( $css_file, $css );
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    global $wp_filesystem;
                    $fs_driver  = $wp_filesystem ? get_class( $wp_filesystem ) : 'n/a (WP_Filesystem non inizializzato)';
                    $perms      = file_exists( $css_file ) ? substr( sprintf( '%o', @fileperms( $css_file ) ), -4 ) : 'N/A';
                    $is_wr      = file_exists( $css_file ) ? var_export( is_writable( $css_file ), true ) : 'N/A';
                    $owner_name = ( file_exists( $css_file ) && function_exists( 'posix_getpwuid' ) )
                                  ? ( @posix_getpwuid( @fileowner( $css_file ) )['name'] ?? 'n/a' )
                                  : 'posix non disponibile';
                    $php_user   = function_exists( 'get_current_user' ) ? get_current_user() : 'n/a';
                    error_log( '[SAF write debug] written=' . var_export( $written, true )
                        . ' | file=' . $css_file
                        . ' | perms=' . $perms
                        . ' | is_writable=' . $is_wr
                        . ' | owner=' . $owner_name
                        . ' | php_user=' . $php_user
                        . ' | fs_driver=' . $fs_driver
                    );
                }
                if ( $written ) {
                    echo '<div class="notice notice-success is-dismissible"><p>' . saf_t( 'err_css_updated' ) . '</p></div>';
                } else {
                    echo '<div class="notice notice-error is-dismissible"><p>' . sprintf( saf_t( 'err_css_write_fail' ), esc_html( $css_file ) ) . '</p></div>';
                }
                clearstatcache();
            }
        }

        // Creazione automatica dal notice dashboard (GET con nonce)
        if ( ! empty( $_GET['saf_auto_create'] ) && check_admin_referer( 'saf_auto_create', 'saf_auto_create_nonce' ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                clearstatcache();
                $result = saf_create_child_theme( true );
                if ( $result === 'success' ) {
                    $parent = saf_auto_parent_theme();
                    if ( file_exists( $css_file ) ) {
                        $content = file_get_contents( $css_file );
                        $content = preg_replace( '/^Template:\s*.+$/m', 'Template: ' . $parent, $content );
                        saf_write_file( $css_file, $content );
                    }
                    clearstatcache();
                    $exists = is_dir( $child_dir );
                    echo '<div class="notice notice-success is-dismissible"><p>' . saf_t( 'err_child_created' ) . '</p></div>';
                } else {
                    echo '<div class="notice notice-error is-dismissible"><p>❌ Creazione fallita: ' . esc_html( $result ) . '</p></div>';
                }
            }
        }

        // Creazione child theme (forza sovrascrittura file mancanti)
        if ( ! empty( $_POST['saf_action'] ) && 'child_create' === $_POST['saf_action'] && check_admin_referer( 'saf_child_create', 'saf_child_create_nonce' ) ) {
            // Usiamo manage_options: install_themes può essere rimossa da DISALLOW_FILE_MODS.
            if ( current_user_can( 'manage_options' ) ) {
                clearstatcache();
                $result = saf_create_child_theme( true );
                if ( $result === 'success' ) {
                    $parent = saf_auto_parent_theme();
                    if ( file_exists( $css_file ) ) {
                        $content = file_get_contents( $css_file );
                        $content = preg_replace( '/^Template:\s*.+$/m', 'Template: ' . $parent, $content );
                        saf_write_file( $css_file, $content );
                    }
                    clearstatcache();
                    $exists = is_dir( $child_dir );
                    echo '<div class="notice notice-success is-dismissible"><p>' . saf_t( 'err_child_created' ) . '</p></div>';
                } elseif ( $result === 'error_source_missing' ) {
                    echo '<div class="notice notice-error is-dismissible"><p>❌ Directory sorgente <code>child-theme/</code> non trovata in:<br><code>' . esc_html( SAF_DIR ) . '</code></p><p>Verifica che la cartella <code>child-theme/</code> sia presente dentro <code>saf/</code>.</p></div>';
                } elseif ( $result === 'error_mkdir' ) {
                    echo '<div class="notice notice-error is-dismissible"><p>❌ Impossibile creare <code>' . esc_html( $child_dir ) . '</code>.</p><p>Verifica permessi di scrittura su <code>/wp-content/themes/</code>.</p></div>';
                } elseif ( strpos( $result, 'error_copy_' ) === 0 ) {
                    $file = str_replace( 'error_copy_', '', $result );
                    echo '<div class="notice notice-error is-dismissible"><p>❌ Errore copia file: <code>' . esc_html( $file ) . '</code>.</p><p>Verifica permessi su <code>' . esc_html( $child_dir ) . '</code>.</p></div>';
                } else {
                    echo '<div class="notice notice-error is-dismissible"><p>❌ Errore sconosciuto: ' . esc_html( $result ) . '</p></div>';
                }
            }
        }

        // Ricreazione forzata child theme (cancella e ricrea da zero) — solo per amar-design
        if ( ! empty( $_POST['saf_action'] ) && 'child_recreate' === $_POST['saf_action'] && check_admin_referer( 'saf_child_recreate', 'saf_child_recreate_nonce' ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                if ( $managed_name !== 'amar-design' ) {
                    echo '<div class="notice notice-error is-dismissible"><p>❌ Reset disponibile solo per il child theme preconfigurato <code>amar-design</code>.</p></div>';
                } else {
                    $child_dir = get_theme_root() . '/amar-design/';
                    if ( is_dir( $child_dir ) ) {
                        $it = new RecursiveDirectoryIterator( $child_dir, RecursiveDirectoryIterator::SKIP_DOTS );
                        $files = new RecursiveIteratorIterator( $it, RecursiveIteratorIterator::CHILD_FIRST );
                        foreach ( $files as $f ) {
                            $f->isDir() ? @rmdir( $f->getRealPath() ) : @unlink( $f->getRealPath() );
                        }
                        @rmdir( $child_dir );
                    }
                    delete_option( 'saf_child_auto_created' );
                    $result = saf_create_child_theme( true );
                    if ( $result === 'success' ) {
                        $parent = saf_auto_parent_theme();
                        if ( file_exists( $css_file ) ) {
                            $content = file_get_contents( $css_file );
                            $content = preg_replace( '/^Template:\s*.+$/m', 'Template: ' . $parent, $content );
                            saf_write_file( $css_file, $content );
                        }
                        $exists = is_dir( $child_dir );
                    update_option( 'saf_child_auto_created', true );
                    clearstatcache();
                    $exists = is_dir( $child_dir );
                    echo '<div class="notice notice-success is-dismissible"><p>✅ Child theme ricreato da zero.</p></div>';
                    } else {
                        echo '<div class="notice notice-error is-dismissible"><p>❌ Errore ricreazione: ' . esc_html( $result ) . '</p></div>';
                    }
                }
            }
        }

        // Debug: test scrittura CSS (solo se WP_DEBUG attivo)
        $saf_write_test_result = null;
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG
             && ! empty( $_POST['saf_action'] ) && 'child_debug_write_test' === $_POST['saf_action']
             && check_admin_referer( 'saf_child_debug_test', 'saf_debug_nonce' )
             && current_user_can( 'manage_options' ) ) {

            clearstatcache();
            $perms      = file_exists( $css_file ) ? substr( sprintf( '%o', @fileperms( $css_file ) ), -4 ) : 'N/A';
            $is_wr      = file_exists( $css_file ) ? is_writable( $css_file ) : null;
            $owner_name = ( file_exists( $css_file ) && function_exists( 'posix_getpwuid' ) )
                          ? ( @posix_getpwuid( @fileowner( $css_file ) )['name'] ?? 'n/a' )
                          : 'posix non disponibile';
            $php_user   = function_exists( 'get_current_user' ) ? get_current_user() : 'n/a';
            $dir_wr     = is_writable( $child_dir );

            // Tentativo reale di scrittura (legge → riscrive contenuto identico).
            // saf_write_file() inizializza WP_Filesystem internamente solo quando serve.
            $test_written = false;
            $test_error   = '';
            if ( file_exists( $css_file ) ) {
                $backup = file_get_contents( $css_file );
                if ( $backup !== false ) {
                    $test_written = saf_write_file( $css_file, $backup );
                    if ( ! $test_written ) {
                        $test_error = 'saf_write_file() ha restituito false';
                    }
                } else {
                    $test_error = 'file_get_contents() ha restituito false';
                }
            } else {
                $test_error = 'style.css non esiste in ' . $css_file;
            }

            // Leggi il driver usato dopo che saf_write_file() ha inizializzato WP_Filesystem.
            global $wp_filesystem;
            $fs_driver = $wp_filesystem ? get_class( $wp_filesystem ) : 'n/a (fallback diretto)';

            $saf_write_test_result = compact(
                'fs_driver', 'perms', 'is_wr', 'owner_name', 'php_user',
                'dir_wr', 'test_written', 'test_error'
            );
        }

        // Parse existing CSS
        $current_css = '';
        clearstatcache();
        if ( $exists && file_exists( $css_file ) && is_readable( $css_file ) ) {
            $raw = file_get_contents( $css_file );
            if ( $raw !== false ) {
                $current_css = $raw;
            }
        }
        $h_defaults = array(
            'theme_name'  => 'SAF Child Theme',
            'theme_uri'   => home_url('/'),
            'description' => 'Child theme per SAF',
            'author'      => 'Il tuo nome / agenzia',
            'author_uri'  => home_url('/'),
            'template'    => 'YOUR-THEME',
            'version'     => '1.0.0',
            'text_domain' => 'saf-child',
        );
        $h_values = $h_defaults;
        $css_body = $current_css;
        if ( preg_match( '/\/\*+\s*\n(.+?)\*+\//s', $current_css, $m ) ) {
            $css_body = trim( substr( $current_css, strlen( $m[0] ) ) );
            foreach ( $h_defaults as $key => $default ) {
                $label = str_replace( '_', ' ', ucwords( str_replace( '_', ' ', $key ) ) );
                if ( preg_match( '/^\s*' . preg_quote( $label, '/' ) . ':\s*(.+)/mi', $m[1], $fm ) ) {
                    $h_values[ $key ] = trim( $fm[1] );
                }
            }
        }
        $h_explain = array(
            'theme_name'  => 'Il nome del tema come appare in Aspetto → Temi.',
            'theme_uri'   => 'URL del sito web del tema (es. la tua pagina portfolio o GitHub).',
            'description' => 'Breve descrizione del tema (opzionale ma consigliata).',
            'author'      => 'Il tuo nome o il nome dell\'agenzia.',
            'author_uri'  => 'URL del tuo sito web o portfolio.',
            'template'    => '⚠️ Nome ESATTO della CARTELLA del tema principale (es. Divi, astra, twentytwentyfour). Obbligatorio.',
            'version'     => 'Versione corrente del child theme. Incrementa a ogni modifica significativa.',
            'text_domain' => 'Prefisso per le traduzioni del tema. Lascia saf-child se non usi traduzioni.',
        );
        ?>
        <h2><?php echo saf_t( 'child_title' ); ?></h2>

        <?php if ( ! saf_is_notice_dismissed( 'child_info' ) ) : ?>
        <div class="notice notice-warning is-dismissible saf-dismiss-custom" style="border-left-color:#e68a2e" data-saf-dismiss="child_info">
            <p><strong>🔒 Proteggi le tue personalizzazioni</strong></p>
            <p style="margin:4px 0">Modificare i file del tema principale (Divi, Astra, Twenty Twenty-Four…) è rischioso: <strong>alla prossima
            uscita del tema, tutte le modifiche vengono sovrascritte</strong>. Un child theme ti permette di:</p>
            <ul style="margin:2px 0 8px 20px;list-style:disc">
                <li>Modificare header, footer e template PHP senza toccare il tema originale</li>
                <li>Sovrascrivere CSS e JavaScript senza perdere nulla agli aggiornamenti</li>
                <li>Aggiungere funzioni personalizzate in <code>functions.php</code></li>
            </ul>
            <p style="margin:4px 0"><strong>SAF</strong> include un child theme preconfigurato (<code>amar-design</code>) con cartelle
            <code>inc/</code>, <code>css/</code>, <code>js/</code> e compatibilità Divi opzionale. Lo crei con un clic, poi imposti il
            <code>Template:</code> giusto e scegli nome/autore.</p>
        </div>
        <?php endif; ?>

        <?php if ( ! saf_is_notice_dismissed( 'child_activate' ) ) : ?>
        <div class="notice notice-info is-dismissible saf-dismiss-custom" style="border-left-color:#f47D39" data-saf-dismiss="child_activate">
            <p><strong>⚠️ <?php echo saf_t( 'child_activate_warn' ); ?></strong></p>
        </div>
        <?php endif; ?>

        <?php if ( $is_detected ) : ?>
        <div class="notice notice-success" style="border-left-color:#2ea3f2">
            <p><strong>🔍 <?php echo saf_t( 'child_detected' ); ?></strong></p>
        </div>
        <?php endif; ?>

        <?php if ( ! saf_is_notice_dismissed( 'cache_note' ) ) : ?>
        <div class="notice notice-warning is-dismissible saf-dismiss-custom" style="border-left-color:#f47D39;font-size:12px" data-saf-dismiss="cache_note">
            <p><strong>⏳ Nota sulla cache del server:</strong> Se dopo aver creato o modificato il child theme non vedi subito le modifiche, attendi qualche secondo e <strong>ricarica la pagina</strong>. <br>OPcache e la cache PHP del server possono ritardare la propagazione dei file. Il child theme è stato comunque creato/salvato sul filesystem.</p>
        </div>
        <?php endif; ?>

        <script>
        (function(){
            var notices = document.querySelectorAll('.saf-dismiss-custom');
            notices.forEach(function(n){
                var key = n.getAttribute('data-saf-dismiss');
                if ( localStorage.getItem('saf_dismissed_' + key) ) {
                    n.style.display = 'none';
                    return;
                }
                var btn = n.querySelector('.notice-dismiss');
                if ( ! btn ) {
                    btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'notice-dismiss';
                    btn.innerHTML = '<span class="screen-reader-text">Ignora questa notifica</span>';
                    n.appendChild(btn);
                }
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    n.style.display = 'none';
                    localStorage.setItem('saf_dismissed_' + key, '1');
                    var fd = new FormData();
                    fd.append('action', 'saf_dismiss_notice');
                    fd.append('key', key);
                    fd.append('_ajax_nonce', saf_admin.dismiss_nonce);
                    navigator.sendBeacon(ajaxurl, fd);
                });
            });
        })();
        </script>

        <?php if ( ! $exists ) : ?>
            <form method="post">
                <?php wp_nonce_field( 'saf_child_create', 'saf_child_create_nonce' ); ?>
                <input type="hidden" name="saf_action" value="child_create">
                <p class="submit"><input type="submit" name="saf_do_create" class="button button-primary" value="<?php echo esc_attr( saf_t( 'child_create_btn' ) ); ?>"></p>
            </form>
        <?php else : ?>
            <p class="description">
                <?php echo sprintf( saf_t( 'child_exists' ), '<code>' . esc_html( $child_dir ) . '</code>' ); ?>
            </p>

            <?php if ( ! $is_detected ) : ?>
            <form method="post" style="margin-top:12px;margin-bottom:20px;padding:12px;background:#fff3cd;border-radius:4px">
                <?php wp_nonce_field( 'saf_child_recreate', 'saf_child_recreate_nonce' ); ?>
                <input type="hidden" name="saf_action" value="child_recreate">
                <p style="margin:0 0 6px"><strong>⚠️ Reset completo</strong> — cancella tutto il child theme e lo ricrea da zero.</p>
                <p style="margin:0 0 6px;font-size:12px;color:#666">Perdi tutte le modifiche a style.css, inc, js, css, functions.php, screenshot.png</p>
                <input type="submit" class="button button-secondary"
                       value="🗑 Resetta e ricrea child theme"
                        onclick="if ( ! confirm( '⛔ Sei sicuro? Questa azione cancellerà TUTTO il child theme (style.css, functions.php, js/, css/, inc/).' ) ) return false; if ( ! confirm( '⚠️ CONFERMA DEFINITIVA: vuoi davvero resettare il child theme <?php echo $managed_name; ?>? I dati cancellati non sono recuperabili.' ) ) return false;">
            </form>
            <?php endif; ?>

            <form method="post" style="margin-bottom:24px">
                <?php wp_nonce_field( 'saf_child_css', 'saf_child_nonce' ); ?>
                <input type="hidden" name="saf_action" value="child_save_css">

                <h3><?php echo saf_t( 'child_css_header' ); ?></h3>
                <p class="description"><?php echo saf_t( 'child_css_header_desc' ); ?></p>
                <table class="form-table" style="margin-bottom:20px">
                <?php foreach ( $h_defaults as $key => $default ) : ?>
                    <tr>
                        <th style="width:140px">
                            <label for="saf_css_h_<?php echo $key; ?>">
                                <?php echo esc_html( str_replace( '_', ' ', ucwords( str_replace( '_', ' ', $key ) ) ) ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" id="saf_css_h_<?php echo $key; ?>"
                                   name="saf_css_h[<?php echo $key; ?>]"
                                   value="<?php echo esc_attr( $h_values[ $key ] ?? $default ); ?>"
                                   class="regular-text" style="width:100%;max-width:500px;font-family:monospace">
                            <p class="description" style="margin:2px 0 0"><?php echo esc_html( $h_explain[ $key ] ?? '' ); ?></p>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </table>

                        <div style="background:#f9f9f9;padding:8px 12px;border-radius:4px;margin-bottom:8px;font-size:12px">
                    <?php
                    clearstatcache();
                    $dbg_exists  = file_exists( $css_file ) ? '✅ file_exists' : '❌ file_exists';
                    $dbg_read    = is_readable( $css_file ) ? '✅ is_readable' : '❌ is_readable';
                    $dbg_size    = file_exists( $css_file ) ? filesize( $css_file ) . ' bytes' : 'N/A';
                    $dbg_content = ( ! empty( $current_css ) ) ? '✅ ' . strlen( $current_css ) . ' letti' : '❌ VUOTO';
                    echo "<strong>Debug:</strong> $dbg_exists | $dbg_read | $dbg_size | $dbg_content";
                    ?>
                </div>

                <h3><code>/* style.css */</code> — CSS rules</h3>
                <p class="description"><?php echo saf_t( 'child_css_warn' ); ?></p>
                <textarea id="saf_css_body" name="saf_css_body"
                          rows="20" class="large-text code saf-code-editor"><?php
                    echo esc_textarea( $css_body );
                ?></textarea>
                <p class="description" style="margin-top:4px">
                    <?php echo saf_t( 'child_css_shortcuts' ); ?>
                </p>

                <?php submit_button( saf_t( 'child_save_css' ), 'primary', 'saf_save_css_btn', false ); ?>
            </form>

            <?php
            // functions.php viewer
            $functions_file = $child_dir . 'functions.php';
            $functions_content = file_exists( $functions_file ) ? file_get_contents( $functions_file ) : '';
            ?>
            <h3><?php echo saf_t( 'child_functions_title' ); ?></h3>
            <p class="description"><?php echo saf_t( 'child_functions_desc' ); ?></p>
            <textarea rows="15" class="large-text code saf-code-editor" readonly><?php echo esc_textarea( $functions_content ); ?></textarea>

        <?php endif; ?>

        <details style="margin-top:20px;padding:12px;background:#f0f0f1;border-radius:4px">
            <summary style="cursor:pointer;font-weight:bold">🔍 Diagnostica child theme</summary>
            <table class="widefat striped" style="margin-top:8px">
                <tr><td><strong>SAF_DIR</strong></td><td><code><?php echo esc_html( SAF_DIR ); ?></code></td></tr>
                <tr><td><strong>Source child-theme/</strong></td><td><code><?php echo esc_html( SAF_DIR . 'child-theme/' ); ?></code></td></tr>
                <tr><td><strong>Source esiste?</strong></td><td><?php echo is_dir( SAF_DIR . 'child-theme/' ) ? '✅ Sì' : '❌ NO'; ?></td></tr>
                <tr><td><strong>Style.css sorgente?</strong></td><td><?php echo file_exists( SAF_DIR . 'child-theme/style.css' ) ? '✅ Sì' : '❌ NO'; ?></td></tr>
                <tr><td><strong>Screenshot.png sorgente?</strong></td><td><?php echo file_exists( SAF_DIR . 'child-theme/screenshot.png' ) ? '✅ Sì' : '❌ NO'; ?></td></tr>
                <tr><td><strong>Theme root</strong></td><td><code><?php echo esc_html( get_theme_root() ); ?></code></td></tr>
                <tr><td><strong>Destinazione</strong></td><td><code><?php echo esc_html( $child_dir ); ?></code></td></tr>
                <tr><td><strong>Destinazione esiste?</strong></td><td><?php echo is_dir( $child_dir ) ? '✅ Sì' : '❌ NO'; ?></td></tr>
                <tr><td><strong>Parent attivo</strong></td><td><code><?php echo esc_html( saf_auto_parent_theme() ); ?></code></td></tr>
                <tr><td><strong>Auto-created option</strong></td><td><?php echo get_option( 'saf_child_auto_created', false ) ? '✅ settata' : '❌ non settata'; ?></td></tr>
                <tr><td><strong>PHP version</strong></td><td><?php echo PHP_VERSION; ?></td></tr>
                <tr><td><strong>Scrittura themes/</strong></td><td><?php echo is_writable( get_theme_root() ) ? '✅ Scrivibile' : '❌ NON scrivibile'; ?></td></tr>
                <tr><td colspan="2" style="background:#f0f0f1;font-weight:bold;padding:6px 10px">wp-config.php — costanti debug/cache</td></tr>
                <tr><td><strong>WP_DEBUG</strong></td><td><?php echo ( defined('WP_DEBUG') && WP_DEBUG ) ? '✅ true' : '❌ false / non definita'; ?></td></tr>
                <tr><td><strong>WP_DEBUG_LOG</strong></td><td><?php echo ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) ? '✅ true' : '❌ false / non definita'; ?></td></tr>
                <tr><td><strong>WP_DEBUG_DISPLAY</strong></td><td><?php echo ( defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ) ? '⚠️ true (warning visibili in pagina)' : '✅ false / non definita'; ?></td></tr>
                <tr><td><strong>WP_CACHE</strong></td><td><?php echo ( defined('WP_CACHE') && WP_CACHE ) ? '⚠️ true (cache attiva)' : '✅ false / non definita'; ?></td></tr>
            </table>
            <p style="margin:8px 0 0;color:#666;font-size:12px">
                <a href="<?php echo esc_url( add_query_arg( 'saf_reset_child', '1' ) ); ?>" class="button button-small" onclick="return confirm('Resetta option child theme? La ricreazione partirà al prossimo page load.');">🔄 Reset auto-create</a>
                &nbsp;Copia questi dati se devi riportare un bug.
            </p>
        </details>

        <?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG && $exists ) : ?>
        <details style="margin-top:12px;padding:12px;background:#fff3cd;border:1px solid #ffc107;border-radius:4px" <?php echo $saf_write_test_result !== null ? 'open' : ''; ?>>
            <summary style="cursor:pointer;font-weight:bold">🐛 Debug scrittura CSS <small style="font-weight:normal;color:#856404">(visibile solo con WP_DEBUG=true)</small></summary>
            <table class="widefat striped" style="margin-top:10px">
                <?php
                clearstatcache();
                // Non inizializziamo WP_Filesystem qui — causerebbe il prompt FTP su hosting condiviso.
                // Mostriamo solo le info disponibili tramite funzioni native PHP.
                global $wp_filesystem;
                $dbg_fs      = $wp_filesystem ? get_class( $wp_filesystem ) : '⚠️ non ancora inizializzato (normale se non hai ancora salvato)';
                $dbg_perms   = file_exists( $css_file ) ? substr( sprintf( '%o', @fileperms( $css_file ) ), -4 ) : 'N/A';
                $dbg_iswr    = file_exists( $css_file ) ? ( is_writable( $css_file ) ? '✅ Sì' : '❌ NO' ) : 'N/A';
                $dbg_dirwr   = is_writable( $child_dir ) ? '✅ Sì' : '❌ NO';
                $dbg_owner   = ( file_exists( $css_file ) && function_exists( 'posix_getpwuid' ) )
                               ? ( @posix_getpwuid( @fileowner( $css_file ) )['name'] ?? 'n/a' )
                               : 'posix non disponibile';
                $dbg_phpuser = function_exists( 'get_current_user' ) ? get_current_user() : 'n/a';
                ?>
                <tr><td style="width:220px"><strong>WP_Filesystem driver</strong></td><td><code><?php echo esc_html( $dbg_fs ); ?></code></td></tr>
                <tr><td><strong>style.css permessi</strong></td><td><code><?php echo esc_html( $dbg_perms ); ?></code></td></tr>
                <tr><td><strong>style.css is_writable()</strong></td><td><?php echo $dbg_iswr; ?></td></tr>
                <tr><td><strong>Directory themes/<code><?php echo $managed_name; ?></code>/ scrivibile</strong></td><td><?php echo $dbg_dirwr; ?></td></tr>
                <tr><td><strong>Proprietario file (owner)</strong></td><td><code><?php echo esc_html( $dbg_owner ); ?></code></td></tr>
                <tr><td><strong>Utente PHP corrente</strong></td><td><code><?php echo esc_html( $dbg_phpuser ); ?></code></td></tr>
                <tr><td><strong>Path style.css</strong></td><td><code><?php echo esc_html( $css_file ); ?></code></td></tr>
            </table>

            <?php if ( $saf_write_test_result !== null ) : ?>
            <div style="margin-top:10px;padding:10px;border-radius:4px;background:<?php echo $saf_write_test_result['test_written'] ? '#d1e7dd' : '#f8d7da'; ?>;border:1px solid <?php echo $saf_write_test_result['test_written'] ? '#0f5132' : '#842029'; ?>">
                <strong><?php echo $saf_write_test_result['test_written'] ? '✅ Test scrittura RIUSCITO' : '❌ Test scrittura FALLITO'; ?></strong><br>
                <?php if ( ! $saf_write_test_result['test_written'] ) : ?>
                <code style="font-size:12px"><?php echo esc_html( $saf_write_test_result['test_error'] ); ?></code><br>
                <?php endif; ?>
                <small>
                    Driver: <code><?php echo esc_html( $saf_write_test_result['fs_driver'] ); ?></code> |
                    Permessi: <code><?php echo esc_html( $saf_write_test_result['perms'] ); ?></code> |
                    is_writable: <?php echo $saf_write_test_result['is_wr'] ? 'true' : 'false'; ?> |
                    Owner: <code><?php echo esc_html( $saf_write_test_result['owner_name'] ); ?></code> |
                    PHP user: <code><?php echo esc_html( $saf_write_test_result['php_user'] ); ?></code>
                </small>
            </div>
            <?php endif; ?>

            <?php
            // Stato debug.log
            $log_file = WP_CONTENT_DIR . '/debug.log';
            if ( file_exists( $log_file ) ) {
                $log_size    = size_format( filesize( $log_file ) );
                $log_mtime   = date_i18n( 'd/m/Y H:i:s', filemtime( $log_file ) );
                $log_excerpt = '';
                $lines       = file( $log_file );
                if ( $lines ) {
                    $last = array_slice( $lines, -5 );
                    $log_excerpt = implode( '', $last );
                }
                echo '<div style="margin-top:10px;padding:8px 12px;background:#e8f4fd;border:1px solid #90cdf4;border-radius:4px;font-size:12px">';
                echo '📄 <strong>debug.log</strong> esiste — ' . esc_html( $log_size ) . ' — ultimo aggiornamento: ' . esc_html( $log_mtime );
                if ( $log_excerpt ) {
                    echo '<details style="margin-top:6px"><summary style="cursor:pointer">Ultime 5 righe</summary>';
                    echo '<pre style="margin:4px 0 0;font-size:11px;overflow-x:auto;background:#1e1e1e;color:#d4d4d4;padding:8px;border-radius:4px">';
                    echo esc_html( $log_excerpt );
                    echo '</pre></details>';
                }
                echo '</div>';
            } else {
                echo '<div style="margin-top:10px;padding:8px 12px;background:#fff5f5;border:1px solid #fc8181;border-radius:4px;font-size:12px">';
                echo '⚠️ <strong>debug.log non esiste ancora</strong> — verrà creato automaticamente al primo errore/log con WP_DEBUG_LOG=true.';
                echo '</div>';
            }
            ?>
            <form method="post" style="margin-top:10px">
                <?php wp_nonce_field( 'saf_child_debug_test', 'saf_debug_nonce' ); ?>
                <input type="hidden" name="saf_action" value="child_debug_write_test">
                <input type="submit" class="button button-secondary" value="🔬 Test scrittura (legge e riscrive style.css identico)">
                <p class="description" style="margin-top:4px">Non modifica il contenuto. Verifica solo se la scrittura è possibile e mostra il risultato qui sopra. I dettagli vanno anche in <code>wp-content/debug.log</code>.</p>
            </form>
        </details>
        <?php endif; ?>

        <?php elseif ( $tab === 'sistema' ) : ?>
        <h2><?php echo saf_t( 'sistema_title' ); ?></h2>
        <p class="description"><?php echo saf_t( 'sistema_desc' ); ?></p>

        <?php
        global $wpdb;
        $upload_dir  = wp_upload_dir();
        $log_file    = WP_CONTENT_DIR . '/debug.log';
        $active_plugins = get_option( 'active_plugins', array() );

        // OPcache
        $opcache_enabled = function_exists( 'opcache_get_status' ) ? @opcache_get_status( false ) : false;
        $opcache_status  = $opcache_enabled ? '✅ Attivo' : '❌ Non attivo / non disponibile';
        ?>

        <h3 style="margin-top:20px">📦 Plugin & WordPress</h3>
        <table class="widefat striped" style="margin-bottom:20px">
            <tr><td style="width:240px"><strong>SAF Version</strong></td><td><code><?php echo esc_html( SAF_VERSION ); ?></code></td></tr>
            <tr><td><strong>WordPress Version</strong></td><td><code><?php echo esc_html( get_bloginfo('version') ); ?></code></td></tr>
            <tr><td><strong>Multisite</strong></td><td><?php echo is_multisite() ? '✅ Sì' : '❌ No'; ?></td></tr>
            <tr><td><strong>Plugin attivi</strong></td><td><?php echo count( $active_plugins ); ?></td></tr>
            <tr><td><strong>Tema attivo</strong></td><td><code><?php echo esc_html( get_stylesheet() ); ?></code></td></tr>
            <tr><td><strong>Tema parent</strong></td><td><code><?php echo esc_html( get_template() ); ?></code></td></tr>
            <tr><td><strong>Lingua sito</strong></td><td><code><?php echo esc_html( get_locale() ); ?></code></td></tr>
            <tr><td><strong>WP_CONTENT_DIR</strong></td><td><code><?php echo esc_html( WP_CONTENT_DIR ); ?></code></td></tr>
            <tr><td><strong>ABSPATH</strong></td><td><code><?php echo esc_html( ABSPATH ); ?></code></td></tr>
            <tr><td><strong>Uploads dir</strong></td><td><code><?php echo esc_html( $upload_dir['basedir'] ); ?></code></td></tr>
            <tr><td><strong>Uploads scrivibile</strong></td><td><?php echo is_writable( $upload_dir['basedir'] ) ? '✅ Sì' : '❌ NO'; ?></td></tr>
        </table>

        <h3>🐘 PHP & Server</h3>
        <table class="widefat striped" style="margin-bottom:20px">
            <tr><td style="width:240px"><strong>PHP Version</strong></td><td><code><?php echo PHP_VERSION; ?></code></td></tr>
            <tr><td><strong>PHP SAPI</strong></td><td><code><?php echo esc_html( PHP_SAPI ); ?></code></td></tr>
            <tr><td><strong>Sistema operativo</strong></td><td><code><?php echo esc_html( PHP_OS ); ?></code></td></tr>
            <tr><td><strong>Server software</strong></td><td><code><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ?? 'n/a' ); ?></code></td></tr>
            <tr><td><strong>memory_limit</strong></td><td><code><?php echo esc_html( ini_get('memory_limit') ); ?></code> — in uso: <code><?php echo esc_html( size_format( memory_get_usage(true) ) ); ?></code></td></tr>
            <tr><td><strong>max_execution_time</strong></td><td><code><?php echo esc_html( ini_get('max_execution_time') ); ?>s</code></td></tr>
            <tr><td><strong>upload_max_filesize</strong></td><td><code><?php echo esc_html( ini_get('upload_max_filesize') ); ?></code></td></tr>
            <tr><td><strong>post_max_size</strong></td><td><code><?php echo esc_html( ini_get('post_max_size') ); ?></code></td></tr>
            <tr><td><strong>max_input_vars</strong></td><td><code><?php echo esc_html( ini_get('max_input_vars') ); ?></code></td></tr>
            <tr><td><strong>OPcache</strong></td><td><?php echo $opcache_status; ?></td></tr>
            <tr><td><strong>allow_url_fopen</strong></td><td><?php echo ini_get('allow_url_fopen') ? '✅ On' : '❌ Off'; ?></td></tr>
            <tr><td><strong>disable_functions</strong></td><td><code style="font-size:11px;word-break:break-all"><?php $df = ini_get('disable_functions'); echo $df ? esc_html($df) : '(nessuna)'; ?></code></td></tr>
        </table>

        <h3>🗄 Database</h3>
        <table class="widefat striped" style="margin-bottom:20px">
            <tr><td style="width:240px"><strong>DB Version (MySQL/MariaDB)</strong></td><td><code><?php echo esc_html( $wpdb->db_version() ); ?></code></td></tr>
            <tr><td><strong>DB Charset</strong></td><td><code><?php echo esc_html( DB_CHARSET ); ?></code></td></tr>
            <tr><td><strong>DB Host</strong></td><td><code><?php echo esc_html( DB_HOST ); ?></code></td></tr>
            <tr><td><strong>Tabelle WP</strong></td><td><code><?php echo esc_html( $wpdb->prefix ); ?>*</code></td></tr>
        </table>

        <h3>📝 Debug & Log</h3>
        <table class="widefat striped" style="margin-bottom:20px">
            <tr><td style="width:240px"><strong>WP_DEBUG</strong></td><td><?php echo ( defined('WP_DEBUG') && WP_DEBUG ) ? '✅ true' : '❌ false'; ?></td></tr>
            <tr><td><strong>WP_DEBUG_LOG</strong></td><td><?php echo ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) ? '✅ true' : '❌ false'; ?></td></tr>
            <tr><td><strong>WP_DEBUG_DISPLAY</strong></td><td><?php echo ( defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ) ? '⚠️ true' : '✅ false'; ?></td></tr>
            <tr><td><strong>SCRIPT_DEBUG</strong></td><td><?php echo ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '⚠️ true' : '✅ false'; ?></td></tr>
            <tr><td><strong>SAVEQUERIES</strong></td><td><?php echo ( defined('SAVEQUERIES') && SAVEQUERIES ) ? '⚠️ true' : '✅ false'; ?></td></tr>
            <tr>
                <td><strong>debug.log</strong></td>
                <td>
                <?php if ( file_exists( $log_file ) ) :
                    echo '✅ Esiste — ' . esc_html( size_format( filesize( $log_file ) ) );
                    echo ' — <a href="' . esc_url( admin_url('admin.php?page=saf-dati-sito&tab=sistema&saf_flush_log=1') ) . '" onclick="return confirm(\'Svuotare debug.log?\')">🗑 Svuota</a>';
                else : ?>
                    ❌ Non esiste ancora
                <?php endif; ?>
                </td>
            </tr>
        </table>

        <h3>🔧 Estensioni PHP rilevanti</h3>
        <table class="widefat striped" style="margin-bottom:20px">
        <?php
        $ext_needed = array(
            'curl'     => 'HTTP requests',
            'json'     => 'JSON encoding/decoding',
            'mbstring' => 'Multibyte string',
            'openssl'  => 'HTTPS / encryption',
            'gd'       => 'Image processing',
            'imagick'  => 'Image processing (alternativa GD)',
            'pdo'      => 'Database abstraction',
            'mysqli'   => 'MySQL connection',
            'zip'      => 'ZIP handling',
            'dom'      => 'DOM parsing',
            'xml'      => 'XML parsing',
            'intl'     => 'Internationalization',
            'sodium'   => 'WP encryption (wp_salt)',
        );
        foreach ( $ext_needed as $ext => $desc ) :
            $loaded = extension_loaded( $ext );
        ?>
            <tr>
                <td style="width:240px"><strong><?php echo esc_html( $ext ); ?></strong> <span style="color:#666;font-size:11px"><?php echo esc_html( $desc ); ?></span></td>
                <td><?php echo $loaded ? '✅ caricata' : '❌ non caricata'; ?></td>
            </tr>
        <?php endforeach; ?>
        </table>

        <?php
        // Svuota debug.log
        if ( ! empty( $_GET['saf_flush_log'] ) && current_user_can( 'manage_options' ) ) {
            if ( file_exists( $log_file ) ) {
                file_put_contents( $log_file, '' );
                echo '<div class="notice notice-success is-dismissible"><p>🗑 debug.log svuotato.</p></div>';
            }
        }
        ?>

        <hr style="margin-top:24px">
        <h3>🧪 Self-Test</h3>
        <p class="description">Verifica che tutti i moduli SAF funzionino correttamente.</p>
        <form method="post">
            <?php wp_nonce_field( 'saf_self_test', 'saf_self_test_nonce' ); ?>
            <input type="hidden" name="saf_action" value="self_test">
            <input type="submit" class="button button-secondary" value="🔬 Esegui test">
        </form>

        <?php
        if ( ! empty( $_POST['saf_action'] ) && 'self_test' === $_POST['saf_action'] && check_admin_referer( 'saf_self_test', 'saf_self_test_nonce' ) ) {
            $results = array();
            $errors  = 0;

            // 1 — Costanti SAF
            $results[] = defined( 'SAF_DIR' ) ? '✅ SAF_DIR definito' : '❌ SAF_DIR mancante';
            $results[] = defined( 'SAF_URL' ) ? '✅ SAF_URL definito' : '❌ SAF_URL mancante';
            $results[] = defined( 'SAF_VERSION' ) ? '✅ SAF_VERSION = ' . SAF_VERSION : '❌ SAF_VERSION mancante';

            // 2 — File richiesti
            $files = array( 'saf-loader.php', 'version.php', 'admin.php', 'security.php', 'seo.php', 'helpers.php', 'performance.php', 'cleanup.php', 'dashboard.php', 'guida.php' );
            foreach ( $files as $f ) {
                $path = SAF_DIR . $f;
                $results[] = file_exists( $path ) ? '✅ <code>' . $f . '</code>' : '❌ MANCA <code>' . $f . '</code>';
                if ( ! file_exists( $path ) ) $errors++;
            }

            // 3 — Child theme source
            $child_src = SAF_DIR . 'child-theme/';
            $results[] = is_dir( $child_src ) ? '✅ child-theme/ esiste' : '❌ child-theme/ mancante';
            $results[] = file_exists( $child_src . 'style.css' ) ? '✅ child-theme/style.css' : '❌ child-theme/style.css mancante';

            // 4 — Language files
            $langs = array( SAF_DIR . 'languages/it_IT.php', SAF_DIR . 'languages/en_US.php' );
            foreach ( $langs as $lf ) {
                $results[] = file_exists( $lf ) ? '✅ ' . basename( $lf ) : '❌ MANCA ' . basename( $lf );
                if ( ! file_exists( $lf ) ) { $errors++; continue; }
                $strings = include $lf;
                if ( ! is_array( $strings ) || empty( $strings ) ) {
                    $results[] = '❌ ' . basename( $lf ) . ' non contiene array valido';
                    $errors++;
                } else {
                    $results[] = 'ℹ️ ' . basename( $lf ) . ' = ' . count( $strings ) . ' chiavi';
                }
            }

            // 5 — Tests traduzione chiavi critiche
            $must = array( 'site_data', 'tab_org', 'tab_seo', 'tab_security', 'tab_robots', 'tab_nap', 'tab_advanced', 'tab_child', 'tab_sistema', 'tab_plugins', 'tab_credits', 'err_css_updated' );
            $ok = 0; $miss = 0;
            foreach ( $must as $k ) {
                $v = saf_t( $k );
                if ( ! empty( $v ) && $v !== $k ) { $ok++; } else { $miss++; }
            }
            $results[] = $miss === 0 ? "✅ $ok chiavi obbligatorie presenti" : "⚠️ $ok trovate, $miss mancanti";
            if ( $miss > 0 ) $errors++;

            // 6 — Options registrate
            $opt_groups = array( 'saf_org_settings', 'saf_seo_settings', 'saf_sec_settings', 'saf_adv_settings', 'saf_robots_content', 'saf_nap_html', 'saf_credits_settings', 'saf_tools_settings' );
            foreach ( $opt_groups as $og ) {
                $val = get_option( $og, '__MISSING__' );
                $results[] = $val !== '__MISSING__' ? "✅ option <code>$og</code> accessibile" : "⚠️ <code>$og</code> non ancora salvata (normale se mai usata)";
            }

            // 7 — Test scrittura
            $tmp = WP_CONTENT_DIR . '/saf-test.tmp';
            $written = saf_write_file( $tmp, 'SAF test OK' );
            if ( $written && file_exists( $tmp ) ) {
                $results[] = '✅ <code>saf_write_file()</code> scrive correttamente';
                unlink( $tmp );
            } else {
                global $wp_filesystem;
                $driver = $wp_filesystem ? get_class( $wp_filesystem ) : 'nessun driver';
                $results[] = "❌ <code>saf_write_file()</code> fallito (driver: $driver)";
                $errors++;
            }

            // 8 — Costanti WP
            $results[] = defined( 'WP_DEBUG' ) && WP_DEBUG ? '⚠️ WP_DEBUG = true (normale in sviluppo)' : '✅ WP_DEBUG = false / non definito';
            $results[] = defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ? '⚠️ DISALLOW_FILE_EDIT = true (rimuove edit_themes/install_themes)' : 'ℹ️ DISALLOW_FILE_EDIT non attivo';

            // Output
            echo '<div style="margin-top:12px;padding:16px;background:' . ( $errors === 0 ? '#f0fdf4' : '#fef2f2' ) . ';border:1px solid ' . ( $errors === 0 ? '#86efac' : '#fca5a5' ) . ';border-radius:6px">';
            echo '<strong style="font-size:15px">' . ( $errors === 0 ? '✅ Tutti i test superati' : "❌ $errors test falliti" ) . '</strong>';
            echo '<table class="widefat striped" style="margin-top:10px;font-size:12px">';
            foreach ( $results as $r ) {
                echo '<tr><td style="padding:4px 8px">' . $r . '</td></tr>';
            }
            echo '</table></div>';
        }
        ?>

        <?php elseif ( $tab === 'plugins' ) : ?>
        <h2>🛠 <?php echo saf_t( 'plugins_title' ); ?></h2>
        <p class="description"><?php echo saf_t( 'plugins_desc' ); ?></p>

        <?php
        $tools = (array) get_option( 'saf_tools_settings', array() );
        $htaccess_file = ABSPATH . '.htaccess';
        ?>

        <form method="post" action="options.php">
            <?php settings_fields( 'saf_tools_group' ); ?>

            <h3>📄 .htaccess</h3>
            <table class="widefat striped" style="margin-bottom:20px">
                <?php if ( file_exists( $htaccess_file ) ) :
                    clearstatcache();
                    $htaccess = file_get_contents( $htaccess_file );
                ?>
                <tr><td><strong>Percorso</strong></td><td><code><?php echo esc_html( $htaccess_file ); ?></code></td></tr>
                <tr><td><strong>Dimensione</strong></td><td><?php echo esc_html( size_format( filesize( $htaccess_file ) ) ); ?></td></tr>
                <tr><td><strong>Ultima modifica</strong></td><td><?php echo esc_html( date_i18n( get_option('date_format') . ' ' . get_option('time_format'), filemtime( $htaccess_file ) ) ); ?></td></tr>
                <tr><td colspan="2"><textarea rows="8" class="large-text code" readonly style="background:#f0f0f1;font-size:11px"><?php echo esc_textarea( $htaccess ); ?></textarea></td></tr>
                <?php else : ?>
                <tr><td colspan="2">❌ Nessun file <code>.htaccess</code> trovato in <code><?php echo esc_html( ABSPATH ); ?></code></td></tr>
                <?php endif; ?>
            </table>

            <h3>⚡ Cache & Performance</h3>

            <h4 style="margin:0 0 6px;color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:0.05em">💻 Lato Server</h4>
            <table class="widefat striped" style="margin-bottom:16px">
                <tr><td style="width:240px"><strong>OPcache</strong></td>
                    <td><?php
                    if ( function_exists( 'opcache_get_status' ) ) {
                        $opc = @opcache_get_status( false );
                        echo $opc ? '✅ Attivo' : '❌ Non attivo';
                    } else {
                        echo '❌ Non disponibile';
                    }
                    ?></td>
                </tr>
                <tr><td><strong>Memcache / Memcached</strong></td>
                    <td><?php
                    $mem = array();
                    if ( extension_loaded( 'memcache' ) )  $mem[] = 'Memcache';
                    if ( extension_loaded( 'memcached' ) ) $mem[] = 'Memcached';
                    echo $mem ? '✅ ' . implode( ', ', $mem ) : '❌ Non rilevato';
                    ?></td>
                </tr>
                <tr><td><strong>Redis</strong></td>
                    <td><?php echo extension_loaded( 'redis' ) ? '✅ Presente' : '❌ Non rilevato'; ?></td>
                </tr>
                <tr><td><strong>APCu</strong></td>
                    <td><?php echo extension_loaded( 'apcu' ) ? '✅ Presente' : '❌ Non rilevato'; ?></td>
                </tr>
                <tr><td><strong>Object Cache (Redis/Memcached)</strong></td>
                    <td><?php echo wp_using_ext_object_cache() ? '⚠️ ' . ( wp_cache_get_last_changed() ? 'Attivo' : 'Attivo (nessun dato)' ) : '❌ Non rilevato'; ?></td>
                </tr>
                <tr><td><strong>Reverse Proxy</strong></td>
                    <td><?php
                    $proxy = array();
                    if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && strpos( $_SERVER['SERVER_SOFTWARE'], 'LiteSpeed' ) !== false ) {
                        $proxy[] = 'LiteSpeed Cache';
                    }
                    if ( isset( $_SERVER['X-Varnish'] ) || isset( $_SERVER['HTTP_X_VARNISH'] ) ) {
                        $proxy[] = 'Varnish';
                    }
                    if ( defined( 'LSCACHE_VARY_COOKIE' ) ) {
                        $proxy[] = 'LiteSpeed (definita)';
                    }
                    echo $proxy ? '⚠️ ' . implode( ', ', $proxy ) : 'ℹ️ Non rilevato';
                    ?></td>
                </tr>
                <tr><td><strong>WP_CACHE</strong></td><td><?php echo ( defined('WP_CACHE') && WP_CACHE ) ? '⚠️ Attivo' : '✅ Non attivo'; ?></td></tr>
            </table>

            <h4 style="margin:0 0 6px;color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:0.05em">🌐 Lato Client / Tema</h4>
            <table class="widefat striped" style="margin-bottom:20px">
                <tr><td style="width:240px"><strong>Cache Divi (et-cache)</strong></td>
                    <td><?php
                    $et_cache = WP_CONTENT_DIR . '/et-cache/';
                    if ( get_template() === 'Divi' && is_dir( $et_cache ) ) {
                        $files = glob( $et_cache . '*' );
                        echo '⚠️ Cartella presente (' . count( $files ) . ' file). Svuotala se vedi stili vecchi.';
                    } elseif ( get_template() === 'Divi' ) {
                        echo 'ℹ️ Divi attivo ma et-cache/ non trovata';
                    } else {
                        echo '❌ Divi non è il tema attivo';
                    }
                    ?></td>
                </tr>
                <tr><td><strong>Caching plugins rilevati</strong></td><td>
                <?php
                $cache_plugins = array(
                    'w3-total-cache/w3-total-cache.php'              => 'W3 Total Cache',
                    'wp-super-cache/wp-cache.php'                    => 'WP Super Cache',
                    'wp-rocket/wp-rocket.php'                        => 'WP Rocket',
                    'litespeed-cache/litespeed-cache.php'            => 'LiteSpeed Cache',
                    'wp-fastest-cache/wpFastestCache.php'            => 'WP Fastest Cache',
                    'hummingbird-performance/wp-hummingbird.php'     => 'Hummingbird',
                    'breeze/breeze.php'                              => 'Breeze',
                    'sg-cachepress/sg-cachepress.php'                => 'SiteGround Optimizer',
                    'cache-enabler/cache-enabler.php'                => 'Cache Enabler',
                    'autoptimize/autoptimize.php'                    => 'Autoptimize',
                    'wp-optimize/wp-optimize.php'                    => 'WP-Optimize',
                    'nitropack/nitropack.php'                        => 'NitroPack',
                );
                $found_cache = array();
                foreach ( $cache_plugins as $plugin => $name ) {
                    if ( is_plugin_active( $plugin ) ) {
                        $found_cache[] = $name;
                    }
                }
                echo $found_cache ? '✅ ' . esc_html( implode( ', ', $found_cache ) ) : '❌ Nessun plugin di cache rilevato';
                ?>
                </td></tr>
                <tr><td><strong>Cache configurata</strong></td>
                    <td><label><input type="checkbox" name="saf_tools_settings[cache_done]" value="1" <?php checked( ! empty( $tools['cache_done'] ) ); ?>> ✅ Cache configurata e testata</label></td>
                </tr>
            </table>

            <h3>🔍 SEO Plugin</h3>
            <table class="widefat striped" style="margin-bottom:20px">
                <?php
                $seo_plugins = array(
                    'wordpress-seo/wp-seo.php'                              => 'Yoast SEO',
                    'wordpress-seo-premium/wp-seo-premium.php'              => 'Yoast SEO Premium',
                    'seo-by-rank-math/rank-math.php'                        => 'Rank Math SEO',
                    'seo-by-rank-math-pro/rank-math-pro.php'                => 'Rank Math SEO Pro',
                    'wp-seopress/seopress.php'                              => 'SEOPress',
                    'wp-seopress-pro/seopress-pro.php'                      => 'SEOPress Pro',
                    'all-in-one-seo-pack/all_in_one_seo_pack.php'           => 'All in One SEO',
                    'all-in-one-seo-pack-pro/all_in_one_seo_pack.php'      => 'All in One SEO Pro',
                    'autodescription/autodescription.php'                   => 'The SEO Framework',
                    'squirrly-seo/squirrly.php'                             => 'Squirrly SEO',
                    'slim-seo/slim-seo.php'                                 => 'Slim SEO',
                );
                $any_seo = false;
                foreach ( $seo_plugins as $plugin => $name ) {
                    $active = is_plugin_active( $plugin );
                    if ( $active ) $any_seo = true;
                ?>
                    <tr><td style="width:240px"><strong><?php echo esc_html( $name ); ?></strong></td>
                        <td><?php echo $active ? '✅ Installato e attivo' : '❌ Non rilevato'; ?></td></tr>
                <?php } ?>
                <?php if ( ! $any_seo ) : ?>
                <tr><td colspan="2" style="color:#856404;background:#fff3cd">⚠️ Nessun plugin SEO attivo. SAF fornisce un fallback base: JSON-LD, OG, canonical.</td></tr>
                <?php endif; ?>
                <tr><td><strong>SEO configurato</strong></td>
                    <td><label><input type="checkbox" name="saf_tools_settings[seo_done]" value="1" <?php checked( ! empty( $tools['seo_done'] ) ); ?>> ✅ SEO plugin configurato (titoli, meta, sitemap)</label></td>
                </tr>
            </table>

            <h3>🗺 Sitemap</h3>
            <table class="widefat striped" style="margin-bottom:20px">
                <?php
                $sitemap_plugins = array(
                    'google-sitemap-generator/sitemap.php' => 'Google XML Sitemaps',
                    'xml-sitemap-feed/index.php'           => 'XML Sitemap & Google News',
                    'simple-sitemap/simple-sitemap.php'    => 'Simple Sitemap',
                    'wordpress-seo/wp-seo.php'              => 'Yoast SEO (sitemap built-in)',
                    'seo-by-rank-math/rank-math.php'        => 'Rank Math SEO (sitemap built-in)',
                    'wp-seopress/seopress.php'              => 'SEOPress (sitemap built-in)',
                    'all-in-one-seo-pack/all_in_one_seo_pack.php' => 'All in One SEO (sitemap built-in)',
                );
                $any_sitemap = false;
                foreach ( $sitemap_plugins as $plugin => $name ) {
                    $active = is_plugin_active( $plugin );
                    if ( $active ) $any_sitemap = true;
                ?>
                    <tr><td style="width:240px"><strong><?php echo esc_html( $name ); ?></strong></td>
                        <td><?php echo $active ? '✅ Attivo' : '❌ Non rilevato'; ?></td></tr>
                <?php } ?>
                <?php if ( ! $any_sitemap ) : ?>
                <tr><td colspan="2" style="color:#856404;background:#fff3cd">⚠️ Nessuna sitemap attiva. SAF non genera sitemap automaticamente.</td></tr>
                <?php endif; ?>
                <tr><td><strong>Sitemap funzionante</strong></td>
                    <td><label><input type="checkbox" name="saf_tools_settings[sitemap_done]" value="1" <?php checked( ! empty( $tools['sitemap_done'] ) ); ?>> ✅ Sitemap testata e inviata a Google / Bing</label></td>
                </tr>
            </table>

            <h3>📊 Google Analytics & Search Console</h3>
            <table class="widefat striped" style="margin-bottom:20px">
                <?php
                $analytics_plugins = array(
                    'google-analytics-for-wordpress/googleanalytics.php'                     => 'MonsterInsights',
                    'google-analytics-premium/googleanalytics-premium.php'                  => 'MonsterInsights Pro',
                    'ga-google-analytics/ga-google-analytics.php'                            => 'GA Google Analytics',
                    'exactmetrics-premium/exactmetrics-premium.php'                          => 'ExactMetrics Pro',
                    'wp-google-analytics/wp-google-analytics.php'                            => 'WP Google Analytics',
                    'google-site-kit/google-site-kit.php'                                    => 'Google Site Kit',
                    'analytics-cat/analytics-cat.php'                                        => 'Analytical Cat',
                    'pixelyoursite/pixelyoursite.php'                                        => 'PixelYourSite',
                    'woocommerce-google-analytics-integration/woocommerce-google-analytics-integration.php' => 'GA for WooCommerce',
                    'google-analytics-dashboard-for-wp/gadwp.php'                            => 'ExactMetrics (legacy)',
                    'ga-google-analytics/ga-google-analytics.php'                            => 'GA Google Analytics',
                );
                $any_analytics = false;
                foreach ( $analytics_plugins as $plugin => $name ) {
                    $active = is_plugin_active( $plugin );
                    if ( $active ) $any_analytics = true;
                ?>
                    <tr><td style="width:240px"><strong><?php echo esc_html( $name ); ?></strong></td>
                        <td><?php echo $active ? '✅ Attivo' : '❌ Non rilevato'; ?></td></tr>
                <?php } ?>
                <?php if ( ! $any_analytics ) : ?>
                <tr><td colspan="2" style="color:#856404;background:#fff3cd">⚠️ Nessun plugin Analytics rilevato. Usa Google Site Kit o inserisci manualmente il codice GA4.</td></tr>
                <?php endif; ?>
                <tr><td><strong>GA4 / Search Console configurato</strong></td>
                    <td><label><input type="checkbox" name="saf_tools_settings[analytics_done]" value="1" <?php checked( ! empty( $tools['analytics_done'] ) ); ?>> ✅ GA4 e/o Search Console configurati e funzionanti</label></td>
                </tr>
            </table>

            <h3>💾 Backup</h3>
            <table class="widefat striped" style="margin-bottom:20px">
                <?php
                $backup_plugins = array(
                    'updraftplus/updraftplus.php'                           => 'UpdraftPlus',
                    'backwpup/backwpup.php'                                 => 'BackWPup',
                    'duplicator/duplicator.php'                             => 'Duplicator',
                    'duplicator-pro/duplicator-pro.php'                     => 'Duplicator Pro',
                    'all-in-one-wp-migration/all-in-one-wp-migration.php'   => 'All-in-One WP Migration',
                    'blogvault/blogvault.php'                               => 'BlogVault',
                    'jetpack/jetpack.php'                                   => 'Jetpack Backup (VaultPress)',
                    'wpvivid-backuprestore/wpvivid-backuprestore.php'       => 'WPvivid Backup',
                    'backup/backup.php'                                     => 'Backup (bUpd)',
                    'backupbuddy/backupbuddy.php'                           => 'BackupBuddy',
                );
                $any_backup = false;
                foreach ( $backup_plugins as $plugin => $name ) {
                    $active = is_plugin_active( $plugin );
                    if ( $active ) $any_backup = true;
                ?>
                    <tr><td style="width:240px"><strong><?php echo esc_html( $name ); ?></strong></td>
                        <td><?php echo $active ? '✅ Attivo' : '❌ Non rilevato'; ?></td></tr>
                <?php } ?>
                <tr><td colspan="2">
                <?php if ( ! $any_backup ) : ?>
                    <p style="color:#856404;background:#fff3cd;padding:6px 10px;border-radius:4px">⚠️ Nessun plugin backup attivo. Verifica che il tuo Hosting Provider fornisca backup automatici (giornalieri/settimanali).</p>
                <?php else : ?>
                    <p style="color:#2e7d32;background:#e8f5e9;padding:6px 10px;border-radius:4px">✅ Almeno un plugin backup rilevato. Ricorda di verificare che i backup siano programmati e funzionanti.</p>
                <?php endif; ?>
                    <p style="margin:8px 0 0"><strong>Checklist backup:</strong></p>
                    <ul style="margin:4px 0 0;padding-left:20px">
                        <li><label><input type="checkbox" name="saf_tools_settings[backup_hosting]" value="1" <?php checked( ! empty( $tools['backup_hosting'] ) ); ?>> Backup hosting (giornaliero/settimanale) verificato</label></li>
                        <li><label><input type="checkbox" name="saf_tools_settings[backup_plugin]" value="1" <?php checked( ! empty( $tools['backup_plugin'] ) ); ?>> Plugin backup configurato e testato</label></li>
                        <li><label><input type="checkbox" name="saf_tools_settings[backup_offsite]" value="1" <?php checked( ! empty( $tools['backup_offsite'] ) ); ?>> Backup off-site attivo (Cloud, FTP, email)</label></li>
                    </ul>
                </td></tr>
            </table>

            <h3>🔒 Sicurezza</h3>
            <table class="widefat striped" style="margin-bottom:20px">
                <?php
                $sec_plugins = array(
                    'wordfence/wordfence.php'                                 => 'Wordfence Security',
                    'sucuri-scanner/sucuri.php'                               => 'Sucuri Security',
                    'better-wp-security/better-wp-security.php'               => 'iThemes Security',
                    'all-in-one-wp-security-and-firewall/wp-security.php'     => 'All-In-One Security (AIOS)',
                    'bulletproof-security/bulletproof-security.php'           => 'BulletProof Security',
                    'secuPRESS/secupress.php'                                 => 'SecuPress',
                    'wp-cerber/wp-cerber.php'                                 => 'WP Cerber',
                    'ninjafirewall/ninjafirewall.php'                         => 'NinjaFirewall',
                    'antispam-bee/antispam-bee.php'                           => 'Antispam Bee',
                    'akismet/akismet.php'                                     => 'Akismet',
                );
                $any_sec = false;
                foreach ( $sec_plugins as $plugin => $name ) {
                    $active = is_plugin_active( $plugin );
                    if ( $active ) $any_sec = true;
                ?>
                    <tr><td style="width:240px"><strong><?php echo esc_html( $name ); ?></strong></td>
                        <td><?php echo $active ? '✅ Attivo' : '❌ Non rilevato'; ?></td></tr>
                <?php } ?>
                <tr><td colspan="2">
                <?php if ( ! $any_sec ) : ?>
                    <p style="color:#856404;background:#fff3cd;padding:6px 10px;border-radius:4px">⚠️ Nessun plugin sicurezza attivo. Valuta Wordfence (gratuito) o iThemes Security per protezione base.</p>
                <?php else : ?>
                    <p style="color:#2e7d32;background:#e8f5e9;padding:6px 10px;border-radius:4px">✅ Almeno un plugin sicurezza rilevato.</p>
                <?php endif; ?>
                    <p style="margin:8px 0 0"><strong>Checklist sicurezza:</strong></p>
                    <ul style="margin:4px 0 0;padding-left:20px">
                        <li><label><input type="checkbox" name="saf_tools_settings[sec_firewall]" value="1" <?php checked( ! empty( $tools['sec_firewall'] ) ); ?>> Firewall applicativo attivo (WAF / plugin)</label></li>
                        <li><label><input type="checkbox" name="saf_tools_settings[sec_login]" value="1" <?php checked( ! empty( $tools['sec_login'] ) ); ?>> Protezione login (rate limiting / 2FA / CAPTCHA)</label></li>
                        <li><label><input type="checkbox" name="saf_tools_settings[sec_scan]" value="1" <?php checked( ! empty( $tools['sec_scan'] ) ); ?>> Scansione malware programmata</label></li>
                        <li><label><input type="checkbox" name="saf_tools_settings[sec_ssl]" value="1" <?php checked( ! empty( $tools['sec_ssl'] ) ); ?>> SSL attivo e redirect HTTP → HTTPS</label></li>
                        <li><label><input type="checkbox" name="saf_tools_settings[sec_updates]" value="1" <?php checked( ! empty( $tools['sec_updates'] ) ); ?>> Aggiornamenti automatici WP / plugin / temi verificati</label></li>
                    </ul>
                </td></tr>
            </table>

            <?php submit_button( saf_t( 'btn_save_tools' ) ); ?>
        </form>

        <?php elseif ( $tab === 'credits' ) : ?>
        <?php
        if ( ! empty( $_POST['saf_credits_save'] ) && check_admin_referer( 'saf_credits', 'saf_credits_nonce' ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                $input = wp_unslash( $_POST['saf_credits'] ?? array() );
                $clean = array(
                    'author_name' => sanitize_text_field( $input['author_name'] ?? '' ),
                    'author_url'  => esc_url_raw( $input['author_url'] ?? '' ),
                    'client_name' => sanitize_text_field( $input['client_name'] ?? '' ),
                    'client_url'  => esc_url_raw( $input['client_url'] ?? '' ),
                    'dev_notes'   => sanitize_textarea_field( $input['dev_notes'] ?? '' ),
                    'created'     => sanitize_text_field( $input['created'] ?? '' ),
                );
                update_option( 'saf_credits_settings', $clean );
                echo '<div class="notice notice-success is-dismissible"><p>' . saf_t( 'err_saved' ) . '</p></div>';
                $credits = $clean;
            }
        }
        ?>
        <h2><?php echo saf_t( 'credits_title' ); ?></h2>
        <p class="description"><?php echo saf_t( 'credits_desc' ); ?></p>

        <form method="post">
            <?php wp_nonce_field( 'saf_credits', 'saf_credits_nonce' ); ?>
            <input type="hidden" name="saf_credits_save" value="1">

            <h3><?php echo saf_t( 'credits_author' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="saf_credits_author_name"><?php echo saf_t( 'credits_author_name' ); ?></label></th>
                    <td>
                        <input type="text" id="saf_credits_author_name"
                               name="saf_credits[author_name]"
                               value="<?php echo esc_attr( $credits['author_name'] ?? '' ); ?>"
                               class="regular-text" placeholder="<?php echo esc_attr( saf_t( 'credits_author_name' ) ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="saf_credits_author_url"><?php echo saf_t( 'credits_author_url' ); ?></label></th>
                    <td>
                        <input type="url" id="saf_credits_author_url"
                               name="saf_credits[author_url]"
                               value="<?php echo esc_attr( $credits['author_url'] ?? '' ); ?>"
                                class="regular-text" placeholder="https://<?php echo esc_attr( saf_t( 'credits_author_url_ph' ) ); ?>">
                    </td>
                </tr>
            </table>

            <h3><?php echo saf_t( 'credits_client' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="saf_credits_client_name"><?php echo saf_t( 'credits_client_name' ); ?></label></th>
                    <td>
                        <input type="text" id="saf_credits_client_name"
                               name="saf_credits[client_name]"
                               value="<?php echo esc_attr( $credits['client_name'] ?? '' ); ?>"
                               class="regular-text" placeholder="<?php echo esc_attr( saf_t( 'credits_client_name' ) ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="saf_credits_client_url"><?php echo saf_t( 'credits_client_url' ); ?></label></th>
                    <td>
                        <input type="url" id="saf_credits_client_url"
                               name="saf_credits[client_url]"
                               value="<?php echo esc_attr( $credits['client_url'] ?? '' ); ?>"
                                class="regular-text" placeholder="https://<?php echo esc_attr( saf_t( 'credits_client_url_ph' ) ); ?>">
                    </td>
                </tr>
            </table>

            <h3><?php echo saf_t( 'credits_notes_title' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="saf_credits_dev_notes"><?php echo saf_t( 'credits_notes' ); ?></label></th>
                    <td>
                        <textarea id="saf_credits_dev_notes" name="saf_credits[dev_notes]"
                                  rows="8" class="large-text"
                                  placeholder="<?php echo esc_attr( saf_t( 'credits_notes_title' ) ); ?>"><?php
                            echo esc_textarea( $credits['dev_notes'] ?? '' );
                        ?></textarea>
                        <p class="description"><?php echo saf_t( 'credits_notes_desc' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="saf_credits_created"><?php echo saf_t( 'credits_created' ); ?></label></th>
                    <td>
                        <input type="text" id="saf_credits_created"
                               name="saf_credits[created]"
                               value="<?php echo esc_attr( $credits['created'] ?? '' ); ?>"
                                class="regular-text" placeholder="<?php echo esc_attr( saf_t( 'credits_created_ph' ) ); ?>">
                    </td>
                </tr>
            </table>

            <?php submit_button( saf_t( 'credits_save' ), 'primary' ); ?>
        </form>

        <?php endif; ?>

        <!-- Crediti SAF -->
        <div class="saf-credits">
            <?php echo saf_get_credits_html(); ?>
        </div>

        </div><!-- /.saf-tab-content -->

    </div><!-- /.wrap -->
    <?php
}


/* ============================================================
   SEZIONE 55 — PULIZIA WIDGET DASHBOARD
   ============================================================ */

add_action( 'wp_dashboard_setup', 'saf_cleanup_dashboard' );
function saf_cleanup_dashboard() {
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_primary',     'dashboard', 'side' );
    // Divi — ID potrebbe cambiare con Divi 5; decommentare dopo verifica
    // remove_meta_box( 'et_dashboard_widget', 'dashboard', 'normal' );
}


/* ============================================================
   SEZIONE 56 — TAB ROBOTS.TXT
   Editor robots.txt dinamico — URL sito auto-compilato.
   WordPress serve /robots.txt virtuale via filtro robots_txt.
   ============================================================ */

// Registra settings per robots e avanzate
add_action( 'admin_init', 'saf_register_extra_settings' );
function saf_register_extra_settings() {
    register_setting( 'saf_robots_group', 'saf_robots_content', 'saf_sanitize_robots' );
    register_setting( 'saf_adv_group',    'saf_adv_settings',   'saf_sanitize_adv' );
    register_setting( 'saf_nap_group',    'saf_nap_html',       'saf_sanitize_nap' );
    register_setting( 'saf_tools_group',  'saf_tools_settings', 'saf_sanitize_tools' );
}

function saf_sanitize_nap( $input ) {
    // Permetti HTML sicuro per il footer NAP
    $allowed = array(
        'address' => array( 'class' => array(), 'id' => array() ),
        'p'       => array( 'class' => array() ),
        'span'    => array( 'class' => array() ),
        'strong'  => array(),
        'em'      => array(),
        'br'      => array(),
        'a'       => array( 'href' => array(), 'class' => array(), 'target' => array(), 'rel' => array() ),
        'div'     => array( 'class' => array(), 'id' => array() ),
        'ul'      => array( 'class' => array() ),
        'li'      => array( 'class' => array() ),
    );
    return wp_kses( $input, $allowed );
}

function saf_sanitize_robots( $input ) {
    // Permetti solo testo semplice — nessun HTML
    return sanitize_textarea_field( $input );
}

function saf_sanitize_adv( $input ) {
    $hide = array();
    $menu_keys = array( 'tools', 'comments', 'themes', 'plugins', 'users', 'settings', 'projects' );
    foreach ( $menu_keys as $k ) {
        $hide[ $k ] = ! empty( $input['hide_menu_items'][ $k ] ) ? 1 : 0;
    }
    $custom_raw = sanitize_textarea_field( $input['custom_hide'] ?? '' );
    $custom_lines = array_filter( array_map( 'trim', explode( "\n", $custom_raw ) ) );
    // Rimuovi slug duplicati o vuoti
    $custom_lines = array_unique( $custom_lines );
    return array(
        'disable_comments' => ! empty( $input['disable_comments'] ) ? 1 : 0,
        'hsts_enabled'     => ! empty( $input['hsts_enabled'] ) ? 1 : 0,
        'hide_progetto'    => ! empty( $input['hide_progetto'] ) ? 1 : 0,
        'hide_menu_items'  => $hide,
        'custom_hide'      => implode( "\n", $custom_lines ),
        'smtp_from_name'   => sanitize_text_field( $input['smtp_from_name'] ?? '' ),
        'smtp_from_email'  => sanitize_email( $input['smtp_from_email'] ?? '' ),
        'enable_svg'       => ! empty( $input['enable_svg'] ) ? 1 : 0,
        // 'enable_divi' rimosso in v1.2.1
    );
}

function saf_sanitize_tools( $input ) {
    return array(
        'cache_done'      => ! empty( $input['cache_done'] ) ? 1 : 0,
        'seo_done'        => ! empty( $input['seo_done'] ) ? 1 : 0,
        'sitemap_done'    => ! empty( $input['sitemap_done'] ) ? 1 : 0,
        'analytics_done'  => ! empty( $input['analytics_done'] ) ? 1 : 0,
        'backup_hosting'  => ! empty( $input['backup_hosting'] ) ? 1 : 0,
        'backup_plugin'   => ! empty( $input['backup_plugin'] ) ? 1 : 0,
        'backup_offsite'  => ! empty( $input['backup_offsite'] ) ? 1 : 0,
        'sec_firewall'    => ! empty( $input['sec_firewall'] ) ? 1 : 0,
        'sec_login'       => ! empty( $input['sec_login'] ) ? 1 : 0,
        'sec_scan'        => ! empty( $input['sec_scan'] ) ? 1 : 0,
        'sec_ssl'         => ! empty( $input['sec_ssl'] ) ? 1 : 0,
        'sec_updates'     => ! empty( $input['sec_updates'] ) ? 1 : 0,
    );
}

// Hook WordPress: serve il nostro robots.txt dinamico
// Non interferire con Rank Math o Yoast — loro gestiscono robots.txt
if ( ! class_exists( 'RankMath\\Robots_Txt' ) && ! defined( 'WPSEO_VERSION' ) ) {
    add_filter( 'robots_txt', 'saf_dynamic_robots_txt', 10, 2 );
}
function saf_dynamic_robots_txt( $output, $public ) {
    $saved = get_option( 'saf_robots_content', '' );
    if ( empty( trim( $saved ) ) ) return $output; // Se vuoto usa quello WP di default

    // Sostituisce automaticamente il placeholder URL con l'URL reale
    $site_url = home_url( '/' );
    $replaced = str_replace(
        array( 'https://www.tuosito.it/', 'https://www.tuosito.it', '{{SITE_URL}}' ),
        $site_url,
        $saved
    );

    // {{LOGIN_SLUG}} rimosso — login su /wp-login.php standard
    // Sostituzione per retrocompatibilità con robots.txt salvati precedentemente
    $replaced = str_replace( '{{LOGIN_SLUG}}', 'wp-login.php', $replaced );

    return $replaced;
}

// Inizializza robots.txt con il template se non ancora salvato
add_action( 'admin_init', 'saf_init_robots_content' );
function saf_init_robots_content() {
    if ( get_option( 'saf_robots_content' ) !== false ) return;
    // Prima inizializzazione — carica il template dal file
    $template_file = SAF_DIR . 'robots-default.txt';
    if ( file_exists( $template_file ) ) {
        $content = file_get_contents( $template_file );
        update_option( 'saf_robots_content', $content );
    }
}


/* ============================================================
   SEZIONE 57 — SMTP FROM NAME / FROM EMAIL
   Migliora affidabilità email WP senza plugin aggiuntivi.
   Per SMTP completo (host, porta, auth) usare WP Mail SMTP.
   ============================================================ */

add_filter( 'wp_mail_from',      'saf_mail_from' );
add_filter( 'wp_mail_from_name', 'saf_mail_from_name' );

function saf_mail_from( $email ) {
    $adv  = (array) get_option( 'saf_adv_settings', array() );
    $from = $adv['smtp_from_email'] ?? '';
    return ! empty( $from ) ? $from : $email;
}

function saf_mail_from_name( $name ) {
    $adv  = (array) get_option( 'saf_adv_settings', array() );
    $from = $adv['smtp_from_name'] ?? '';
    if ( ! empty( $from ) ) return $from;
    // Fallback: nome organizzazione da Dati Sito
    if ( function_exists( 'saf_get_org_data' ) ) {
        $org = saf_get_org_data();
        if ( ! empty( $org['name'] ) ) return $org['name'];
    }
    return $name;
}


/* ============================================================
   SEZIONE 58 — CLEANUP OPTIONS (disinstallazione)
   ============================================================ */

/**
 * Elimina tutte le opzioni saf_* dal database.
 * Chiamabile manualmente dal tab Avanzate o da un hook di disinstallazione.
 */
function saf_cleanup_options() {
    global $wpdb;

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", 'saf_%' ) );
}


/* ============================================================
   SEZIONE 59 — HOOK DI DISINSTALLAZIONE (plugin normale)
   Se SAF è installato come plugin normale (non MU), questo hook
   pulisce le opzioni alla disattivazione.
   ============================================================ */

register_deactivation_hook( SAF_DIR . 'saf-loader.php', 'saf_cleanup_on_deactivation' );
function saf_cleanup_on_deactivation() {
    // Chiedi conferma all'utente (hook standard non può mostrare UI)
    // Puliamo solo se è esplicitamente richiesto via clean_active_plugins
    // Per ora: lascia le opzioni, l'utente le pulisce manualmente da ⚙️ Avanzate
}


/* ============================================================
   SEZIONE 60 — DISMISS NOTIFICHE PERSISTENTE (AJAX + user meta)
   ============================================================ */

add_action( 'wp_ajax_saf_dismiss_notice', 'saf_ajax_dismiss_notice' );
function saf_ajax_dismiss_notice() {
    check_ajax_referer( 'saf_dismiss_notice' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( -1 );
    }
    $key = sanitize_key( $_POST['key'] ?? '' );
    if ( $key ) {
        $dismissed = get_user_meta( get_current_user_id(), 'saf_dismissed_notices', true ) ?: array();
        $dismissed[ $key ] = true;
        update_user_meta( get_current_user_id(), 'saf_dismissed_notices', $dismissed );
    }
    wp_die( 1 );
}

function saf_is_notice_dismissed( $key ) {
    $dismissed = get_user_meta( get_current_user_id(), 'saf_dismissed_notices', true ) ?: array();
    return isset( $dismissed[ $key ] );
}

