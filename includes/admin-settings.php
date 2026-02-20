<?php
/**
 * Admin settings page with tabbed interface.
 *
 * Tabs: Appearance · Schema · Advanced
 *
 * @package AbstractBox
 */

defined( 'ABSPATH' ) || exit;

/* ── Register page & settings ──────────────────────────────────────── */

/**
 * Add the settings page under Settings menu.
 */
function abstract_box_add_settings_page() {
    add_options_page(
        __( 'Abstract Box Settings', 'abstract-box' ),
        __( 'Abstract Box', 'abstract-box' ),
        'manage_options',
        'abstract-box',
        'abstract_box_render_settings_page'
    );
}

add_action( 'admin_menu', 'abstract_box_add_settings_page' );

/**
 * Register settings and sections/fields.
 */
function abstract_box_register_settings() {
    register_setting( 'abstract_box_group', 'abstract_box_options', array(
        'sanitize_callback' => 'abstract_box_sanitize_options',
        'default'           => abstract_box_get_defaults(),
    ) );

    /* ── Appearance tab ─────────────────────────────────────────────── */

    add_settings_section(
        'abstract_box_appearance',
        '',
        '__return_false',
        'abstract-box-appearance'
    );

    add_settings_field( 'ab_style', __( 'Box Style', 'abstract-box' ), 'abstract_box_field_style', 'abstract-box-appearance', 'abstract_box_appearance' );
    add_settings_field( 'ab_use_theme_css', __( 'Use Theme CSS', 'abstract-box' ), 'abstract_box_field_use_theme_css', 'abstract-box-appearance', 'abstract_box_appearance' );
    add_settings_field( 'ab_title_color', __( 'Title Colour', 'abstract-box' ), 'abstract_box_field_color', 'abstract-box-appearance', 'abstract_box_appearance', array( 'key' => 'title_color' ) );
    add_settings_field( 'ab_text_color', __( 'Text Colour', 'abstract-box' ), 'abstract_box_field_color', 'abstract-box-appearance', 'abstract_box_appearance', array( 'key' => 'text_color' ) );
    add_settings_field( 'ab_bg_color', __( 'Background Start', 'abstract-box' ), 'abstract_box_field_color', 'abstract-box-appearance', 'abstract_box_appearance', array( 'key' => 'bg_color' ) );
    add_settings_field( 'ab_bg_color_end', __( 'Background End', 'abstract-box' ), 'abstract_box_field_color', 'abstract-box-appearance', 'abstract_box_appearance', array( 'key' => 'bg_color_end' ) );
    add_settings_field( 'ab_accent_color', __( 'Accent Colour', 'abstract-box' ), 'abstract_box_field_color', 'abstract-box-appearance', 'abstract_box_appearance', array( 'key' => 'accent_color' ) );
    add_settings_field( 'ab_border_radius', __( 'Border Radius', 'abstract-box' ), 'abstract_box_field_border_radius', 'abstract-box-appearance', 'abstract_box_appearance' );
    add_settings_field( 'ab_font_family', __( 'Font Family', 'abstract-box' ), 'abstract_box_field_font_family', 'abstract-box-appearance', 'abstract_box_appearance' );

    /* ── Schema tab ─────────────────────────────────────────────────── */

    add_settings_section(
        'abstract_box_schema',
        '',
        '__return_false',
        'abstract-box-schema'
    );

    add_settings_field( 'ab_enable_schema', __( 'Enable Schema Output', 'abstract-box' ), 'abstract_box_field_enable_schema', 'abstract-box-schema', 'abstract_box_schema' );
    add_settings_field( 'ab_schema_type', __( 'Schema Type', 'abstract-box' ), 'abstract_box_field_schema_type', 'abstract-box-schema', 'abstract_box_schema' );

    /* ── Advanced tab ───────────────────────────────────────────────── */

    add_settings_section(
        'abstract_box_advanced',
        '',
        '__return_false',
        'abstract-box-advanced'
    );

    add_settings_field( 'ab_custom_css_class', __( 'Custom CSS Class', 'abstract-box' ), 'abstract_box_field_custom_css_class', 'abstract-box-advanced', 'abstract_box_advanced' );
    add_settings_field( 'ab_hover_effect', __( 'Hover Effect', 'abstract-box' ), 'abstract_box_field_hover_effect', 'abstract-box-advanced', 'abstract_box_advanced' );
}

