<?php
namespace Menj\AbstractBox\Admin;

use Menj\AbstractBox\Helpers;

defined( 'ABSPATH' ) || exit;

class Settings {

    public function init() {
        if ( ! is_admin() ) {
            return;
        }
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function add_settings_page() {
        add_options_page(
            __( 'Abstract Box Settings', 'abstract-box' ),
            __( 'Abstract Box', 'abstract-box' ),
            'manage_options',
            'abstract-box',
            [ $this, 'render_settings_page' ]
        );
    }

    public function register_settings() {
        register_setting( 'abstract_box_group', 'abstract_box_options', array(
            'sanitize_callback' => [ $this, 'sanitize_options' ],
            'default'           => Helpers::get_defaults(),
        ) );
    }

    public function sanitize_options( $input ) {
        $defaults  = Helpers::get_defaults();
        $sanitized = array();

        // Migrate legacy style values saved before v2.2.0.
        $raw_style = $input['style'] ?? '';
        if ( 'default' === $raw_style ) { $raw_style = 'modern'; }
        if ( 'custom'  === $raw_style ) { $raw_style = 'academic'; }

        $valid_styles       = array( 'modern', 'academic', 'minimal', 'card', 'ruled', 'editorial', 'summary', 'bulletin', 'default', 'custom' );
        $sanitized['style'] = in_array( $raw_style, $valid_styles, true ) ? $raw_style : $defaults['style'];

        $sanitized['use_theme_css'] = ! empty( $input['use_theme_css'] );

        $color_keys = array( 'title_color', 'text_color', 'bg_color', 'bg_color_end', 'accent_color', 'bullet_color', 'stripe_color' );
        foreach ( $color_keys as $ck ) {
            $sanitized[ $ck ] = sanitize_hex_color( $input[ $ck ] ?? '' ) ?: $defaults[ $ck ];
        }

        $sanitized['border_radius'] = absint( $input['border_radius'] ?? $defaults['border_radius'] );
        $sanitized['border_radius'] = min( 50, max( 0, $sanitized['border_radius'] ) );

        $valid_fonts              = array( 'sans-serif', 'serif', 'humanist', 'monospace', 'slab', 'system' );
        $sanitized['font_family'] = in_array( $input['font_family'] ?? '', $valid_fonts, true )
                                        ? $input['font_family'] : $defaults['font_family'];

        $sanitized['enable_schema'] = ! empty( $input['enable_schema'] );

        $valid_schema_types       = array( 'CreativeWork', 'ScholarlyArticle', 'Article' );
        $sanitized['schema_type'] = in_array( $input['schema_type'] ?? '', $valid_schema_types, true )
                                        ? $input['schema_type'] : $defaults['schema_type'];

        $sanitized['custom_css_class'] = sanitize_html_class( $input['custom_css_class'] ?? '' );
        $sanitized['hover_effect']     = ! empty( $input['hover_effect'] );

        return $sanitized;
    }

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $tabs = array(
            'appearance' => __( 'Appearance', 'abstract-box' ),
            'schema'     => __( 'Schema', 'abstract-box' ),
            'advanced'   => __( 'Advanced', 'abstract-box' ),
            'usage'      => __( 'Usage', 'abstract-box' ),
        );

        $active_tab = 'appearance';
        if ( isset( $_GET['tab'] ) ) {
            $requested = sanitize_key( wp_unslash( $_GET['tab'] ) );
            if ( array_key_exists( $requested, $tabs ) ) {
                $active_tab = $requested;
            }
        }

        include ABSTRACT_BOX_DIR . 'includes/views/admin-settings.php';
    }

    /* ── Card renderer helpers ────────────────────────────────────── */

    /**
     * Render a setting row as a flex card.
     *
     * @param string $label       Field label.
     * @param string $description Optional helper text (plain text, will be escaped).
     * @param string $control_html Already-escaped HTML for the control.
     * @param bool   $stacked     If true, control sits below label instead of inline-right.
     */
    private function render_card( $label, $description, $control_html, $stacked = false ) {
        $modifier = $stacked ? ' ab-field-card--stacked' : '';
        echo '<div class="ab-field-card' . esc_attr( $modifier ) . '">';
        echo '<div class="ab-field__meta">';
        echo '<span class="ab-field__label">' . esc_html( $label ) . '</span>';
        if ( $description ) {
            echo '<span class="ab-field__description">' . esc_html( $description ) . '</span>';
        }
        echo '</div>';
        echo '<div class="ab-field__control">' . $control_html . '</div>';
        echo '</div>';
    }

