# Changelog

All notable changes to the Abstract Box plugin are documented in this file.

---

## 2.0.4 — 2026-02-20

### Fixed
- Added nonce verification to admin settings tab navigation, fully resolving Plugin Check's `NonceVerification.Recommended` flag at line 101. Tab links now carry an `_ab_tab_nonce` parameter generated via `wp_create_nonce()`, verified with `wp_verify_nonce()` before `$_GET['tab']` is read. Falls back to the Appearance tab when the nonce is absent or invalid. Replaces the previous `phpcs:ignore` approach.

---

## 2.0.3 — 2026-02-20

### Fixed
- Added `phpcs:ignore` annotation with justification for the `$_GET['tab']` read in the settings page renderer, resolving Plugin Check's `NonceVerification.Recommended` flag. The tab parameter is a read-only navigation aid whose value is whitelist-validated against known tab slugs; the form submission itself is already protected by `settings_fields()` which outputs a nonce verified by `options.php`.
- Added `wp_unslash()` to the `$_GET['tab']` read for correct superglobal handling.
- Added inline documentation clarifying the existing nonce/capability protection provided by `settings_fields()` and `options.php`.

---

## 2.0.2 — 2026-02-20

### Removed
- Manual `load_plugin_textdomain()` call, discouraged since WordPress 4.6. WordPress auto-loads translations for plugins using the standard text-domain slug convention. The `Text Domain` and `Domain Path` plugin headers are retained.

---

## 2.0.1 — 2026-02-20

### Fixed
- Escaped all dynamic output in `abstract_box_render_preview()` to satisfy Plugin Check `OutputNotEscaped` rule: individual colour values sanitised with `sanitize_hex_color()`, border radius with `absint()`, font stack with `esc_attr()`, compound style strings with `esc_attr()`, and text with `esc_html__()`.
- Call site wrapped in `wp_kses()` with an explicit `div`/`h2`/`p` allowlist.

---

## 2.0.0 — 2026-02-20

A complete restructure of the plugin architecture, resolving all known bugs and adding a full settings interface.

### Added
- Tabbed admin settings page under Settings → Abstract Box (Appearance, Schema, Advanced).
- Configurable colour scheme with five colour pickers (title, text, background start, background end, accent).
- CSS custom properties (`--ab-*`) for theme-aware, dynamic colour output.
- Font family selector: Sans-Serif (Modernist), Serif (Traditional), System Default.
- Border radius control (0–50 px).
- Schema type selector: CreativeWork, ScholarlyArticle, Article.
- Enable/disable toggle for JSON-LD structured data output.
- Custom CSS class option for targeted styling.
- Hover effect toggle (desktop-only via `@media (hover: hover)`).
- Customizer integration with live colour preview via `customizer-preview.js`.
- Admin colour picker initialisation via `admin-settings.js`.
- `uninstall.php` to clean up options and legacy theme_mods on deletion.
- `ABSTRACT_BOX_VERSION` constant, used for cache-busting all enqueued assets.
- `load_plugin_textdomain()` for full internationalisation support.
- `defined('ABSPATH') || exit;` security guard on all PHP files.
- Styles for `.abstract-box__subtitle` (previously unstyled).
- Static preview panel on the admin settings page.
- Custom style variant (`abstract-box--custom`) with academic/minimal aesthetic.
- `CHANGELOG.md`.

### Fixed
- **Broken custom style:** `sanitize_callback` on style selector was `absint`, coercing string values to `0`. Replaced with whitelist validation.
- **Non-functional custom style class:** Shortcode output never applied the `.custom-style` CSS class. Now conditionally adds `abstract-box--custom` when the custom style is selected.
- **Double stylesheet enqueue:** Two separate `wp_enqueue_scripts` callbacks could both fire and load the same stylesheet twice. Consolidated into a single unified callback in `includes/enqueue.php`.
- **Invalid HTML:** Content was wrapped in a `<p>` tag, breaking when block-level elements (lists, blockquotes) were used inside the shortcode. Changed to a `<div>` wrapper.
- **Hover layout shifts on mobile:** `translateY` hover effect now gated behind `@media (hover: hover)` so it only applies on pointer devices.

### Changed
- Complete directory restructure: CSS files moved to `/css/`, JS files to `/js/`, PHP modules to `/includes/`.
- Main plugin file reduced to a lean bootstrap (constants, i18n, module loader).
- All function prefixes standardised to `abstract_box_` (was inconsistent: `abstract_shortcode`, `abstract_box_styles`, etc.).
- BEM-style CSS class naming: `abstract-box`, `abstract-box__title`, `abstract-box__subtitle`, `abstract-box__content`.
- Settings migrated from `theme_mod` (Customizer-only) to `get_option()` (Settings API), with Customizer kept as a secondary convenience layer.
- Both stylesheets rewritten to use CSS custom properties with hardcoded fallbacks.

### Removed
- `abstract-box.phps` source-highlighting file (security risk, maintenance burden, already out of sync).
- `ob_start()` / `ob_end_clean()` wrapping the plugin file (served no purpose, masked errors).

---

## 1.1 — 2026-02-19

### Added
- Schema.org JSON-LD structured data output (`CreativeWork` with `abstract` property).
- Customizer style selector (Default / Custom).
- `apply_filters` hooks for schema type, payload, and output toggle.

---

## 1.0 — Initial Release

### Added
- `[abstract]` shortcode with `title` and `subtitle` attributes.
- Default stylesheet with gradient background, shadow, and hover effect.
- Customizer toggle to disable plugin CSS in favour of theme styles.
