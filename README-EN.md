# Amar SAF — Security & Admin Framework

**SAF** is a modular WordPress framework that adds security, admin tools, SEO, performance, and utilities to **any theme**.

Developed by [Amar Amoretti](https://yaoki.academy) — GPL v2+.

---

## What is SAF?

A **must-use plugin** (or regular plugin) with 9 independent functional modules. Each module does one thing and does it well. Works with **any WordPress theme**: Divi, Astra, GeneratePress, Kadence, Twenty Twenty-Four...

No jQuery, no dependencies, no bloat.

---

## Modules

| Module | File | What it does |
|--------|------|-------------|
| 🔒 **Security** | `security.php` | Login rate limiting, XML-RPC off, HSTS, honeypot, security headers, branded login |
| ⚙️ **Site Data** | `admin.php` | 10 tabs: Org, SEO, Security, Robots, NAP, Child, Credits, Adv, Plugin, System |
| 🔍 **SEO** | `seo.php` | JSON-LD Organization/WebSite/BreadcrumbList, canonical, OG tags fallback |
| 🛠️ **Helpers** | `helpers.php` | YouTube ID, Italian dates, social sharing, pagination, reading time, NAP |
| 🚀 **Performance** | `performance.php` | oEmbed off, heartbeat off, limited revisions, lazy load, optimized CF7 |
| 🧹 **Cleanup** | `cleanup.php` | Disable comments, admin menu cleanup, custom admin bar, branded footer |
| 📊 **Dashboard** | `dashboard.php` | Site Overview widget with checklist, security status, quick buttons |
| 📖 **Guide** | `guida.php` | 7 tabs: info, structure, shortcodes, security, checklist, project, notes |

---

## Installation

### As MU Plugin (recommended)

1. Copy the `saf/` folder to `/wp-content/mu-plugins/saf/`
2. Copy `saf-loader.php` to `/wp-content/mu-plugins/saf-loader.php` **(note: NOT inside /saf/)**
3. Modules are **always active** — cannot be accidentally deactivated

```
wp-content/
└── mu-plugins/
    ├── saf-loader.php       ← this is the entry point
    └── saf/
        ├── version.php
        ├── security.php
        ├── admin.php
        └── ...
```

### As Regular Plugin

1. Upload the entire `saf/` folder to `/wp-content/plugins/saf/`
2. Go to **Plugins → Installed Plugins**
3. Activate **Amar SAF**

The plugin will automatically create the child theme (`/wp-content/themes/amar-design/`) on activation if it doesn't exist.

---

## Child Theme (optional)

The plugin bundles a generic child theme that gets created automatically on activation.

1. Go to **⚙️ Site Data → Child Theme**
2. Click **Create child theme amar-design** if it doesn't exist
3. Edit `style.css` to set the correct `Template:` line
4. Activate the theme from **Appearance → Themes**

The `Template:` must match the folder name of your parent theme:
- `Template: Divi` for Divi
- `Template: twentytwentyfour` for Twenty Twenty-Four
- `Template: astra` for Astra
- `Template: generatepress` for GeneratePress

### Divi Features

If your parent theme is Divi, you can enable the **header search overlay**:
1. Go to **⚙️ Site Data → Child Theme** → Divi Features
2. Check **Enable Divi header search**
3. A magnifier icon appears in the Divi mobile menu + fullscreen AJAX search overlay

---

## First-Time Setup

1. Go to **⚙️ Site Data** in the admin menu
2. Fill the **Organization** tab (company name, logo, address, social profiles)
3. Go to **Security** tab → set max login attempts
4. Go to **Robots.txt** tab → customize if needed
5. Go to **Advanced** tab → configure SMTP, comments, HSTS, admin menu cleanup
6. Go to **Credits** tab → add your developer/agency branding
7. Go to **Plugins** tab → verify cache, SEO, backup, and security tools
8. Go to **System** tab → run the self-test to verify everything works

---

## Customizing Credits

The plugin footer shows "Developed by..." with your details.
Set them up at **⚙️ Site Data → Credits** tab.

The GPL license credits are read from `CREDITS.md` — edit that file to change the author name and website displayed in the plugin footer.

---

## Translation / Language

SAF supports Italian (`it_IT`) and English (`en_US`) out of the box.
The admin UI automatically switches language based on your WordPress site language setting.

To add a new language:
1. Copy `saf/languages/it_IT.php` to `saf/languages/{locale}.php`
2. Translate the string values
3. SAF will auto-detect your locale

---

## Uninstall

1. Deactivate the plugin (or remove `saf-loader.php` if MU plugin)
2. Go to **⚙️ Site Data → Advanced** and click **Delete all SAF options** to clean the database
3. Or run manually: `DELETE FROM wp_options WHERE option_name LIKE 'saf_%';`

---

## License

GNU General Public License v2 or later.
See [LICENSE](LICENSE) for full terms.
