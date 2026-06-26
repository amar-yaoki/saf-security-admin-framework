# Amar SAF — Security & Admin Framework

**SAF** è un framework modulare per WordPress che aggiunge sicurezza, strumenti admin, SEO, performance e utility a **qualsiasi tema**.

Sviluppato da [Amar Amoretti](https://yaoki.academy) — GPL v2+.

---

## Cos'è SAF?

Un **plugin WordPress** (standard o MU) con 6 moduli funzionali indipendenti e un'interfaccia admin unificata. Ogni modulo fa una cosa e la fa bene. Funziona con **qualsiasi tema WordPress**: Divi, Astra, GeneratePress, Kadence, Twenty Twenty-Four...

Niente jQuery (salvo admin.js), niente dipendenze pesanti, niente bloat.

**Novità v2.0:** architettura OOP con namespace `SAF\*`, autoloader PSR-4, template separati, menu WooCommerce-style, helper classi, i18n.

---

## Struttura v2

```
saf/
├── saf.php                          ← Entry point (plugin header)
├── saf-loader.php                   ← MU loader
├── version.php                      ← SAF_VERSION, SAF_DIR, SAF_URL
├── src/
│   ├── Autoloader.php               ← PSR-4 autoloader
│   ├── Plugin.php                   ← Bootstrap, init moduli
│   ├── helpers-compat.php           ← Funzioni globali v1 preservate
│   ├── I18n/Translator.php          ← Traduzioni via PHP array
│   ├── Modules/                     ← 6 moduli OOP
│   │   ├── Security.php
│   │   ├── SEO.php
│   │   ├── Performance.php
│   │   ├── Cleanup.php
│   │   ├── Duplicate.php
│   │   └── PostStatus.php
│   ├── Admin/                       ← Admin menu e pagine
│   │   ├── AdminMenu.php
│   │   ├── SettingsPage.php
│   │   ├── DashboardWidget.php
│   │   └── GuidaPage.php
│   └── Helpers/                     ← Classi helper
│       ├── YouTube.php              ← Embed video con nocookie
│       ├── DateFormatter.php        ← Date relative italiane
│       ├── SocialShare.php          ← Pulsanti condivisione
│       └── Pagination.php           ← Navigazione pagine
├── templates/admin/                 ← 8 template separati
├── assets/css/                      ← admin.css + login.css
├── assets/js/                       ← admin.js
├── languages/                       ← it_IT.php + en_US.php
├── tests/                           ← PHPUnit bootstrap + test
└── child-theme/                     ← Child theme amar-design (preservato)
```

## Moduli

| Modulo | Classe | Cosa fa |
|--------|--------|---------|
| **Sicurezza** | `Security` | Rimuove generator tag, XML-RPC, pingback, endpoint REST anonimi, notifiche WP_DEBUG, login CSS |
| **SEO** | `SEO` | Meta description automatica, supporto excerpt, title filter (attivabile da impostazioni) |
| **Performance** | `Performance` | Rimuove emoji, embed, jQuery migrate; defer CSS/JS opzionale |
| **Cleanup** | `Cleanup` | Disabilita commenti, pulisce admin bar, rimuove menu default, contatori CPT in dashboard |
| **Duplicate** | `Duplicate` | Link "Clona" su pagine/articoli/CPT con copia di meta, tassonomie e thumbnail |
| **Post Status** | `PostStatus` | Contatore bozze in admin bar, stati colore, shortcode version info |

## Helper

| Classe | Metodi principali |
|--------|-------------------|
| `YouTube` | `getEmbedUrl($url)`, `renderEmbed($url, $title, $attrs)`, shortcode `[saf_youtube]` |
| `DateFormatter` | `formatRelative($date)`, `formatItalian($date, $show_time)` |
| `SocialShare` | `getLinks($url, $title)`, `renderButtons($url, $title, $networks)` |
| `Pagination` | `render($query, $args)` |

## Installazione

### Come plugin normale

1. Copia la cartella `saf/` in `/wp-content/plugins/saf/`
2. Vai in **Plugin → Aggiungi → Attiva**

### Come MU Plugin

1. Copia la cartella `saf/` in `/wp-content/mu-plugins/saf/`
2. Copia `saf-loader.php` in `/wp-content/mu-plugins/saf-loader.php`
3. I moduli sono sempre attivi

```
wp-content/
└── mu-plugins/
    ├── saf-loader.php       ← entry point MU
    └── saf/
        ├── saf.php
        ├── version.php
        ├── src/
        └── ...
```

## Shortcode

| Shortcode | Descrizione |
|-----------|-------------|
| `[saf_version_info]` | Mostra versioni PHP, WordPress e SAF |
| `[saf_youtube url="..." title="..."]` | Embed YouTube con youtube-nocookie.com |

## Child Theme

Il child theme **amar-design** può essere creato dalla dashboard SAF → tab **Child Theme**.
Viene creato in `/wp-content/themes/amar-design/` con style.css, functions.php e le cartelle inc/, css/, js/.

## Traduzioni

SAF supporta IT ed EN. Le traduzioni sono file PHP in `languages/`.
Per aggiungere una lingua: copia `languages/it_IT.php` in `languages/{locale}.php` e traducí le stringhe.

## Upgrade da v1

Tutte le funzioni globali v1 sono preservate in `src/helpers-compat.php`. I file v1 nella root (`security.php`, `seo.php`, ecc.) restano per backward compat ma vengono automaticamente saltati se v2 è attivo.

## Licenza

GNU General Public License v2 or later.
Vedi [LICENSE](LICENSE) per i termini completi.
