# SAF — Security & Admin Framework

## Stack
WordPress 7.0+, PHP 8.2+, zero jQuery, zero dipendenze. Funziona con qualsiasi tema.

## Struttura
```
saf/ ← Plugin (MU o normale)
├── saf-loader.php       ← Entry point + child theme auto-create + saf_write_file()
├── version.php          ← SAF_DIR, SAF_URL, saf_t(), saf_get_credits_html()
├── admin.php            ← ⚙️ Dati Sito (9 tab: Org, SEO, Security, Robots, NAP, Adv, Child, Credits)
├── child-theme/         ← Template sorgente per creazione amar-design
├── security.php         ← Rate limiting, HSTS, honeypot, login brand
├── seo.php              ← JSON-LD, canonical, OG, breadcrumb
├── helpers.php          ← YouTube, date, social share, pagination, NAP
├── performance.php      ← oEmbed, heartbeat, revisioni, lazy load
├── cleanup.php          ← Commenti, menu, admin bar, crediti footer
├── dashboard.php        ← Widget + frontend button + crediti
├── guida.php            ← 📖 Guida Sito (7 tab)
├── languages/           ← it_IT.php, en_US.php (~100 chiavi)
├── guide-IT.html, guide-EN.html
└── CREDITS.md, LICENSE, README.md, README-EN.md
```

## Convenzioni
- Funzioni: prefisso `saf_`, option name: `saf_*_settings`
- Shortcode: return, ob_start/ob_get_clean
- Output escapato: esc_html, esc_url, esc_attr, wp_kses_post
- i18n: `saf_t('chiave')` in `languages/*.php`
- Scrittura file: usare `saf_write_file()` (usa WP_Filesystem, fallback file_put_contents + chmod)

## Checkpoint 05/06/2026
- Child theme creato correttamente via admin_init (MU + plugin normale)
- Scrittura style.css converted a saf_write_file() con WP_Filesystem API
- `@chmod(0644)` dopo copy() per permessi sovrascrittura
- Diagnostica visibile in tab Child Theme (percorsi, permessi, owner)
- Bug risolti: deadlock option, register_activation_hook non funzionante per MU
- Da testare: salvataggio style.css su hosting reale
