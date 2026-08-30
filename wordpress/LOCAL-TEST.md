# Option 1 Builders — LocalWP test

Same stack as Ground Truth. LocalWP is the test site only. Do not copy Local page IDs or media IDs onto live.

## Stack (nothing else)

- Theme: Hello Elementor
- Plugins: Elementor (free), Happy Addons (free), Novamira
- Do **not** install Elementor Pro. A Pro Theme Builder header would fight Happy Addons.
- Do **not** install extra form/SEO plugins unless the owner approves.

Header and footer are Happy Addons Theme Builder templates (`ha_library`, condition `include/general`). Page bodies are Elementor documents with template `elementor_header_footer`.

## Page set

| Key | Path |
| --- | --- |
| home | `/` |
| services | `/services/` |
| artificial-grass-installation | `/services/artificial-grass-installation/` |
| paver-installation | `/services/paver-installation/` |
| landscape-design-installation | `/services/landscape-design-installation/` |
| stepping-stones-pathways | `/services/stepping-stones-pathways/` |
| concrete-dg-gravel | `/services/concrete-dg-gravel/` |
| irrigation-drainage | `/services/irrigation-drainage/` |
| vinyl-fencing | `/services/vinyl-fencing/` |
| projects | `/projects/` |
| about-us | `/about-us/` |
| blog | `/blog/` |
| contact-us | `/contact-us/` |

## Forms

Estimate fields use the `[o1b_estimate]` shortcode (`novamira-sandbox/o1b-estimate.php`). Not an Elementor Pro form.

## After LocalWP is up

1. Connect Novamira to the Local site URL.
2. Import media from `images/` using keys in `media-manifest.json`.
3. Import `elementor/*.json` onto the six pages.
4. Recreate Happy Addons header/footer from `elementor/header.json` and `elementor/footer.json`.
5. Regenerate Elementor CSS.

Credentials stay out of git.