add_action( 'admin_init', 'abstract_box_register_settings' );

/* ── Settings page renderer ────────────────────────────────────────── */

/**
 * Render the tabbed settings page.
 */
function abstract_box_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'appearance';
    $tabs = array(
        'appearance' => __( 'Appearance', 'abstract-box' ),
        'schema'     => __( 'Schema', 'abstract-box' ),
        'advanced'   => __( 'Advanced', 'abstract-box' ),
    );

    if ( ! array_key_exists( $active_tab, $tabs ) ) {
        $active_tab = 'appearance';
    }
    ?>
    <div class="wrap abstract-box-settings">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

        <nav class="nav-tab-wrapper abstract-box-tabs">
            <?php foreach ( $tabs as $slug => $label ) : ?>
                <a href="<?php echo esc_url( add_query_arg( 'tab', $slug, admin_url( 'options-general.php?page=abstract-box' ) ) ); ?>"
                   class="nav-tab <?php echo ( $active_tab === $slug ) ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html( $label ); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <form method="post" action="options.php" class="abstract-box-form">
            <?php
            settings_fields( 'abstract_box_group' );

            // Render only the active tab's section.
            switch ( $active_tab ) {
                case 'schema':
                    do_settings_sections( 'abstract-box-schema' );
                    break;
                case 'advanced':
                    do_settings_sections( 'abstract-box-advanced' );
                    break;
                default:
                    do_settings_sections( 'abstract-box-appearance' );
                    break;
            }

            // Output hidden fields for inactive tabs so their values are preserved.
            abstract_box_hidden_fields_for_inactive_tabs( $active_tab );

            submit_button();
            ?>
        </form>

        <div class="abstract-box-preview-panel">
            <h3><?php esc_html_e( 'Preview', 'abstract-box' ); ?></h3>
            <div class="abstract-box-preview-container">
                <?php echo abstract_box_render_preview(); ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Output hidden inputs for tabs not currently displayed, so their
 * values are not lost when the form is submitted.
 *
 * @param string $active_tab Currently active tab slug.
 */
