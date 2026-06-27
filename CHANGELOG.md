# Changelog

## v2.0.0 — 27/06/2026

### 🔄 Full OOP Rewrite (community feedback)
- PSR-4 autoloader with `SAF\*` namespaces
- All modules migrated to classes: Security, SEO, Performance, Cleanup, Duplicate, PostStatus
- Admin classes: AdminMenu, SettingsPage, DashboardWidget, GuidaPage
- Helper classes: YouTube, SocialShare, Breadcrumb, ReadingTime, FooterInfo, NapHtml, Pagination, DateFormatter
- Templates separated from PHP logic (`templates/admin/`)
- Assets separated (`assets/css/`, `assets/js/`)
- WooCommerce-style submenu (9 pages)
- Unit test structure (PHPUnit + WP mocks)
- v1 backward compatibility layer (`helpers-compat.php`)

### 🔒 Security Improvements
- `saf_get_credits_html()` output escaped with `wp_kses_post()`
- `$_GET` inputs sanitized with `sanitize_text_field(wp_unslash())`
- XSS sanitizer bypass fixed (unquoted event handlers)
- All inline styles moved to CSS classes

### ✨ New Features
- Diagnostica tab (WP/PHP/server info, debug log viewer)
- GitHub + Reddit added to social share platforms
- Amazon Author added to organization settings
- CSS editor with automatic `.bak` backup before save
- Cache notice after child theme modifications
- Credits tab separated from settings

### 🐛 Fixes
- Namespace declaration order (must be first after `<?php`)
- Admin submenu centralized (no more duplicate slugs)
- Parse error in tab-settings.php (missing endif)

## v1.2.1 — 03/2026

- Initial public release
- 6 modules: Security, SEO, Performance, Cleanup, Duplicate, PostStatus
- 5 shortcodes: breadcrumb, social share, reading time, footer info, NAP
- Admin dashboard with 6 tabs
- Robots.txt editor
- Child theme creation
- IT/EN translations
