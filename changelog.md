# Changelog

All notable changes to the Abstract Box plugin are documented in this file.

---

## 2.2.9 — 2026-03-06

### Added — Configurable bullet dot and bulletin stripe colours

Previously bullet dots and the Bulletin stripe always used `--ab-accent-color`. They are now independent optional settings under Appearance → Colours.

- **Bullet Dots colour** (`bullet_color`) — controls `--ab-bullet-color` on `ul li::before`, nested bullets, and `ol li::marker`. Defaults to empty, which inherits the accent colour. Fallback chain: `--ab-bullet-color` → `--ab-accent-color` → hardcoded default.
- **Bulletin Stripe colour** (`stripe_color`) — controls `--ab-stripe-color` on the Bulletin style border and both stripe pseudo-elements. Same fallback chain to `--ab-accent-color`.
- Both pickers appear in the Colours compact card on the Appearance tab, below Accent Colour.
- Both emit CSS custom properties via `build_inline_css()` only when set (no empty variable declarations).
- Live preview updates immediately when either picker changes.
- `block-editor.css` updated to use `--ab-bullet-color` fallback chain so editor bullets match frontend.

---

## 2.2.8 — 2026-03-06

### Added — Bulletin box style; Gutenberg block list support

**Bulletin style (Style 8):**
- Full solid border on all four sides using the accent colour.
- Diagonal hazard-stripe pattern on the right edge and bottom edge via `::before` and `::after` pseudo-elements with `repeating-linear-gradient`.
- Stripe width controlled by `--ab-bulletin-stripe` custom property (default 20 px).
- Title rendered at `font-weight: 700` to match news bulletin convention.
- Available in the shortcode via `style="bulletin"`, in the Gutenberg block Style selector, and in the admin visual selector (now 8 columns).
- Accent colour controls both the border and stripe colour — changes to the colour picker reflect immediately in the live preview.

**Gutenberg block list support:**
- `block-editor.css` created and registered as `editor_style` — list bullet and marker styles now mirror the frontend inside the Gutenberg editor.
- `allowedBlocks` set on InnerBlocks: `core/paragraph`, `core/list`, `core/heading`, `core/quote`, `core/separator`.
- `style` attribute added to block schema (PHP and JS) — per-block style override now available in the inspector under "Abstract Settings".
- `style` attribute passed through `render_block()` to `Shortcode::render()` so block and shortcode style overrides use the same code path.

**Shortcode:**
- `style` attribute added to `shortcode_atts` — any of the 8 style values can now be set per-instance, overriding the global setting. e.g. `[abstract style="bulletin"]`.

---

## 2.2.7 — 2026-03-06

### Added — List styles in box content

Previously `<ul>` and `<ol>` inside the shortcode or block rendered without bullets or numbers because no list styles were defined inside `.abstract-box__content`, leaving theme CSS resets to strip them.

- `ul` items now render with circular accent-coloured bullet markers via `::before`, matching `--ab-accent-color`.
- `ol` items render with decimal numbering; `::marker` colour set to the accent colour with `font-weight: 600`.
- Nested `ul` bullets render at 80% size with a faded (55% opacity) accent tint to indicate depth.
- Indentation, spacing, and `text-align: left` set explicitly so lists are never affected by theme resets.
- Last `li` margin removed, consistent with existing `p:last-child` behaviour.

---

## 2.2.6 — 2026-02-27

### Changed — Directory structure rationalised

- `css/` and `js/` moved under `assets/css/` and `assets/js/` — asset files grouped together, off the plugin root.
- `inc/` renamed to `includes/` — full word, consistent with WordPress Core conventions.
- `views/` moved to `includes/views/` — view partials are internal templates, not standalone files; they belong alongside the logic they serve.
- All path constants and URL references updated throughout (`assets.php`, `block.php`, `customizer.php`, `settings.php`, `shortcode.php`, all view includes).
- `upgrading.md` added — upgrade notes per version and a structured feature roadmap.

---

## 2.2.5 — 2026-02-27

### Fixed — Preview panel now appears on Appearance tab only

The preview was rendered for all non-Usage tabs, so it appeared on the Schema and Advanced tabs where it is irrelevant.

---

## 2.2.4 — 2026-02-27

