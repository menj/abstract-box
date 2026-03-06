# Upgrading & Roadmap

Upgrade notes for site owners, and a record of planned features and suggestions for future development.

---

## Upgrade Notes

### 2.2.6

**Directory restructure — no action required for standard installations.**

If any external code (a theme, a child plugin, or a custom snippet) references the plugin's internal files directly by path, those paths have changed:

| Old path | New path |
|---|---|
| `abstract-box/css/` | `abstract-box/assets/css/` |
| `abstract-box/js/` | `abstract-box/assets/js/` |
| `abstract-box/inc/` | `abstract-box/includes/` |
| `abstract-box/views/` | `abstract-box/includes/views/` |

Standard installations that use only the shortcode, block, or Customizer are entirely unaffected.

---

### 2.2.0

**Style option values changed.** If any code reads the saved `abstract_box_options[style]` option directly, note that `"default"` is now `"modern"` and `"custom"` is now `"academic"`. The plugin's own sanitizer migrates saved values automatically on the next settings save. Legacy values continue to render correctly until then.

---

### 2.1.1

**Tab navigation URLs changed.** The `_ab_tab_nonce` parameter was removed from all settings tab URLs. Any bookmarked URL containing `_ab_tab_nonce=` will still load correctly — it simply falls back to the Appearance tab gracefully.

---

### 2.1.0

**Settings storage migrated from `theme_mod` to `get_option()`.** Options previously saved via the Customizer under `theme_mod` are not automatically migrated. If a site was using the Customizer to configure Abstract Box prior to v2.1.0, settings will need to be re-entered on the Settings page once after upgrading.

---

## Roadmap

Planned features and suggestions recorded here for future development. Items are loosely grouped by area; no fixed release schedule.

---

### Appearance & Styles

> **Implemented in 2.2.8:** Bulletin style, per-instance `style` attribute on shortcode and block, Gutenberg block list support with `block-editor.css`.


- **RTL support** — verify all seven styles render correctly under `direction: rtl`; adjust border placement for Ruled and Editorial styles where the left-side accent needs to mirror to the right.
- **Dark mode variant** — `@media (prefers-color-scheme: dark)` overrides for each style so boxes adapt automatically to OS-level dark mode without requiring a separate preset.
- **Additional box styles** — candidate styles noted during development:
  - *Timeline* — vertical left-border with date stamp, suitable for historical or chronological abstracts
  - *Callout* — icon slot on the left (info, warning, note), modelled on documentation-style callout boxes
  - *Inline* — minimal inline variant with no border or background, purely typographic
- **Custom accent image / texture** — optional background texture or pattern overlay for the Card and Summary styles.
- **Border style control** — allow selection of `solid`, `dashed`, or `dotted` for styles that use a border (Ruled, Editorial, Academic).

---

### Colour & Typography

- **Per-style colour overrides** — currently all styles share the same five colour values. Allow each style to have its own saved palette so switching styles also switches the colour scheme cleanly.
- **Google Fonts integration** — optional enqueue of a selected Google Font as the font family, with a font selector that previews the typeface name in its own face.
- **CSS custom property export** — a read-only field in the Advanced tab showing the generated `--ab-*` variable block, so developers can copy it into their theme for use outside the shortcode.

---

### Shortcode & Block

- **Block transforms** — register a block transform so the `[abstract]` shortcode can be converted to the native block in one click from the block editor toolbar.
- **Block variations** — register named block variations (Modern, Academic, Summary, etc.) so each style is accessible as a distinct entry in the block inserter.
- **`[abstract]` nested shortcode support** — explicitly test and document behaviour when `[abstract]` is used inside `[columns]`, `[accordion]`, or other wrapper shortcodes from popular page builders.
- **Shortcode builder UI** — a modal or inline tool on the Usage tab that assembles the shortcode from form inputs and outputs a copyable string.

---

### Admin & Settings

- **Import / export settings** — JSON export of the full `abstract_box_options` array, and a corresponding import field, so settings can be moved between sites without manual re-entry.
- **Reset to defaults button** — a single-click reset on the Appearance tab, distinct from saving, that restores all values to the plugin defaults without requiring a save.
- **Colour picker: hex input visible by default** — currently the hex value is hidden until the swatch is clicked. Consider making the hex field always visible in the compact colour row for power users.
- **Appearance tab preview position** — evaluate moving the preview panel above the form (sticky) rather than below it, so changes at the bottom of the tab are still visible in the preview without scrolling.

---

### Schema & SEO

- **`speakable` property** — add an optional `speakable` CSS selector array to the JSON-LD output, pointing at the abstract box content, for Google's speakable feature.
- **FAQ schema** — if the abstract box is used to summarise a FAQ page, offer an optional `FAQPage` schema type with `mainEntity` pairs extracted from shortcode attributes.
- **Breadcrumb / article position** — optional `position` property in the JSON-LD for sites using Abstract Box inside listicles or multi-part series.

---

### Developer & Architecture

- **Dedicated `languages/` directory** — generate a `.pot` template file and add it to the repository so translators have a starting point without needing to run `wp i18n make-pot` manually.
- **Plugin Check (PCP) CI** — add a GitHub Actions workflow that runs `plugin-check` on every push so regressions against WordPress.org guidelines are caught before submission.
- **Unit tests** — PHPUnit tests for `Helpers::get_options()`, `Helpers::font_stack()`, `Assets::build_inline_css()`, and `Shortcode::render()` as a minimum viable test suite.
- **`abstract-box-custom.css` removal** — the file is empty and retained only as a legacy stub. Remove in the next major version (3.0.0) with a note in upgrade documentation.
- **Minified asset variants** — ship `abstract-box.min.css` and `admin-settings.min.js` alongside the unminified sources; enqueue minified in production, unminified when `SCRIPT_DEBUG` is true.

---

## Suggestions Log

Ideas raised during development that are not yet scheduled but are worth revisiting.

| Suggestion | Context | Status |
|---|---|---|
| Typography Suite — unified settings shared across Abstract Box, Auto Justify Content, and Endmark | Plugin suite coordination | Parked — requires inter-plugin settings API |
| `prefers-reduced-motion` guard on all hover transitions | Accessibility | Pending — add to next CSS pass |
| Block editor live preview using InnerBlocks rather than SSR | Editor UX | Pending — SSR currently works; InnerBlocks preview is a UX improvement only |
| Colour contrast checker extended to title/accent pair (currently only bg/text) | Accessibility | Pending |
| Settings page search / filter for sites with many options | UX | Low priority until option count grows significantly |

