# Changelog

All notable changes to the Abstract Box plugin are documented in this file.

---

## 2.1.3 — 2026-02-27

### Added

- **Usage tab in settings:** A new Usage tab has been added to the plugin settings page. It covers: a three-step getting-started guide, full shortcode reference with all attributes and copyable examples, block editor walkthrough, explanation of global settings vs per-instance overrides, comparison of the Default and Academic box styles, CSS class reference for theme customisation, and an uninstall note. All code examples have one-click copy buttons.

---

## 2.1.2 — 2026-02-27

### Changed

- **Line endings normalised:** Six files (`inc/helpers.php`, `inc/admin/customizer.php`, `inc/frontend/block.php`, `views/shortcode.php`, `js/block-editor.js`, `js/customizer-preview.js`) were using Windows CRLF `\r\n` line endings while the rest of the plugin used Unix LF `\n`. All files now use LF consistently.
- **`Helpers::get_defaults()` memoized:** The defaults array was rebuilt as a new `array()` allocation on every call, and the method is invoked eight or more times per settings page render. A `private static $defaults` property now caches the array after the first call so all subsequent calls return the same reference with no allocation cost.
- **`Assets`: extracted `resolve_style()` helper:** The three-line CSS file and handle resolution block was duplicated identically in `enqueue_frontend()` and `enqueue_block_editor()`. A private `resolve_style( string $context, string $style ): array` method centralises the mapping — a change to the handle convention or file naming now only needs to be made in one place.
- **`render_preview()` redundant validation removed:** The method was re-running `sanitize_hex_color()` on all five colour values and `absint()` on the border radius before building the preview HTML. These values are already validated by `sanitize_options()` at save time. The redundant re-validation has been removed; `Helpers::get_options()` is called once and the stored values are used directly.
- **Block editor: all shortcode attributes now exposed:** `Block::render_block()` previously only mapped four of the eight attributes that `Shortcode::render()` accepts (`title`, `subtitle`, `bg_color`, `text_color`). The remaining four (`title_tag`, `bg_color_end`, `title_color`, `accent_color`) were silently ignored, making them inaccessible to Gutenberg block users. All eight are now mapped. The JS block definition (`block-editor.js`) registers all nine attributes (including `content`) and the inspector panel now includes a Title Tag selector and individual colour pickers for Background End, Title Colour, and Accent Colour.

---

## 2.1.1 — 2026-02-26

### Fixed

- **PHP 8 compatibility:** `Shortcode::render()` defaulted `$content` to `null`; `do_shortcode( null )` triggers a `TypeError` on PHP 8+. Default changed to `''` with an explicit `(string)` cast as a belt-and-braces guard.
- **Tab navigation nonce removed:** `$_GET['tab']` was guarded by `wp_verify_nonce()`, causing every direct URL load of the settings page to silently fall back to the Appearance tab (the nonce is absent without a prior page load). Nonces protect write operations; tab navigation is read-only. Replaced with a direct `array_key_exists()` check against the known tab slugs, which is both correct and sufficient. The `_ab_tab_nonce` parameter is removed from all tab URLs.
- **Frontend CSS enqueue timing:** `Assets::enqueue_frontend()` was called directly inside `Shortcode::render()`, which executes during `the_content` — after `wp_head` has already fired. Styles were therefore output to `wp_footer` rather than `<head>`, risking a flash of unstyled content. Fixed by hooking `enqueue_frontend()` to `wp_enqueue_scripts` in `Assets::init()`, which is the correct WordPress lifecycle hook for frontend styles.
- **Plugin header URLs updated:** `Plugin URI` changed from `https://menj.net/abstract-box` to `https://github.com/menj/abstract-box`; `Author URI` changed from `https://menj.org` to `https://github.com/menj`. Both previous URLs were private or unavailable without authentication, failing the WordPress.org plugin review requirement that header URLs return HTTP 200 publicly.
- **Inline CSS: validate-not-escape pattern (`wp_add_inline_style`):** The previous code applied `esc_attr()` to colour values before interpolating them into the CSS string — escaping is the wrong defence for CSS context. Refactored into a private `Assets::build_inline_css()` helper that validates every value against its expected format (`sanitize_hex_color()` for colours, `absint()` + range clamp for border radius, closed allowlist check for font family) and substitutes a known-safe default if any value fails. Nothing untrusted can enter the CSS string. Eliminates code duplication between `enqueue_frontend()` and `enqueue_block_editor()`.
- **JSON-LD output flags:** `wp_json_encode()` was called with `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`. These flags suppress the default escaping of forward slashes, allowing `</script>` sequences to pass through unescaped and break out of the `<script>` tag. Removed both flags; `wp_json_encode()` with no flags safely escapes slashes and non-ASCII characters by default. Added a `false === $json` guard so a failed encode silently returns rather than outputting a broken script tag.
- **Double-encoding in `render_preview()`:** `$font_stack` was passed through `esc_attr()` at assignment time, then the entire `$container_style` string containing it was passed through `esc_attr()` again at output. The early `esc_attr()` was removed; the font value is already safe because `Helpers::font_stack()` returns only from its own hardcoded `$stacks` array.
- **Unescaped echo in tab active class:** `echo ( $active_tab === $slug ) ? 'nav-tab-active' : ''` in `views/admin-settings.php` lacked an escaping function. Wrapped with `esc_attr()`.

