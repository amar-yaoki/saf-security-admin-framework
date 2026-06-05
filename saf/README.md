# Amar SAF — Security & Admin Framework

**SAF** è un framework modulare per WordPress che aggiunge sicurezza, strumenti admin, SEO, performance e utility a **qualsiasi tema**.

Sviluppato da [Amar Amoretti](https://yaoki.academy) — GPL v2+.

---

## Cos'è SAF?

Un **must-use plugin** (o plugin normale) che include 9 moduli funzionali indipendenti. Ogni modulo fa una cosa e la fa bene. Funziona con **qualsiasi tema WordPress**: Divi, Astra, GeneratePress, Kadence, Twenty Twenty-Four...

Niente jQuery, niente dipendenze, niente bloat.

---

## Moduli

| Modulo | File | Cosa fa |
|--------|------|---------|
| 🔒 **Sicurezza** | `security.php` | Rate limiting login, XML-RPC off, HSTS, honeypot, security headers, login brandizzato |
| ⚙️ **Dati Sito** | `admin.php` | 10 tab: Org, SEO, Security, Robots, NAP, Child, Credits, Adv, Plugin, Sistema |
| 🔍 **SEO** | `seo.php` | JSON-LD Organization/WebSite/BreadcrumbList, canonical, OG tags fallback |
| 🛠️ **Helpers** | `helpers.php` | YouTube ID, date italiana, social sharing, paginazione, reading time, NAP |
| 🚀 **Performance** | `performance.php` | oEmbed off, heartbeat off, revisioni limitate, lazy load, CF7 ottimizzato |
| 🧹 **Cleanup** | `cleanup.php` | Disabilitazione commenti, pulizia menu admin, admin bar, footer personalizzato |
| 📊 **Dashboard** | `dashboard.php` | Widget Panoramica Sito con checklist, sicurezza, pulsanti rapidi |
| 📖 **Guida** | `guida.php` | 7 tab: info, struttura, shortcode, sicurezza, checklist, progetto, note |

---

## Installazione

### Come MU Plugin (consigliato)

1. Copia la cartella `saf/` in `/wp-content/mu-plugins/saf/`
2. Il file `saf-loader.php` va in `/wp-content/mu-plugins/saf-loader.php` **(attenzione: NON dentro /saf/)**
3. I moduli sono **sempre attivi** — non si possono disattivare per errore

```
wp-content/
└── mu-plugins/
    ├── saf-loader.php       ← questo è l'entry point
    └── saf/
        ├── version.php
        ├── security.php
        ├── admin.php
        └── ...
```

### Come Plugin Normale

1. Copia l'intera cartella `saf/` in `/wp-content/plugins/saf/`
2. Vai in **Plugin → Aggiungi → Attiva**

In entrambi i casi, `saf-loader.php` rileva automaticamente la posizione e carica tutti i moduli.

---

## Child Theme (opzionale)

Il child theme **amar-design** può essere creato automaticamente:

1. Vai su **⚙️ Dati Sito → Child Theme**
2. Clicca **"Crea child theme amar-design"** se non esiste
3. Modifica `style.css` per impostare il `Template:` corretto
4. Attiva il tema da **Aspetto → Temi**

Il `Template:` deve corrispondere alla cartella del tema parent:
- `Template: Divi` per Divi
- `Template: twentytwentyfour` per Twenty Twenty-Four
- `Template: astra` per Astra
- `Template: generatepress` per GeneratePress

**Feature extra Divi:** Se il tema parent è Divi, puoi attivare l'header search con overlay AJAX (lente + ricerca fullscreen) dal tab Child Theme.

---

## Personalizzazione crediti

Modifica `saf/CREDITS.md` per cambiare il nome autore e il sito web che appaiono:
- Nel widget dashboard
- Nel footer admin di WordPress

---

## Prima configurazione

1. Vai su **⚙️ Dati Sito** nel menu admin
2. Compila il tab **Organizzazione** (nome azienda, logo, indirizzo, social)
3. Vai al tab **Sicurezza** → imposta il numero massimo di tentativi login
4. Vai al tab **Robots.txt** → modifica il contenuto se necessario
5. Vai al tab **Avanzate** → configura SMTP, commenti, HSTS, menu admin
6. Vai al tab **Plugin** → verifica cache, SEO, backup e strumenti di sicurezza
7. Vai al tab **Sistema** → esegui il self-test per verificare che tutto funzioni

---

## Disinstallazione

1. Disattiva il plugin (se plugin normale) o rimuovi `saf-loader.php` (se MU plugin)
2. Le opzioni salvate rimangono nel DB. Per rimuoverle:
   ```sql
   DELETE FROM wp_options WHERE option_name LIKE 'saf_%';
   ```

---

## Licenza

GNU General Public License v2 or later.
Vedi [LICENSE](LICENSE) per i termini completi.
