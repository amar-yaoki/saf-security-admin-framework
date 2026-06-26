<?php
namespace SAF\Admin;
defined( 'ABSPATH' ) || exit;

class DashboardWidget {
    public function init(): void {
        add_action( 'wp_dashboard_setup', [ $this, 'registerWidget' ] );
    }

    public function registerWidget(): void {
        wp_add_dashboard_widget(
            'saf_panoramica',
            '🏠 SAF — Panoramica Sito',
            [ $this, 'renderWidget' ]
        );
        global $wp_meta_boxes;
        $widget = $wp_meta_boxes['dashboard']['normal']['core']['saf_panoramica'] ?? null;
        if ( $widget ) {
            unset( $wp_meta_boxes['dashboard']['normal']['core']['saf_panoramica'] );
            $wp_meta_boxes['dashboard']['normal']['high']['saf_panoramica'] = $widget;
        }
    }

    public function renderWidget(): void {
        $org     = (array) get_option( 'saf_org_settings', [] );
        $checks  = (array) get_option( 'saf_checklist', [] );
        $credits = (array) get_option( 'saf_credits_settings', [] );
        $theme   = wp_get_theme();
        $version = $theme->get( 'Version' );

        $all_keys = [ 'org_name','org_address','org_social','seo_og','sec_slug','rankmath','check_login','check_author','check_headers','check_xmlrpc','hsts','robots_url','robots_sitemap','jld_org','jld_website','breadcrumb','og_tags','canonical','sitemap','pagespeed','gutenberg_css','defer','images','cookie_banner','privacy_policy','terms','antispam','staging_test','backup','analytics','search_console','uptime' ];
        $total = count( $all_keys );
        $done  = 0;
        foreach ( $all_keys as $k ) { if ( ! empty( $checks[ $k ] ) ) $done++; }
        $pct   = $total > 0 ? round( $done / $total * 100 ) : 0;
        $color = $pct < 40 ? '#e74c3c' : ( $pct < 80 ? '#f39c12' : '#27ae60' );

        $posts  = wp_count_posts();
        $pages  = wp_count_posts( 'page' );
        $users  = count_users();
        $drafts = (int) ( $posts->draft ?? 0 ) + (int) ( $posts->pending ?? 0 );
        ?>
        <div class="saf-dash-widget">
          <div class="saf-dash-row" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
            <div style="flex:1;min-width:120px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px;text-align:center;">
              <strong style="font-size:20px;display:block;"><?php echo (int) ( $posts->publish ?? 0 ); ?></strong>
              <span style="font-size:11px;color:#64748b;">Articoli</span>
            </div>
            <div style="flex:1;min-width:120px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px;text-align:center;">
              <strong style="font-size:20px;display:block;"><?php echo (int) ( $pages->publish ?? 0 ); ?></strong>
              <span style="font-size:11px;color:#64748b;">Pagine</span>
            </div>
            <div style="flex:1;min-width:120px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px;text-align:center;">
              <strong style="font-size:20px;display:block;"><?php echo (int) $drafts; ?></strong>
              <span style="font-size:11px;color:#64748b;">Bozze</span>
            </div>
            <div style="flex:1;min-width:120px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px;text-align:center;">
              <strong style="font-size:20px;display:block;"><?php echo (int) $users['total_users']; ?></strong>
              <span style="font-size:11px;color:#64748b;">Utenti</span>
            </div>
          </div>

          <div style="margin-bottom:12px;">
            <div style="font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">✅ Checklist Pre-Go-Live</div>
            <div style="background:#e2e8f0;border-radius:8px;height:10px;overflow:hidden;margin-bottom:4px;">
              <div style="height:100%;border-radius:8px;width:<?php echo $pct; ?>%;background:<?php echo $color; ?>;transition:width .4s;"></div>
            </div>
            <div style="font-size:11px;font-weight:700;color:<?php echo $color; ?>;"><?php echo $done; ?>/<?php echo $total; ?> (<?php echo $pct; ?>%)</div>
          </div>

          <div style="margin-bottom:12px;">
            <div style="font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:5px;">🔒 Sicurezza</div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
              <span style="font-size:10px;background:#e6f4ea;color:#137333;padding:2px 7px;border-radius:10px;font-weight:600;">✅ XML-RPC off</span>
              <span style="font-size:10px;background:#e6f4ea;color:#137333;padding:2px 7px;border-radius:10px;font-weight:600;">✅ Rate limiting</span>
              <span style="font-size:10px;background:#e6f4ea;color:#137333;padding:2px 7px;border-radius:10px;font-weight:600;">✅ Headers HTTP</span>
              <span style="font-size:10px;background:#e6f4ea;color:#137333;padding:2px 7px;border-radius:10px;font-weight:600;">✅ Enum bloccato</span>
            </div>
          </div>

          <?php if ( ! empty( $org['name'] ) ) : ?>
          <div style="margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #e2e8f0;font-size:12px;">
            <span style="color:#64748b;">Organizzazione:</span> <strong><?php echo esc_html( $org['name'] ); ?></strong>
          </div>
          <?php endif; ?>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=saf&tab=settings' ) ); ?>" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;background:linear-gradient(135deg,#f5960a,#e07b00);border-radius:8px;padding:14px 8px;text-decoration:none;color:#fff;font-weight:700;font-size:12px;box-shadow:0 3px 10px rgba(230,130,12,.35);">
              <span style="font-size:22px;">⚙️</span> Impostazioni
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=saf&tab=guida' ) ); ?>" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;background:linear-gradient(135deg,#f5960a,#e07b00);border-radius:8px;padding:14px 8px;text-decoration:none;color:#fff;font-weight:700;font-size:12px;box-shadow:0 3px 10px rgba(230,130,12,.35);">
              <span style="font-size:22px;">📖</span> Guida
            </a>
          </div>

          <?php
          $author_name  = $credits['author_name'] ?? '';
          $author_url   = $credits['author_url'] ?? '';
          $client_name  = $credits['client_name'] ?? '';
          if ( $author_name || $client_name ) : ?>
          <div style="padding-top:10px;border-top:1px solid #e2e8f0;font-size:11px;color:#94a3b8;text-align:center;">
            <?php if ( $author_name ) : ?>
              Sviluppato da <?php if ( $author_url ) : ?><a href="<?php echo esc_url( $author_url ); ?>" target="_blank" style="color:#2ea3f2;"><?php endif; ?><strong><?php echo esc_html( $author_name ); ?></strong><?php if ( $author_url ) : ?></a><?php endif; ?>
            <?php endif; ?>
            <?php if ( $client_name ) : ?> · per <strong><?php echo esc_html( $client_name ); ?></strong><?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
        <?php
    }
}