    /**
     * Render a toggle switch control.
     * Returns safe HTML — the markup is static, user value only affects the
     * checked attribute which is rendered via checked().
     *
     * @param string $name    options array key.
     * @param bool   $checked Current value.
     * @return string
     */
    private function toggle( $name, $checked ) {
        ob_start();
        ?>
        <label class="ab-toggle">
            <input type="checkbox"
                   name="abstract_box_options[<?php echo esc_attr( $name ); ?>]"
                   value="1"
                   <?php checked( $checked ); ?> />
            <span class="ab-toggle__track" aria-hidden="true"></span>
        </label>
        <?php
        return ob_get_clean();
    }

    /* ── Tab: Appearance ──────────────────────────────────────────── */

    public function render_appearance_tab() {
        $opts = Helpers::get_options();
        settings_fields( 'abstract_box_group' );
        ?>

        <p class="ab-section-label"><?php esc_html_e( 'Layout', 'abstract-box' ); ?></p>

        <?php
        /* Box Style — visual selector with 7 styles */
        ob_start();
        $style_opts = array(
            'modern'    => array(
                'label' => __( 'Modern', 'abstract-box' ),
                'sub'   => __( 'Gradient · left bar · shadow', 'abstract-box' ),
            ),
            'academic'  => array(
                'label' => __( 'Academic', 'abstract-box' ),
                'sub'   => __( 'Flat · dotted border · uppercase', 'abstract-box' ),
            ),
            'minimal'   => array(
                'label' => __( 'Minimal', 'abstract-box' ),
                'sub'   => __( 'White · top accent line · clean', 'abstract-box' ),
            ),
            'card'      => array(
                'label' => __( 'Card', 'abstract-box' ),
                'sub'   => __( 'Elevated · full border · shadow', 'abstract-box' ),
            ),
            'ruled'     => array(
                'label' => __( 'Ruled', 'abstract-box' ),
                'sub'   => __( 'Thick left bar · title rule', 'abstract-box' ),
            ),
            'editorial' => array(
                'label' => __( 'Editorial', 'abstract-box' ),
                'sub'   => __( 'Solid full border · flat · no shadow', 'abstract-box' ),
            ),
            'summary'   => array(
                'label' => __( 'Summary', 'abstract-box' ),
                'sub'   => __( 'Tinted background · no border · rounded', 'abstract-box' ),
            ),
            'bulletin'  => array(
                'label' => __( 'Bulletin', 'abstract-box' ),
                'sub'   => __( 'Full border · diagonal stripe accent', 'abstract-box' ),
            ),
        );

        // Normalise legacy saved value for selected state.
        $saved_style = $opts['style'];
        if ( 'default' === $saved_style ) { $saved_style = 'modern'; }
        if ( 'custom'  === $saved_style ) { $saved_style = 'academic'; }

        echo '<div class="ab-visual-selector ab-visual-selector--8col">';
        foreach ( $style_opts as $val => $opt ) {
            $checked = ( $saved_style === $val );
            ?>
            <label class="ab-visual-option">
                <input type="radio"
                       name="abstract_box_options[style]"
                       value="<?php echo esc_attr( $val ); ?>"
                       <?php checked( $checked ); ?> />
                <span class="ab-visual-option__card">
                    <span class="ab-style-preview ab-style-preview--<?php echo esc_attr( $val ); ?>" aria-hidden="true">
                        <span class="ab-style-preview__title"></span>
                        <span class="ab-style-preview__line"></span>
                        <span class="ab-style-preview__line ab-style-preview__line--short"></span>
                    </span>
                    <span class="ab-visual-option__label"><?php echo esc_html( $opt['label'] ); ?></span>
                    <span class="ab-visual-option__sub"><?php echo esc_html( $opt['sub'] ); ?></span>
                </span>
            </label>
            <?php
        }
        echo '</div>';
        $style_html = ob_get_clean();
        $this->render_card(
            __( 'Box Style', 'abstract-box' ),
            __( 'Choose the visual style of the abstract box.', 'abstract-box' ),
            $style_html,
            true
        );

        /* Use Theme CSS toggle */
        $this->render_card(
            __( 'Use Theme CSS', 'abstract-box' ),
            __( 'Disable plugin styles and rely on your theme instead.', 'abstract-box' ),
            $this->toggle( 'use_theme_css', $opts['use_theme_css'] )
        );

        ?>
        <p class="ab-section-label"><?php esc_html_e( 'Colours', 'abstract-box' ); ?></p>
        <?php

        /* Colour presets */
        ob_start();
        ?>
        <div class="abstract-box-presets">
            <button type="button" class="button abstract-box-preset-btn" data-preset="default"><?php esc_html_e( 'Default', 'abstract-box' ); ?></button>
            <button type="button" class="button abstract-box-preset-btn" data-preset="dark"><?php esc_html_e( 'Dark', 'abstract-box' ); ?></button>
            <button type="button" class="button abstract-box-preset-btn" data-preset="sepia"><?php esc_html_e( 'Sepia', 'abstract-box' ); ?></button>
            <button type="button" class="button abstract-box-preset-btn" data-preset="ocean"><?php esc_html_e( 'Ocean', 'abstract-box' ); ?></button>
            <button type="button" class="button abstract-box-preset-btn" data-preset="forest"><?php esc_html_e( 'Forest', 'abstract-box' ); ?></button>
            <button type="button" class="button abstract-box-preset-btn" data-preset="rose"><?php esc_html_e( 'Rose', 'abstract-box' ); ?></button>
            <button type="button" class="button abstract-box-preset-btn" data-preset="midnight"><?php esc_html_e( 'Midnight', 'abstract-box' ); ?></button>
            <button type="button" class="button abstract-box-preset-btn" data-preset="sand"><?php esc_html_e( 'Sand', 'abstract-box' ); ?></button>
        </div>
        <?php
        $presets_html = ob_get_clean();
        $this->render_card(
            __( 'Colour Presets', 'abstract-box' ),
            __( 'Apply a pre-configured palette to all colour fields at once.', 'abstract-box' ),
            $presets_html,
            true
        );

        /* All five colour pickers — one compact card */
        $color_fields = array(
            'title_color'  => __( 'Title', 'abstract-box' ),
            'text_color'   => __( 'Body Text', 'abstract-box' ),
            'bg_color'     => __( 'Background Start', 'abstract-box' ),
            'bg_color_end' => __( 'Background End', 'abstract-box' ),
            'accent_color' => __( 'Accent', 'abstract-box' ),
            'bullet_color' => __( 'Bullet Dots', 'abstract-box' ),
            'stripe_color' => __( 'Bulletin Stripe', 'abstract-box' ),
        );
        ?>
        <div class="ab-info-card ab-colour-grid">
            <?php foreach ( $color_fields as $key => $label ) : ?>
            <div class="ab-colour-row">
                <label class="ab-colour-row__label"
                       for="ab-color-<?php echo esc_attr( $key ); ?>">
                    <?php echo esc_html( $label ); ?>
                </label>
                <input type="text"
                       id="ab-color-<?php echo esc_attr( $key ); ?>"
                       name="abstract_box_options[<?php echo esc_attr( $key ); ?>]"
                       value="<?php echo esc_attr( $opts[ $key ] ); ?>"
                       class="abstract-box-color-picker"
                       data-default-color="<?php echo esc_attr( Helpers::get_defaults()[ $key ] ); ?>" />
            </div>
            <?php endforeach; ?>
        </div>

        <p class="ab-section-label"><?php esc_html_e( 'Typography & Shape', 'abstract-box' ); ?></p>

        <!-- Border Radius + Font Family — one compact card -->
        <div class="ab-info-card ab-typo-grid">
            <div class="ab-typo-row">
                <label class="ab-typo-row__label" for="ab-border-radius">
                    <?php esc_html_e( 'Border Radius', 'abstract-box' ); ?>
                    <span class="ab-typo-row__hint"><?php esc_html_e( '0–50 px', 'abstract-box' ); ?></span>
                </label>
                <div class="ab-typo-row__control">
                    <input type="number"
                           id="ab-border-radius"
                           name="abstract_box_options[border_radius]"
                           value="<?php echo esc_attr( $opts['border_radius'] ); ?>"
                           min="0" max="50" step="1"
                           class="small-text" />
                    <span class="ab-unit">px</span>
                </div>
            </div>
            <div class="ab-typo-row ab-typo-row--bordered">
                <label class="ab-typo-row__label" for="ab-font-family">
                    <?php esc_html_e( 'Font Family', 'abstract-box' ); ?>
                </label>
                <div class="ab-typo-row__control">
                    <?php
                    $font_options = array(
                        'sans-serif' => __( 'Sans-Serif (Modernist)', 'abstract-box' ),
                        'serif'      => __( 'Serif (Traditional)', 'abstract-box' ),
                        'humanist'   => __( 'Humanist Sans', 'abstract-box' ),
                        'monospace'  => __( 'Monospace', 'abstract-box' ),
                        'slab'       => __( 'Slab Serif (Academic)', 'abstract-box' ),
                        'system'     => __( 'System Default', 'abstract-box' ),
                    );
                    ?>
                    <select name="abstract_box_options[font_family]" id="ab-font-family">
                        <?php foreach ( $font_options as $val => $lbl ) : ?>
                            <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $opts['font_family'], $val ); ?>>
                                <?php echo esc_html( $lbl ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <?php

        /* ── Getting Started ─────────────────────────────────────── */
        ?>
        <p class="ab-section-label"><?php esc_html_e( 'Getting Started', 'abstract-box' ); ?></p>
        <div class="ab-info-card">
            <ol class="ab-usage-steps">
                <li><?php esc_html_e( 'Set your colours, font, and style above and save.', 'abstract-box' ); ?></li>
                <li><?php esc_html_e( 'Insert the Abstract Box block from the block inserter, or paste the shortcode into any post or page.', 'abstract-box' ); ?></li>
                <li><?php esc_html_e( 'Override colours per-instance via the block inspector or shortcode attributes — see the Usage tab for the full reference.', 'abstract-box' ); ?></li>
            </ol>
        </div>

        <p class="ab-section-label"><?php esc_html_e( 'Box Style Reference', 'abstract-box' ); ?></p>
        <div class="ab-info-card">
            <div class="ab-style-comparison ab-style-comparison--3col">
                <div class="ab-style-card">
                    <h4><?php esc_html_e( 'Modern', 'abstract-box' ); ?></h4>
                    <p><?php esc_html_e( 'Gradient background, left accent bar, soft shadow.', 'abstract-box' ); ?></p>
                </div>
                <div class="ab-style-card">
                    <h4><?php esc_html_e( 'Academic', 'abstract-box' ); ?></h4>
                    <p><?php esc_html_e( 'Flat background, dotted border, uppercase spaced title.', 'abstract-box' ); ?></p>
                </div>
                <div class="ab-style-card">
                    <h4><?php esc_html_e( 'Minimal', 'abstract-box' ); ?></h4>
                    <p><?php esc_html_e( 'White background, single top accent line, no shadow.', 'abstract-box' ); ?></p>
                </div>
                <div class="ab-style-card">
                    <h4><?php esc_html_e( 'Card', 'abstract-box' ); ?></h4>
                    <p><?php esc_html_e( 'Elevated white card, full border, strong shadow.', 'abstract-box' ); ?></p>
                </div>
                <div class="ab-style-card">
                    <h4><?php esc_html_e( 'Ruled', 'abstract-box' ); ?></h4>
                    <p><?php esc_html_e( 'Thick left bar, hairline rule under the title.', 'abstract-box' ); ?></p>
                </div>
                <div class="ab-style-card">
                    <h4><?php esc_html_e( 'Editorial', 'abstract-box' ); ?></h4>
                    <p><?php esc_html_e( 'Solid border on all sides, flat white background, no shadow.', 'abstract-box' ); ?></p>
                </div>
                <div class="ab-style-card">
                    <h4><?php esc_html_e( 'Summary', 'abstract-box' ); ?></h4>
                    <p><?php esc_html_e( 'Soft tinted background, no border, generous rounded corners.', 'abstract-box' ); ?></p>
                </div>
            </div>
        </div>
        <?php
    }

