# Urban CMS Platform

A WordPress theme for urban place management organizations — Business Improvement Districts, Downtown Management Organizations, and urban district authorities.

---

## Quick Start (Docker)

**Requirements:** [Docker Desktop](https://www.docker.com/products/docker-desktop/)

```bash
# 1. Start the containers
docker compose up -d

# 2. Run first-time setup (installs WordPress + seeds demo content)
bash setup.sh

# 3. Open in your browser
open http://localhost:8080
```

| URL | What it is |
|---|---|
| `http://localhost:8080` | Front-end site |
| `http://localhost:8080/wp-admin` | WordPress admin |
| `http://localhost:8081` | phpMyAdmin |

**Admin credentials:** `admin` / `admin`

---

## What's Included in Demo Content

The `setup.sh` script seeds the site with realistic sample data:

| Type | Count | Details |
|---|---|---|
| Pages | 5 | Home, News, Submit a Business, Submit an Event, Member Dashboard |
| Events | 3 | With dates, times, locations, categories |
| Businesses | 3 | With addresses, hours, map coordinates, categories |
| Initiatives | 2 | With status, progress %, budget, lead, timeline |
| Team Members | 2 | With titles, contact info |
| News Posts | 3 | Blog posts for the News archive |

---

## Live Editing

The `urban-cms/` theme directory is **live-mounted** into the WordPress container. Any change you make to a theme file is reflected immediately on refresh — no container restart needed.

```bash
# Edit a file
vim urban-cms/style.css

# Refresh browser — change is live
```

---

## Custom Post Types

| Post Type | Slug | Archive URL |
|---|---|---|
| Events | `urban_event` | `/events/` |
| Business Directory | `urban_business` | `/businesses/` |
| Initiatives | `urban_initiative` | `/initiatives/` |
| Team | `urban_team` | No archive |

---

## Custom Gutenberg Blocks

The theme registers 7 dynamic blocks under the **Urban CMS** category in the block editor:

- **Hero Banner** — Full-width hero with CTA buttons, custom colours, background image
- **District Stats** — 4-stat bar (pulls from Customizer or per-block overrides)
- **Events List** — Upcoming events with count/category filter
- **Business Directory** — Filterable business grid with layout options
- **Initiative Progress** — Single initiative progress card
- **Team Grid** — Team member grid with column control
- **Newsletter** — Newsletter signup (inline, card, or full-width variants)

---

## Submission Forms

Two public submission forms create **draft listings** for admin review:

- `/submit-a-business/` — Business directory listing
- `/submit-an-event/` — Event calendar entry

Both include honeypot spam protection, rate limiting (3/hr per IP), and admin email notification.

---

## Member Dashboard

The `/member-dashboard/` page shows logged-in users their submitted businesses and events (by both user ID and submission email, supporting guest submissions).

---

## Docker Commands

```bash
# Start containers
docker compose up -d

# Stop containers (data preserved)
docker compose down

# Stop + wipe all data (start fresh)
docker compose down -v

# View WordPress logs
docker compose logs -f wordpress

# Run a WP-CLI command
docker compose run --rm wpcli wp --allow-root <command>
```

---

## CI / Code Quality

GitHub Actions runs on every push:

| Check | Tool |
|---|---|
| PHP syntax | `php -l` on PHP 8.1, 8.2, 8.3 |
| Block JSON | Python JSON validation + required field check |
| Coding standards | PHPCS + WordPress Coding Standards + PHPCompatibilityWP |
| Theme headers | `style.css` header validation |

Run locally:
```bash
composer install
composer lint           # check
vendor/bin/phpcbf ...   # auto-fix
```

---

## Theme Structure

```
urban-cms/
├── style.css               # Theme header + design system (CSS custom properties)
├── functions.php           # Theme setup, enqueues, REST API, AJAX handlers
├── front-page.php          # Homepage template
├── header.php / footer.php
├── inc/
│   ├── post-types.php      # CPT registration + meta boxes
│   ├── taxonomies.php      # Custom taxonomies
│   ├── customizer.php      # Per-district branding panel
│   ├── blocks.php          # Gutenberg block registration + render callbacks
│   ├── submission-forms.php# AJAX form handlers
│   ├── newsletter.php      # Double opt-in newsletter + admin
│   ├── archive-filters.php # Initiative AJAX filtering
│   └── helpers.php         # Shared utility functions
├── blocks/                 # 7× block.json metadata files
├── assets/
│   ├── css/forms.css
│   └── js/                 # main.js, blocks-editor.js, submission-forms.js, …
├── template-parts/         # Reusable partials (cards, forms, newsletter)
└── templates/              # Custom page templates (submit, dashboard)
```