### Changed — Admin Appearance tab layout consolidated

- **Colours section:** Five individual colour picker cards replaced with a single compact card. Each picker is a label + swatch row separated by a hairline divider. Colours section reduced from 6 cards to 2.
- **Typography & Shape section:** Border Radius and Font Family combined into a single compact two-row card instead of two separate cards.
- `wp-color-result-text` ("Select Colour" label) hidden inside compact rows — the swatch button is self-explanatory.

---

## 2.2.3 — 2026-02-27

### Fixed — Live preview was static; now updates immediately on every change

Previously the preview was server-rendered once at page load from saved settings and did not respond to any form changes until the page was reloaded after saving.

- Preview element rewritten to use real `.abstract-box` CSS classes and CSS custom properties, identical to frontend output.
- Frontend stylesheet (`abstract-box.css`) now enqueued on the admin settings page so preview CSS rules apply correctly.
- Font stacks passed to JavaScript via `wp_localize_script` so font family changes render correctly without a server round-trip.
- `updatePreview()` function added to `admin-settings.js`, wired to: Box Style radio buttons, all colour pickers, font family select, border radius input, and preset buttons.

---

## 2.2.2 — 2026-02-27

### Changed — Style-specific hover effects

Each style now has a hover effect native to its structural character rather than a generic lift or shadow.

- **Modern** — deeper lift (4 px), multi-layer shadow, `brightness(1.025)` lifts the gradient.
- **Academic** — border transitions dotted → solid in the accent colour; background tints lightly with the accent.
- **Minimal** — background tints with the accent; upward glow radiates from the top accent line without layout shift.
- **Card** — lift (6 px) + `scale(1.005)` + deep three-layer shadow.
- **Ruled** — nudge right (4 px); left bar grows 5 px → 7 px via `inset box-shadow` with no layout shift.
- **Editorial** — border colour activates fully to accent + thin `box-shadow` ring reinforces it.
- **Summary** — background brightens slightly + soft tinted glow ring expands outward.

Base `transition` property expanded to include `border-color`, `background-color`, `filter`, and `outline-color`.

---

## 2.2.1 — 2026-02-27

### Added — Editorial and Summary box styles

- **Editorial** — solid full border on all four sides, flat white background, no shadow. Modelled on news article pull-quote boxes.
- **Summary** — soft tinted background, no border, generous rounded corners. Modelled on AI summary / ringkasan boxes.

### Changed

- Admin visual selector expanded to all 7 styles (Modern, Academic, Minimal, Card, Ruled, Editorial, Summary) in a responsive 7-column grid with CSS mini-previews.
- Box Style Reference info card updated to document all 7 styles in a 3-column grid.
- Responsive breakpoints: 7-col → 4-col at 980 px → 3-col at 782 px → 2-col on mobile.

### Fixed

- Font family allowlist in `assets.php` only covered 3 of the 6 options. Selecting Humanist Sans, Monospace, or Slab Serif silently fell back to Sans-Serif. All 6 fonts now validated correctly.

---

## 2.2.0 — 2026-02-27

### Added — Full style system

- Five box styles: Modern, Academic, Minimal, Card, Ruled (up from 2).
- Eight colour presets: Default, Dark, Sepia, Ocean, Forest, Rose, Midnight, Sand (up from 4). Each preset also sets the recommended Box Style automatically.
- Visual mini-previews on each box style selector card.

### Changed

- Box Style options renamed from Default/Custom to Modern/Academic — names now describe the visual rather than the mode.
- All styles consolidated into one CSS file (`abstract-box.css`); `abstract-box-custom.css` retired.
- `Assets::resolve_style()` removed; the single stylesheet is always loaded.

### Migration (automatic, no action required)

- Saved style `"default"` migrates to `"modern"` on next save.
- Saved style `"custom"` migrates to `"academic"` on next save.

---

## 2.1.9 — 2026-02-27

### Changed — Usage tab content redistribution

- Getting Started and Box Style Reference moved to the Appearance tab (closer to the settings they describe).
- CSS Class Reference moved to the Advanced tab (developer-facing content alongside developer-facing settings).
- Uninstalling section removed (standard WordPress behaviour, not worth a dedicated section).
- Copy trimmed throughout for concision.
- Usage tab now contains only the shortcode/block reference and attributes table.

