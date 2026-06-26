<?php
/**
 * Admin page template for SAF v2.
 * Variables: $tab, $credits_html
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap saf-dashboard">
    <h1>SAF — Security & Admin Framework <span class="saf-version">v<?php echo esc_html( SAF_VERSION ); ?></span></h1>
    <p class="saf-subtitle">Moduli funzionali per WordPress: sicurezza, admin, SEO, performance, child theme</p>

    <?php if ( function_exists( 'saf_get_credits_html' ) ): ?>
        <div class="saf-credits"><?php echo wp_kses_post( saf_get_credits_html() ); ?></div>
    <?php endif; ?>

    <nav class="nav-tab-wrapper saf-tabs">
        <a href="?page=saf&tab=dashboard"    class="nav-tab <?php echo $tab === 'dashboard' ? 'nav-tab-active' : ''; ?>">Dashboard</a>
        <a href="?page=saf&tab=settings"     class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>">Impostazioni</a>
        <a href="?page=saf&tab=modules"      class="nav-tab <?php echo $tab === 'modules' ? 'nav-tab-active' : ''; ?>">Moduli</a>
        <a href="?page=saf&tab=tools"        class="nav-tab <?php echo $tab === 'tools' ? 'nav-tab-active' : ''; ?>">Strumenti</a>
        <a href="?page=saf&tab=diagnostica"  class="nav-tab <?php echo $tab === 'diagnostica' ? 'nav-tab-active' : ''; ?>">Diagnostica</a>
        <a href="?page=saf&tab=child"        class="nav-tab <?php echo $tab === 'child' ? 'nav-tab-active' : ''; ?>">Child Theme</a>
        <a href="?page=saf&tab=guida"        class="nav-tab <?php echo $tab === 'guida' ? 'nav-tab-active' : ''; ?>">Guida</a>
        <a href="?page=saf&tab=credits"      class="nav-tab <?php echo $tab === 'credits' ? 'nav-tab-active' : ''; ?>">Credits</a>
        <a href="?page=saf&tab=about"        class="nav-tab <?php echo $tab === 'about' ? 'nav-tab-active' : ''; ?>">About</a>
    </nav>

    <div class="saf-tab-content">
        <?php
        $template = SAF_DIR . 'templates/admin/tab-' . $tab . '.php';
        if ( file_exists( $template ) ) {
            include $template;
        } else {
            echo '<p>Contenuto non trovato per la scheda: ' . esc_html( $tab ) . '</p>';
        }
        ?>
    </div>
</div>