    /* ── Tab: Schema ──────────────────────────────────────────────── */

    public function render_schema_tab() {
        $opts = Helpers::get_options();
        settings_fields( 'abstract_box_group' );

        ?>
        <p class="ab-section-label"><?php esc_html_e( 'Structured Data', 'abstract-box' ); ?></p>
        <?php

        $this->render_card(
            __( 'Enable Schema Output', 'abstract-box' ),
            __( 'Output schema.org JSON-LD structured data in the page head.', 'abstract-box' ),
            $this->toggle( 'enable_schema', $opts['enable_schema'] )
        );

        ob_start();
        ?>
        <select name="abstract_box_options[schema_type]" id="ab-schema-type">
            <option value="CreativeWork" <?php selected( $opts['schema_type'], 'CreativeWork' ); ?>>CreativeWork</option>
            <option value="ScholarlyArticle" <?php selected( $opts['schema_type'], 'ScholarlyArticle' ); ?>>ScholarlyArticle</option>
            <option value="Article" <?php selected( $opts['schema_type'], 'Article' ); ?>>Article</option>
        </select>
        <?php
        $this->render_card(
            __( 'Schema Type', 'abstract-box' ),
            __( 'CreativeWork is recommended to avoid conflicts with SEO plugin graphs.', 'abstract-box' ),
            ob_get_clean()
        );
    }

