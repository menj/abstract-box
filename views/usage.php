<?php
/**
 * Abstract Box — Usage tab view.
 *
 * Rendered inside the settings page when the Usage tab is active.
 * No form or save button — purely informational.
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="abstract-box-usage">

    <!-- ── Getting Started ───────────────────────────────────────────── -->
    <section class="ab-usage-section">
        <h2 class="ab-usage-heading"><?php esc_html_e( 'Getting Started', 'abstract-box' ); ?></h2>
        <ol class="ab-usage-steps">
            <li><?php esc_html_e( 'Set your preferred colours, font, and style on the Appearance tab and save.', 'abstract-box' ); ?></li>
            <li><?php esc_html_e( 'On any post or page, insert the block from the block inserter — search for "Abstract Box" — or paste the shortcode below into the content.', 'abstract-box' ); ?></li>
            <li><?php esc_html_e( 'Your abstract will appear on the page styled with your global settings. You can override colours per-instance using the block inspector or shortcode attributes.', 'abstract-box' ); ?></li>
        </ol>
    </section>

    <!-- ── Shortcode ─────────────────────────────────────────────────── -->
    <section class="ab-usage-section">
        <h2 class="ab-usage-heading"><?php esc_html_e( 'Shortcode', 'abstract-box' ); ?></h2>
        <p><?php esc_html_e( 'Paste this into any post or page content area:', 'abstract-box' ); ?></p>

        <div class="ab-code-block">
            <code>[abstract title="Abstract"]Your abstract text here.[/abstract]</code>
            <button type="button" class="ab-copy-btn" data-copy="[abstract title=&quot;Abstract&quot;]Your abstract text here.[/abstract]">
                <?php esc_html_e( 'Copy', 'abstract-box' ); ?>
            </button>
        </div>

        <h3 class="ab-usage-subheading"><?php esc_html_e( 'Available Attributes', 'abstract-box' ); ?></h3>
        <table class="ab-usage-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Attribute', 'abstract-box' ); ?></th>
                    <th><?php esc_html_e( 'Default', 'abstract-box' ); ?></th>
                    <th><?php esc_html_e( 'Description', 'abstract-box' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>title</code></td>
                    <td><code>Abstract</code></td>
                    <td><?php esc_html_e( 'The heading text shown above the abstract content.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>subtitle</code></td>
                    <td><?php esc_html_e( '(none)', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Optional line of smaller text shown below the title.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>title_tag</code></td>
                    <td><code>div</code></td>
                    <td><?php esc_html_e( 'HTML element used for the title. Accepts: div, h2, h3, h4, h5, h6, p, span. Use h2–h6 for correct heading hierarchy in your article.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>bg_color</code></td>
                    <td><?php esc_html_e( 'Global setting', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Override the background start colour for this instance only. Use a hex value, e.g. #f8fafc.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>bg_color_end</code></td>
                    <td><?php esc_html_e( 'Global setting', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Override the background end colour (gradient end). Omit to use a flat colour with bg_color.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>text_color</code></td>
                    <td><?php esc_html_e( 'Global setting', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Override the body text colour for this instance.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>title_color</code></td>
                    <td><?php esc_html_e( 'Global setting', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Override the title text colour for this instance.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>accent_color</code></td>
                    <td><?php esc_html_e( 'Global setting', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Override the left border / accent colour for this instance.', 'abstract-box' ); ?></td>
                </tr>
            </tbody>
        </table>

        <h3 class="ab-usage-subheading"><?php esc_html_e( 'Example with overrides', 'abstract-box' ); ?></h3>
        <div class="ab-code-block">
            <code>[abstract title="Summary" subtitle="Section 1.2" title_tag="h3" bg_color="#1e293b" bg_color_end="#334155" text_color="#cbd5e1" title_color="#f8fafc" accent_color="#3b82f6"]This paper examines the structural relationship between…[/abstract]</code>
            <button type="button" class="ab-copy-btn" data-copy='[abstract title="Summary" subtitle="Section 1.2" title_tag="h3" bg_color="#1e293b" bg_color_end="#334155" text_color="#cbd5e1" title_color="#f8fafc" accent_color="#3b82f6"]This paper examines the structural relationship between…[/abstract]'>
                <?php esc_html_e( 'Copy', 'abstract-box' ); ?>
            </button>
        </div>
    </section>

    <!-- ── Block Editor ───────────────────────────────────────────────── -->
    <section class="ab-usage-section">
        <h2 class="ab-usage-heading"><?php esc_html_e( 'Block Editor', 'abstract-box' ); ?></h2>
        <p><?php esc_html_e( 'The Abstract Box block is available in the Gutenberg block inserter under the Text category. Search for "Abstract Box" to find it.', 'abstract-box' ); ?></p>
        <p><?php esc_html_e( 'Once inserted, the block has two panels in the right-hand inspector:', 'abstract-box' ); ?></p>
        <ul class="ab-usage-list">
            <li><strong><?php esc_html_e( 'Abstract Settings', 'abstract-box' ); ?></strong> — <?php esc_html_e( 'Set the title, subtitle, and the HTML tag used for the title heading.', 'abstract-box' ); ?></li>
            <li><strong><?php esc_html_e( 'Colour Overrides', 'abstract-box' ); ?></strong> — <?php esc_html_e( 'Optionally override any of the five colours for this block only. Leave a colour picker empty to inherit the global setting.', 'abstract-box' ); ?></li>
        </ul>
        <p><?php esc_html_e( 'The block preview updates live in the editor as you type or adjust settings — what you see is what will appear on the published page.', 'abstract-box' ); ?></p>
    </section>

    <!-- ── Global Settings vs Per-instance Overrides ─────────────────── -->
    <section class="ab-usage-section">
        <h2 class="ab-usage-heading"><?php esc_html_e( 'Global Settings and Per-instance Overrides', 'abstract-box' ); ?></h2>
        <p><?php esc_html_e( 'The Appearance tab controls the default look of every abstract box across your entire site. You set colours, font, border radius, and style once and every box inherits those values automatically.', 'abstract-box' ); ?></p>
        <p><?php esc_html_e( 'If you need a specific box to look different — for example a dark-coloured box inside a light article — you can override any of the five colours using shortcode attributes or the block Colour Overrides panel. The override applies only to that one instance; everything else on the site is unaffected.', 'abstract-box' ); ?></p>
        <p><?php esc_html_e( 'You can also preview your global colour changes live without saving by going to Appearance → Customise in the WordPress admin and looking for the Abstract Box section.', 'abstract-box' ); ?></p>
    </section>

    <!-- ── Box Styles ─────────────────────────────────────────────────── -->
    <section class="ab-usage-section">
        <h2 class="ab-usage-heading"><?php esc_html_e( 'Box Styles', 'abstract-box' ); ?></h2>
        <p><?php esc_html_e( 'The Box Style option on the Appearance tab lets you choose between two visual styles:', 'abstract-box' ); ?></p>
        <div class="ab-style-comparison">
            <div class="ab-style-card">
                <h4><?php esc_html_e( 'Default', 'abstract-box' ); ?></h4>
                <p><?php esc_html_e( 'Gradient background, a solid left accent bar, and a soft drop shadow. Suits modern blog layouts and general-purpose use.', 'abstract-box' ); ?></p>
            </div>
            <div class="ab-style-card">
                <h4><?php esc_html_e( 'Custom (Academic)', 'abstract-box' ); ?></h4>
                <p><?php esc_html_e( 'Flat background, dotted border on all sides, no shadow, and an uppercase title with wider letter-spacing. Suits scholarly articles and academic publishing.', 'abstract-box' ); ?></p>
            </div>
        </div>
        <p class="ab-usage-note"><?php esc_html_e( 'If you prefer to style the abstract box entirely from your theme, enable Use Theme CSS on the Appearance tab. This stops the plugin loading its own stylesheet. Note that the global colour settings will still apply via CSS custom properties, so your theme can reference them as variables if needed.', 'abstract-box' ); ?></p>
    </section>

    <!-- ── Theme Styling ──────────────────────────────────────────────── -->
    <section class="ab-usage-section">
        <h2 class="ab-usage-heading"><?php esc_html_e( 'Styling from Your Theme', 'abstract-box' ); ?></h2>
        <p><?php esc_html_e( 'If you want to add extra CSS from your theme or a custom stylesheet, these are the class names to target:', 'abstract-box' ); ?></p>
        <table class="ab-usage-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Class', 'abstract-box' ); ?></th>
                    <th><?php esc_html_e( 'What it selects', 'abstract-box' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>.abstract-box</code></td>
                    <td><?php esc_html_e( 'The outer container of every abstract box.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>.abstract-box__title</code></td>
                    <td><?php esc_html_e( 'The title heading inside the box.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>.abstract-box__subtitle</code></td>
                    <td><?php esc_html_e( 'The optional subtitle line.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>.abstract-box__content</code></td>
                    <td><?php esc_html_e( 'The body text area inside the box.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>.abstract-box--custom</code></td>
                    <td><?php esc_html_e( 'Added when the Academic style is selected. Use this to write style rules that apply only in Custom mode.', 'abstract-box' ); ?></td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- ── Uninstall ──────────────────────────────────────────────────── -->
    <section class="ab-usage-section ab-usage-section--last">
        <h2 class="ab-usage-heading"><?php esc_html_e( 'Uninstalling', 'abstract-box' ); ?></h2>
        <p><?php esc_html_e( 'Deleting the plugin from the Plugins screen removes all saved settings from the database. No other data, tables, or files are left behind.', 'abstract-box' ); ?></p>
    </section>

</div>

<script>
(function () {
    document.querySelectorAll('.ab-copy-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var text = btn.getAttribute('data-copy');
            if ( navigator.clipboard && navigator.clipboard.writeText ) {
                navigator.clipboard.writeText(text).then(function () {
                    btn.textContent = '<?php echo esc_js( __( 'Copied!', 'abstract-box' ) ); ?>';
                    setTimeout(function () {
                        btn.textContent = '<?php echo esc_js( __( 'Copy', 'abstract-box' ) ); ?>';
                    }, 2000);
                });
            } else {
                /* Fallback for older browsers */
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity  = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                btn.textContent = '<?php echo esc_js( __( 'Copied!', 'abstract-box' ) ); ?>';
                setTimeout(function () {
                    btn.textContent = '<?php echo esc_js( __( 'Copy', 'abstract-box' ) ); ?>';
                }, 2000);
            }
        });
    });
}());
</script>