function abstract_box_hidden_fields_for_inactive_tabs( $active_tab ) {
    $options  = abstract_box_get_options();
    $defaults = abstract_box_get_defaults();

    // Map keys to their tabs.
    $tab_keys = array(
        'appearance' => array( 'style', 'use_theme_css', 'title_color', 'text_color', 'bg_color', 'bg_color_end', 'accent_color', 'border_radius', 'font_family' ),
        'schema'     => array( 'enable_schema', 'schema_type' ),
        'advanced'   => array( 'custom_css_class', 'hover_effect' ),
    );

    foreach ( $tab_keys as $tab => $keys ) {
        if ( $tab === $active_tab ) {
            continue;
        }
        foreach ( $keys as $key ) {
            $value = isset( $options[ $key ] ) ? $options[ $key ] : ( $defaults[ $key ] ?? '' );

            // Booleans: only output hidden field with value "1" when true.
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

/**
 * Render a static HTML preview of the abstract box.
 *
 * @return string HTML preview.
 */
function abstract_box_render_preview() {
    $options    = abstract_box_get_options();
    $font_stack = abstract_box_font_stack( $options['font_family'] );

    $style  = "font-family: {$font_stack};";
    $style .= "background-image: linear-gradient(to right top, {$options['bg_color']}, {$options['bg_color_end']});";
    $style .= "border-radius: {$options['border_radius']}px;";
    $style .= "border-left: 3px solid {$options['accent_color']};";
    $style .= "padding: 16px 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.08);";

    $html  = '<div style="' . esc_attr( $style ) . '">';
    $html .= '<h2 style="color:' . esc_attr( $options['title_color'] ) . '; font-size:18px; margin:0 0 8px; font-weight:600;">' . esc_html__( 'Abstract', 'abstract-box' ) . '</h2>';
    $html .= '<p style="color:' . esc_attr( $options['text_color'] ) . '; font-size:14px; line-height:1.6; margin:0; text-align:justify;">';
    $html .= esc_html__( 'This is a preview of how your abstract box will appear on posts. The colours, font family, and border radius shown here reflect your current settings.', 'abstract-box' );
    $html .= '</p></div>';

    return $html;
}

/* ── Field renderers ───────────────────────────────────────────────── */

function abstract_box_field_style() {
    $value = abstract_box_get_option( 'style' );
    ?>
    <select name="abstract_box_options[style]" id="ab-style">
        <option value="default" <?php selected( $value, 'default' ); ?>><?php esc_html_e( 'Default', 'abstract-box' ); ?></option>
        <option value="custom" <?php selected( $value, 'custom' ); ?>><?php esc_html_e( 'Custom', 'abstract-box' ); ?></option>
    </select>
    <p class="description"><?php esc_html_e( 'Choose the base stylesheet for the abstract box.', 'abstract-box' ); ?></p>
    <?php
}

function abstract_box_field_use_theme_css() {
    $value = abstract_box_get_option( 'use_theme_css' );
    ?>
    <label>
        <input type="checkbox" name="abstract_box_options[use_theme_css]" value="1" <?php checked( $value ); ?> />
        <?php esc_html_e( 'Disable plugin styles and rely on your theme to style the abstract box.', 'abstract-box' ); ?>
    </label>
    <?php
}

function abstract_box_field_color( $args ) {
    $key   = $args['key'];
    $value = abstract_box_get_option( $key );
    ?>
    <input type="text"
           name="abstract_box_options[<?php echo esc_attr( $key ); ?>]"
           value="<?php echo esc_attr( $value ); ?>"
           class="abstract-box-color-picker"
           data-default-color="<?php echo esc_attr( abstract_box_get_defaults()[ $key ] ); ?>" />
    <?php
}

function abstract_box_field_border_radius() {
    $value = abstract_box_get_option( 'border_radius' );
    ?>
    <input type="number" name="abstract_box_options[border_radius]" value="<?php echo esc_attr( $value ); ?>" min="0" max="50" step="1" class="small-text" />
    <span class="description">px</span>
    <?php
}

function abstract_box_field_font_family() {
    $value = abstract_box_get_option( 'font_family' );
    ?>
    <select name="abstract_box_options[font_family]" id="ab-font-family">
        <option value="sans-serif" <?php selected( $value, 'sans-serif' ); ?>><?php esc_html_e( 'Sans-Serif (Modernist)', 'abstract-box' ); ?></option>
        <option value="serif" <?php selected( $value, 'serif' ); ?>><?php esc_html_e( 'Serif (Traditional)', 'abstract-box' ); ?></option>
        <option value="system" <?php selected( $value, 'system' ); ?>><?php esc_html_e( 'System Default', 'abstract-box' ); ?></option>
    </select>
    <?php
}

function abstract_box_field_enable_schema() {
    $value = abstract_box_get_option( 'enable_schema' );
    ?>
    <label>
        <input type="checkbox" name="abstract_box_options[enable_schema]" value="1" <?php checked( $value ); ?> />
        <?php esc_html_e( 'Output schema.org JSON-LD structured data in the page head.', 'abstract-box' ); ?>
    </label>
    <?php
}

function abstract_box_field_schema_type() {
    $value = abstract_box_get_option( 'schema_type' );
    ?>
    <select name="abstract_box_options[schema_type]" id="ab-schema-type">
        <option value="CreativeWork" <?php selected( $value, 'CreativeWork' ); ?>>CreativeWork</option>
        <option value="ScholarlyArticle" <?php selected( $value, 'ScholarlyArticle' ); ?>>ScholarlyArticle</option>
        <option value="Article" <?php selected( $value, 'Article' ); ?>>Article</option>
    </select>
    <p class="description"><?php esc_html_e( 'CreativeWork is recommended to avoid conflicts with SEO plugin graphs.', 'abstract-box' ); ?></p>
    <?php
}

function abstract_box_field_custom_css_class() {
    $value = abstract_box_get_option( 'custom_css_class' );
    ?>
    <input type="text" name="abstract_box_options[custom_css_class]" value="<?php echo esc_attr( $value ); ?>" class="regular-text" placeholder="e.g. my-abstract" />
    <p class="description"><?php esc_html_e( 'Add a custom CSS class to every abstract box for targeted styling.', 'abstract-box' ); ?></p>
    <?php
}

function abstract_box_field_hover_effect() {
    $value = abstract_box_get_option( 'hover_effect' );
    ?>
    <label>
        <input type="checkbox" name="abstract_box_options[hover_effect]" value="1" <?php checked( $value ); ?> />
        <?php esc_html_e( 'Enable subtle lift-and-shadow on hover (desktop only).', 'abstract-box' ); ?>
    </label>
    <?php
}
