# Amar SAF — Security & Admin Framework

**SAF** is a modular WordPress framework that adds security, admin tools, SEO, performance, and utilities to **any theme**.

Developed by [Amar Amoretti](https://yaoki.academy) — GPL v2+.

---

## What is SAF?

A **WordPress plugin** (standard or MU) with 6 independent functional modules and a unified admin interface. Each module does one thing and does it well. Works with **any WordPress theme**: Divi, Astra, GeneratePress, Kadence, Twenty Twenty-Four...

No jQuery (except admin.js), no heavy dependencies, no bloat.

**New in v2.0:** OOP architecture with `SAF\*` namespaces, PSR-4 autoloader, separate templates, WooCommerce-style menu, helper classes, i18n.

---

## v2 Structure

```
saf/
├── saf.php                          ← Entry point (plugin header)
├── saf-loader.php                   ← MU loader
├── version.php                      ← SAF_VERSION, SAF_DIR, SAF_URL
├── src/
│   ├── Autoloader.php               ← PSR-4 autoloader
│   ├── Plugin.php                   ← Bootstrap, module init
│   ├── helpers-compat.php           ← v1 global functions preserved
│   ├── I18n/Translator.php          ← PHP array-based translations
│   ├── Modules/                     ← 6 OOP modules
│   │   ├── Security.php
│   │   ├── SEO.php
│   │   ├── Performance.php
│   │   ├── Cleanup.php
│   │   ├── Duplicate.php
│   │   └── PostStatus.php
│   ├── Admin/                       ← Admin menu and pages
│   │   ├── AdminMenu.php
│   │   ├── SettingsPage.php
│   │   ├── DashboardWidget.php
│   │   └── GuidaPage.php
│   └── Helpers/                     ← Helper classes
│       ├── YouTube.php              ← Video embed (nocookie)
│       ├── DateFormatter.php        ← Relative/Italian dates
│       ├── SocialShare.php          ← Share buttons
│       └── Pagination.php           ← Page navigation
├── templates/admin/                 ← 8 separate templates
├── assets/css/                      ← admin.css + login.css
├── assets/js/                       ← admin.js
├── languages/                       ← it_IT.php + en_US.php
├── tests/                           ← PHPUnit bootstrap + tests
└── child-theme/                     ← amar-design child theme (preserved)
```

## Modules

| Module | Class | What it does |
|--------|-------|-------------|
| **Security** | `Security` | Removes generator tag, XML-RPC, pingback, anonymous REST endpoints, WP_DEBUG notices, login CSS |
| **SEO** | `SEO` | Automatic meta description, excerpt support, title filter (toggleable from settings) |
| **Performance** | `Performance` | Removes emoji, embed, jQuery migrate; optional CSS/JS defer |
| **Cleanup** | `Cleanup` | Disables comments, cleans admin bar, removes default menus, CPT counters in dashboard |
| **Duplicate** | `Duplicate` | "Clone" link on pages/posts/CPTs with meta, taxonomies, and thumbnail copy |
| **Post Status** | `PostStatus` | Draft counter in admin bar, colored status badges, version info shortcode |

## Helpers

| Class | Main methods |
|-------|-------------|
| `YouTube` | `getEmbedUrl($url)`, `renderEmbed($url, $title, $attrs)`, shortcode `[saf_youtube]` |
| `DateFormatter` | `formatRelative($date)`, `formatItalian($date, $show_time)` |
| `SocialShare` | `getLinks($url, $title)`, `renderButtons($url, $title, $networks)` |
| `Pagination` | `render($query, $args)` |

## Installation

### As regular plugin

1. Copy the `saf/` folder to `/wp-content/plugins/saf/`
2. Go to **Plugins → Add New → Activate**

### As MU Plugin

1. Copy the `saf/` folder to `/wp-content/mu-plugins/saf/`
2. Copy `saf-loader.php` to `/wp-content/mu-plugins/saf-loader.php`
3. Modules are always active

```
wp-content/
└── mu-plugins/
    ├── saf-loader.php       ← MU entry point
    └── saf/
        ├── saf.php
        ├── version.php
        ├── src/
        └── ...
```

## Shortcodes

| Shortcode | Description |
|-----------|-------------|
| `[saf_version_info]` | Shows PHP, WordPress, and SAF versions |
| `[saf_youtube url="..." title="..."]` | YouTube embed via youtube-nocookie.com |

## Child Theme

The **amar-design** child theme can be created from the SAF dashboard → **Child Theme** tab.
It will be created at `/wp-content/themes/amar-design/` with style.css, functions.php, and inc/, css/, js/ folders.

## Translations

SAF supports IT and EN out of the box. Translation files are PHP arrays in `languages/`.
To add a language: copy `languages/it_IT.php` to `languages/{locale}.php` and translate the strings.

## Upgrade from v1

All v1 global functions are preserved in `src/helpers-compat.php`. Legacy v1 files in the root (`security.php`, `seo.php`, etc.) remain for backward compatibility but are automatically skipped when v2 is active.

## License

GNU General Public License v2 or later.
See [LICENSE](LICENSE) for full terms.