    /* ── Tab: Advanced ────────────────────────────────────────────── */

    public function render_advanced_tab() {
        $opts = Helpers::get_options();
        settings_fields( 'abstract_box_group' );

        ?>
        <p class="ab-section-label"><?php esc_html_e( 'Output', 'abstract-box' ); ?></p>
        <?php

        ob_start();
        ?>
        <input type="text"
               name="abstract_box_options[custom_css_class]"
               value="<?php echo esc_attr( $opts['custom_css_class'] ); ?>"
               class="regular-text"
               placeholder="<?php esc_attr_e( 'e.g. my-abstract', 'abstract-box' ); ?>" />
        <?php
        $this->render_card(
            __( 'Custom CSS Class', 'abstract-box' ),
            __( 'Add an extra CSS class to every abstract box for targeted theme styling.', 'abstract-box' ),
            ob_get_clean()
        );

        $this->render_card(
            __( 'Hover Effect', 'abstract-box' ),
            __( 'Enable a subtle lift-and-shadow on hover (desktop only).', 'abstract-box' ),
            $this->toggle( 'hover_effect', $opts['hover_effect'] )
        );

        /* ── CSS class reference ─────────────────────────────────── */
        ?>
        <p class="ab-section-label"><?php esc_html_e( 'CSS Class Reference', 'abstract-box' ); ?></p>
        <div class="ab-info-card">
            <table class="ab-usage-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Class', 'abstract-box' ); ?></th>
                        <th><?php esc_html_e( 'Selects', 'abstract-box' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>.abstract-box</code></td>
                        <td><?php esc_html_e( 'Outer container of every abstract box.', 'abstract-box' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>.abstract-box__title</code></td>
                        <td><?php esc_html_e( 'Title heading inside the box.', 'abstract-box' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>.abstract-box__subtitle</code></td>
                        <td><?php esc_html_e( 'Optional subtitle line.', 'abstract-box' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>.abstract-box__content</code></td>
                        <td><?php esc_html_e( 'Body text area inside the box.', 'abstract-box' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>.abstract-box--custom</code></td>
                        <td><?php esc_html_e( 'Present when Academic style is active. Target this for Custom-only rules.', 'abstract-box' ); ?></td>
                    </tr>
                </tbody>
            </table>
            <p class="ab-usage-note"><?php esc_html_e( 'Enable "Use Theme CSS" on the Appearance tab to disable plugin styles entirely — CSS custom properties (--ab-title-color etc.) are still injected so your theme can reference them.', 'abstract-box' ); ?></p>
        </div>
        <?php
    }

    /* ── Hidden preservation fields for inactive tabs ─────────────── */

    public function hidden_fields_for_inactive_tabs( $active_tab ) {
        $options  = Helpers::get_options();
        $defaults = Helpers::get_defaults();

        $tab_keys = array(
            'appearance' => array( 'style', 'use_theme_css', 'title_color', 'text_color', 'bg_color', 'bg_color_end', 'accent_color', 'bullet_color', 'stripe_color', 'border_radius', 'font_family' ),
            'schema'     => array( 'enable_schema', 'schema_type' ),
            'advanced'   => array( 'custom_css_class', 'hover_effect' ),
        );

        foreach ( $tab_keys as $tab => $keys ) {
            if ( $tab === $active_tab ) {
                continue;
            }
            foreach ( $keys as $key ) {
                $value = isset( $options[ $key ] ) ? $options[ $key ] : ( $defaults[ $key ] ?? '' );

                if ( is_bool( $value ) ) {
                    if ( $value ) {
                        echo '<input type="hidden" name="abstract_box_options[' . esc_attr( $key ) . ']" value="1" />';
                    }
                    continue;
                }

                echo '<input type="hidden" name="abstract_box_options[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '" />';
            }
        }
    }

    /* ── Live preview renderer ────────────────────────────────────── */

    public function render_preview() {
        $options = Helpers::get_options();

        $style_map = array(
            'modern'    => 'abstract-box--modern',
            'academic'  => 'abstract-box--academic',
            'minimal'   => 'abstract-box--minimal',
            'card'      => 'abstract-box--card',
            'ruled'     => 'abstract-box--ruled',
            'editorial' => 'abstract-box--editorial',
            'summary'   => 'abstract-box--summary',
            'bulletin'  => 'abstract-box--bulletin',
            'default'   => '',
            'custom'    => 'abstract-box--academic',
        );
        $modifier = $style_map[ $options['style'] ] ?? '';

        $classes  = 'abstract-box';
        if ( $modifier ) {
            $classes .= ' ' . $modifier;
        }

        $font_stack = Helpers::font_stack( $options['font_family'] );
        $radius     = absint( $options['border_radius'] );

        $css_vars  = '--ab-bg-color: '     . sanitize_hex_color( $options['bg_color'] )     . '; ';
        $css_vars .= '--ab-bg-color-end: ' . sanitize_hex_color( $options['bg_color_end'] ) . '; ';
        $css_vars .= '--ab-accent-color: ' . sanitize_hex_color( $options['accent_color'] ) . '; ';
        if ( ! empty( $options['bullet_color'] ) ) {
            $css_vars .= '--ab-bullet-color: ' . sanitize_hex_color( $options['bullet_color'] ) . '; ';
        }
        if ( ! empty( $options['stripe_color'] ) ) {
            $css_vars .= '--ab-stripe-color: ' . sanitize_hex_color( $options['stripe_color'] ) . '; ';
        }
        $css_vars .= '--ab-title-color: '  . sanitize_hex_color( $options['title_color'] )  . '; ';
        $css_vars .= '--ab-text-color: '   . sanitize_hex_color( $options['text_color'] )   . '; ';
        $css_vars .= '--ab-border-radius: ' . $radius . 'px; ';
        $css_vars .= '--ab-font-family: '   . $font_stack . ';';

        $html  = '<div id="ab-live-preview" class="' . esc_attr( $classes ) . '" style="' . esc_attr( $css_vars ) . '">';
        $html .= '<div class="abstract-box__title">' . esc_html__( 'Abstract', 'abstract-box' ) . '</div>';
        $html .= '<div class="abstract-box__subtitle">' . esc_html__( 'Section 1.1', 'abstract-box' ) . '</div>';
        $html .= '<div class="abstract-box__content">';
        $html .= '<p>' . esc_html__( 'This is a live preview of the abstract box. Changes to style, colours, font, and border radius are reflected here immediately without saving.', 'abstract-box' ) . '</p>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
