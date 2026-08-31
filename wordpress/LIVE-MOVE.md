# Option 1 Builders — LocalWP test to live

Live site: [https://option1buildersinc.com](https://option1buildersinc.com). LocalWP (`https://option-1-builders.local`) is the test site only. Do not clone the Local database onto live.

## Stack on live

- Theme: Hello Elementor (Astra stays installed but unused)
- Plugins already on live: Elementor, Elementor Pro, Novamira, plus the existing Hostinger / Yoast / SMTP / LiteSpeed set
- **Do not install any new plugin.** That includes Happy Addons.
- Header / footer: Elementor Pro Theme Builder, native Pro widgets only (`theme-site-logo`, `nav-menu`)
- Page bodies: the LocalWP Elementor JSON
- Estimate form: `[o1b_estimate]` (not Elementor Pro Form, not Contact Form 7)
- Reviews: existing Google Reviews widget `[grw id=94]` on Home, About, and the seven service children. Do not paste a static quote.
- Awards: the six `award-*` media keys. Home keeps them on the dark manager band. Service pages use `.o1b-awards--light` above GRW.

## Public URLs (must not change)

- `/`
- `/services/`
- `/projects/`
- `/about-us/`
- `/blog/`
- `/contact-us/`

New public URLs (create if missing; do not invent other slugs):

- `/services/artificial-grass-installation/`
- `/services/paver-installation/`
- `/services/landscape-design-installation/`
- `/services/stepping-stones-pathways/`
- `/services/concrete-dg-gravel/`
- `/services/irrigation-drainage/`
- `/services/vinyl-fencing/`

Old Skyline pages are drafted as `{slug}-old`. New pages take the original slugs.

## What not to copy from Local

Do not copy Local `o1b_page_ids`, `o1b_media_map`, or Happy Addons `ha_library` IDs. If live GRW is not feed `94`, keep the live ID.

## Rebuild order

1. Activate Hello Elementor. Do not install plugins.
2. Upload media into `wp-content/uploads/o1b-src/` (include `project-11.jpg` / `12` / `13`), then `o1b_attach_uploaded_src()`.
3. Copy sandbox PHP (`o1b-live-bootstrap.php`, `o1b-chrome.php`, `o1b-estimate.php`, `o1b-import.php`). Do not copy the Local-only `o1b-bootstrap.php`.
4. If Skyline was already drafted, run **`o1b_ensure_service_children()`** only — creates the seven child pages and rebuilds the Services dropdown. Do not re-run `o1b_live_bootstrap()` in that case.
5. If this is the first live rebuild, run `o1b_live_bootstrap()` — drafts Skyline pages, creates top-level + child pages, rebuilds menus, drafts Skyline footer `#164`.
6. Copy `wordpress/elementor/*.json` to `uploads/o1b-elementor/` and run `o1b_import_all()`.
7. Purge Elementor CSS and LiteSpeed.
