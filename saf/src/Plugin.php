<?php
namespace SAF;
defined( 'ABSPATH' ) || exit;

use SAF\Admin\AdminMenu;
use SAF\Admin\SettingsPage;
use SAF\Admin\DashboardWidget;
use SAF\Admin\GuidaPage;
use SAF\Modules\Security;
use SAF\Modules\SEO;
use SAF\Modules\Performance;
use SAF\Modules\Cleanup;
use SAF\Modules\Duplicate;
use SAF\Modules\PostStatus;
use SAF\I18n\Translator;
use SAF\Helpers\YouTube;
use SAF\Helpers\SocialShare;
use SAF\Helpers\Breadcrumb;
use SAF\Helpers\ReadingTime;
use SAF\Helpers\FooterInfo;
use SAF\Helpers\NapHtml;
use SAF\Helpers\Pagination;

class Plugin {
    private static ?Plugin $instance = null;
    private array $modules = [];

    public static function getInstance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void {
        $translator = new Translator();
        $translator->init();

        $this->modules['security']    = new Security();
        $this->modules['seo']         = new SEO();
        $this->modules['performance'] = new Performance();
        $this->modules['cleanup']     = new Cleanup();
        $this->modules['duplicate']   = new Duplicate();
        $this->modules['poststatus']  = new PostStatus();

        $admin_menu     = new AdminMenu();
        $settings_page  = new SettingsPage();
        $dashboard_wid  = new DashboardWidget();
        $guida_page     = new GuidaPage();

        $admin_menu->init();
        $settings_page->init();
        $dashboard_wid->init();
        $guida_page->init();

        foreach ( $this->modules as $module ) {
            $module->init();
        }

        YouTube::registerShortcode();
        ( new SocialShare() )->init();
        ( new Breadcrumb() )->init();
        ( new ReadingTime() )->init();
        ( new FooterInfo() )->init();
        ( new NapHtml() )->init();

        $this->initCompatHelpers();
    }

    private function initCompatHelpers(): void {
        require_once SAF_DIR . 'version.php';
        require_once SAF_DIR . 'src/helpers-compat.php';
    }

    public function getModule( string $name ): ?object {
        return $this->modules[ $name ] ?? null;
    }
}