---

## 2.1.0 — 2026-02-24

### Added
- **Conditional Asset Loading:** The plugin's CSS is now only loaded in the frontend on pages that actually contain the `[abstract]` shortcode. This is a massive page-speed optimization to prevent useless CSS payloads.
- **MVC Views Directory:** Extracted all HTML components entirely out of PHP logic files and placed them securely inside a `views/` directory for pure layout management.
- **Accessibility Contrast Warning:** Implemented a real-time WCAG color contrast calculator in the backend (using Javascript `luminance` math) to warn users when their background and text customizer combinations fall below the `4.5:1` readability threshold.
- **Per-Instance Overrides:** Unlocked the shortcode to accept specific inline parameter colour properties (`bg_color`, `bg_color_end`, `text_color`, `title_color`, `accent_color`). Users can now override the global settings box selectively on any post without breaking structure configurations.
- **Plugin Shortcut Link:** Injected a quick `Settings` hyperlink into the WordPress administrator plugins table directly connecting to `options-general.php?page=abstract-box`.
- **Native Gutenberg Block:** Added a fully-integrated native Vanilla JS block component for the WordPress Block Editor (`/abstract`). Gives users a full WYSIWYG live preview of their styled abstract box right in the editor workspace with inspector control sidebars to override parameter color palettes on the fly.
- **Color Presets:** Added four pre-configured color palettes (Dark Mode, Academic Sepia, Ocean Blue, and Default) directly on the admin settings page allowing users to instantly stylize their abstract boxes.

### Changed
- **OOP Architecture Rewrite:** Converted the entire plugin away from a spaghetti procedural approach into proper, pure Object-Oriented PHP using the `Menj\AbstractBox` namespace and PSR-4 style autoloading logic.
- **Directory map clean up**: The `includes/` logic folder was fully replaced with `inc/frontend` and `inc/admin` mapping cleanly.
- **Dynamic Semantic Layouts:** Shortcode HTML wrapper titles now intelligently parse via `$title_tag` mapping instead of a hardcoded `<h2>`. This fixes broken screen reader heading outline hierarchies if a box falls underneath an `h4` or deeper. Changed default to generic `div`.

### Fixed
- **Deep Nesting Block Drops:** Removed an aggressive `wp_kses_post` that prevented `<iframe>` elements and rich block structures from surviving inside embedded shortcode outputs.
- **Inline Style Duplication Logic Bug:** Ensured frontend CSS variables are guaranteed to only insert one inline `<style>` tag no matter how many `[abstract]` codes sit on a single page.
- **Double Enqueue Conflict:** Eliminated an extra unnecessary `wp-color-picker` native enqueue from settings since it hooks as a dependency properly inside `admin-settings.js` directly.
- **Live Preview Border parsing:** Enforced strictly typed `parseInt(10)` during Customizer border-radius dragging to stave off any String interpolation errors ending up as `NaNpx`.

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