---

## 2.1.8 — 2026-02-27

### Changed — Admin UI rewrite (design_system v1.0.0)

- All CSS scoped to `.abstract-box-settings`; no styles exist outside the plugin wrapper.
- 97 `var()` calls replace hardcoded hex values. Colour tokens defined on `.abstract-box-settings`, not `:root`.
- All `!important` declarations removed; specificity handled via scoped selectors.
- `do_settings_sections()` and its `<table>` output removed. Settings rendered as `<div class="ab-field-card">` elements via `render_appearance_tab()`, `render_schema_tab()`, and `render_advanced_tab()`.
- Boolean settings (`use_theme_css`, `enable_schema`, `hover_effect`) now render as CSS toggle switches instead of plain checkboxes.
- Box Style uses a visual card selector instead of a `<select>` dropdown.
- SVG tab icons now pass through `wp_kses()` with an explicit allowlist instead of being echoed raw.
- JavaScript event binding scoped to `WRAPPER` constant; no global jQuery listeners.
- Footer credit ("Developed by MENJ") with GitHub link added to all settings tabs.

---

## 2.1.7 — 2026-02-27

### Changed — Admin UI redesign (pre-design_system)

- Card-based settings layout with 12 px rounded corners and hover lift effect.
- Gradient header banner (slate `#1B3C53` → teal `#2E6A8E`) with plugin icon, title, subtitle, and version badge.
- Tab navigation: SVG icon + label, slate underline indicator for active tab.
- Gradient Save Settings button with drop shadow and smooth hover/focus states.
- Responsive layout: header collapses to column on narrow screens.

---

## 2.1.6 — 2026-02-27

### Fixed — Gutenberg block rewritten to InnerBlocks

- Content now managed via InnerBlocks (correct pattern for dynamic blocks) rather than a `RichText` string attribute, which caused REST API validation errors.
- `save()` returns `InnerBlocks.Content` so inner blocks are correctly serialised into post content.
- `content` attribute removed from PHP schema and JS attributes, eliminating the "Invalid parameter: attributes" REST error.
- `wp-server-side-render` script dependency removed.

---

## 2.1.5 — 2026-02-27

### Fixed

- "Invalid parameter: attributes" Gutenberg error: full attribute schema now declared in `register_block_type()` so WordPress can validate REST parameters.
- `ServerSideRender` no longer receives the `content` attribute, which caused REST validation failure on block preview.

---

## 2.1.4 — 2026-02-27

### Changed

- Font Family selector expanded from 3 to 6 options: Sans-Serif, Serif, Humanist Sans, Monospace, Slab Serif, System Default. Allowlist and Customizer control updated consistently across `helpers.php`, `settings.php`, and `customizer.php`.

---

## 2.1.3 — 2026-02-27

### Added

- Usage tab with shortcode reference, copyable examples, block editor guide, style comparison, CSS class reference, and getting-started steps.

### Security

- JSON-LD output now encodes with `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT`; `</script>` breakout structurally impossible.
- Shortcode inner content passed through `wp_kses_post()` after `do_shortcode()`.
- `title_tag` attribute escaped with `tag_escape()` instead of `esc_html()`.

### Performance

- Block editor script now registered with `in_footer = true`.

---

## 2.1.2 — 2026-02-27

### Changed

- Line endings normalised to LF across all files.
- `Helpers::get_defaults()` memoized with `private static $defaults`; built once per request, reused on all subsequent calls.
- `Assets::resolve_style()` private helper extracted to remove duplicated CSS file/handle resolution code.
- `render_preview()` no longer re-validates option values already sanitized at save time.

### Fixed

- Gutenberg block now maps all 8 shortcode attributes. Previously `title_tag`, `bg_color_end`, `title_color`, and `accent_color` were silently ignored.

---

## 2.1.1 — 2026-02-26

### Fixed

