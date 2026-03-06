<?php
/**
 * Abstract Box — Usage tab view.
 *
 * Shortcode reference and block editor notes only.
 * Getting Started → Appearance tab.
 * CSS class reference → Advanced tab.
 *
 * @package AbstractBox
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="abstract-box-usage">

    <!-- ── Shortcode ─────────────────────────────────────────────────── -->
    <section class="ab-usage-section">
        <h2 class="ab-usage-heading"><?php esc_html_e( 'Shortcode', 'abstract-box' ); ?></h2>

        <div class="ab-code-block">
            <code>[abstract title="Abstract"]Your abstract text here.[/abstract]</code>
            <button type="button" class="ab-copy-btn" data-copy="[abstract title=&quot;Abstract&quot;]Your abstract text here.[/abstract]">
                <?php esc_html_e( 'Copy', 'abstract-box' ); ?>
            </button>
        </div>

        <h3 class="ab-usage-subheading"><?php esc_html_e( 'Attributes', 'abstract-box' ); ?></h3>
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
                    <td><?php esc_html_e( 'Heading text shown above the content.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>subtitle</code></td>
                    <td><?php esc_html_e( '(none)', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Optional smaller line below the title.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>title_tag</code></td>
                    <td><code>div</code></td>
                    <td><?php esc_html_e( 'HTML element for the title. Accepts: div, h2–h6, p, span.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>bg_color</code></td>
                    <td><?php esc_html_e( 'Global', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Per-instance background start colour (hex).', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>bg_color_end</code></td>
                    <td><?php esc_html_e( 'Global', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Per-instance gradient end colour.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>text_color</code></td>
                    <td><?php esc_html_e( 'Global', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Per-instance body text colour.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>title_color</code></td>
                    <td><?php esc_html_e( 'Global', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Per-instance title colour.', 'abstract-box' ); ?></td>
                </tr>
                <tr>
                    <td><code>accent_color</code></td>
                    <td><?php esc_html_e( 'Global', 'abstract-box' ); ?></td>
                    <td><?php esc_html_e( 'Per-instance left border / accent colour.', 'abstract-box' ); ?></td>
                </tr>
            </tbody>
        </table>

        <h3 class="ab-usage-subheading"><?php esc_html_e( 'Example with overrides', 'abstract-box' ); ?></h3>
        <div class="ab-code-block">
            <code>[abstract title="Summary" subtitle="Section 1.2" title_tag="h3" bg_color="#1e293b" bg_color_end="#334155" text_color="#cbd5e1" title_color="#f8fafc" accent_color="#3b82f6"]Text here.[/abstract]</code>
            <button type="button" class="ab-copy-btn" data-copy='[abstract title="Summary" subtitle="Section 1.2" title_tag="h3" bg_color="#1e293b" bg_color_end="#334155" text_color="#cbd5e1" title_color="#f8fafc" accent_color="#3b82f6"]Text here.[/abstract]'>
                <?php esc_html_e( 'Copy', 'abstract-box' ); ?>
            </button>
        </div>
    </section>

    <!-- ── Block Editor ───────────────────────────────────────────────── -->
    <section class="ab-usage-section">
        <h2 class="ab-usage-heading"><?php esc_html_e( 'Block Editor', 'abstract-box' ); ?></h2>
        <p><?php esc_html_e( 'Search for "Abstract Box" in the block inserter (Text category). The right-hand inspector exposes Abstract Settings (title, subtitle, tag) and Colour Overrides — leave any colour picker empty to inherit the global setting.', 'abstract-box' ); ?></p>
        <p class="ab-usage-note"><?php esc_html_e( 'Colour overrides set on the Appearance tab apply site-wide. Shortcode attributes and block inspector values override those defaults for that one instance only.', 'abstract-box' ); ?></p>
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