- `Shortcode::render()` `$content` default changed from `null` to `''` with explicit `(string)` cast — `do_shortcode(null)` causes `TypeError` on PHP 8+.
- Tab navigation nonce removed; `$_GET['tab']` now validated against known slugs only. Nonces protect write operations, not read-only navigation.
- `enqueue_frontend()` moved to `wp_enqueue_scripts` hook; was called inside shortcode render after `wp_head` had already fired, causing styles to output to footer.
- Plugin URI and Author URI updated to publicly accessible GitHub URLs (WordPress.org requirement).
- Inline CSS refactored to validate-not-escape pattern. New `Assets::build_inline_css()` helper validates all option values before CSS interpolation; no untrusted data can enter the CSS string.
- `wp_json_encode()` `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE` flags removed; encode failure guard added.
- `render_preview()` double-encoding fixed: premature `esc_attr()` on `$font_stack` removed; escaping applied once at output only.
- Unescaped `echo` on tab active class wrapped with `esc_attr()`.

---

## 2.1.0 — 2026-02-24

### Added

- Conditional asset loading: frontend CSS only loads on pages containing the `[abstract]` shortcode.
- MVC-inspired directory structure (`views/`, `inc/frontend/`, `inc/admin/`).
- Real-time WCAG contrast checker in admin settings (warns below 4.5:1).
- Per-instance colour overrides via shortcode attributes (`bg_color`, `bg_color_end`, `text_color`, `title_color`, `accent_color`).
- Settings shortcut link in the plugins table.
- Native Gutenberg block with Server-Side Rendering and inspector controls.
- Four colour presets (Dark, Sepia, Ocean, Default).

### Changed

- Full OOP rewrite using `Menj\AbstractBox` namespace and PSR-4 autoloading.
- Directory restructured: `includes/` replaced with `inc/frontend/` and `inc/admin/`.
- `title_tag` now configurable; defaults to `<div class="abstract-box__title">` instead of hardcoded `<h2>`.
- BEM-style CSS class naming throughout.
- Settings migrated from `theme_mod` to `get_option()` (Settings API); Customizer retained as a convenience layer.

### Fixed

- `wp_kses_post` prevented nested block elements (iframes, embeds) inside shortcode output.
- Double enqueue of frontend stylesheet resolved.

---

## 2.0.4 — 2026-02-20

### Fixed

- Nonce verification added to admin tab navigation, resolving Plugin Check `NonceVerification.Recommended` flag.

---

## 2.0.3 — 2026-02-20

### Fixed

- `phpcs:ignore` annotation added for `$_GET['tab']` read with justification; `wp_unslash()` added.

---

## 2.0.2 — 2026-02-20

### Removed

- Manual `load_plugin_textdomain()` call (deprecated since WordPress 4.6; auto-loaded by WordPress).

---

## 2.0.1 — 2026-02-20

### Fixed

- All dynamic output in `abstract_box_render_preview()` properly escaped; call site wrapped in `wp_kses()`.

---

## 2.0.0 — 2026-02-20

A complete restructure of the plugin architecture.

### Added

- Tabbed admin settings page (Appearance, Schema, Advanced).
- Five colour pickers (title, text, background start/end, accent) using CSS custom properties.
- Font family selector, border radius control, schema type selector, hover effect toggle, custom CSS class option.
- JSON-LD structured data toggle.
- Customizer live preview via `customizer-preview.js`.
- `uninstall.php` for clean removal.
- `ABSTRACT_BOX_VERSION` constant for cache-busting.
- Static preview panel in admin settings.
- `CHANGELOG.md`.

### Changed

- CSS moved to `/css/`, JS to `/js/`, PHP modules to `/inc/`.
- All function prefixes standardised to `abstract_box_`.
- BEM class naming: `abstract-box`, `abstract-box__title`, `abstract-box__subtitle`, `abstract-box__content`.
- Settings migrated from `theme_mod` to `get_option()`.
- Hover effect wrapped in `@media (hover: hover)`.
- Content wrapper changed from `<p>` to `<div>`.

### Fixed

- Custom style `sanitize_callback` was `absint`, coercing strings to `0`; replaced with allowlist validation.
- Custom style CSS class never applied on frontend; now conditionally adds `abstract-box--custom`.
- Double stylesheet enqueue consolidated into a single callback.

### Removed

- `abstract-box.phps` source-highlighting file.
- `ob_start()` / `ob_end_clean()` wrapping the plugin file.

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
