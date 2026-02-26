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

        /* Appearance */
        add_settings_section( 'abstract_box_appearance', '', '__return_false', 'abstract-box-appearance' );
        add_settings_field( 'ab_style', __( 'Box Style', 'abstract-box' ), [ $this, 'field_style' ], 'abstract-box-appearance', 'abstract_box_appearance' );
        add_settings_field( 'ab_use_theme_css', __( 'Use Theme CSS', 'abstract-box' ), [ $this, 'field_use_theme_css' ], 'abstract-box-appearance', 'abstract_box_appearance' );
        add_settings_field( 'ab_color_presets', __( 'Colour Presets', 'abstract-box' ), [ $this, 'field_color_presets' ], 'abstract-box-appearance', 'abstract_box_appearance' );
        add_settings_field( 'ab_title_color', __( 'Title Colour', 'abstract-box' ), [ $this, 'field_color' ], 'abstract-box-appearance', 'abstract_box_appearance', array( 'key' => 'title_color' ) );
        add_settings_field( 'ab_text_color', __( 'Text Colour', 'abstract-box' ), [ $this, 'field_color' ], 'abstract-box-appearance', 'abstract_box_appearance', array( 'key' => 'text_color' ) );
        add_settings_field( 'ab_bg_color', __( 'Background Start', 'abstract-box' ), [ $this, 'field_color' ], 'abstract-box-appearance', 'abstract_box_appearance', array( 'key' => 'bg_color' ) );
        add_settings_field( 'ab_bg_color_end', __( 'Background End', 'abstract-box' ), [ $this, 'field_color' ], 'abstract-box-appearance', 'abstract_box_appearance', array( 'key' => 'bg_color_end' ) );
        add_settings_field( 'ab_accent_color', __( 'Accent Colour', 'abstract-box' ), [ $this, 'field_color' ], 'abstract-box-appearance', 'abstract_box_appearance', array( 'key' => 'accent_color' ) );
        add_settings_field( 'ab_border_radius', __( 'Border Radius', 'abstract-box' ), [ $this, 'field_border_radius' ], 'abstract-box-appearance', 'abstract_box_appearance' );
        add_settings_field( 'ab_font_family', __( 'Font Family', 'abstract-box' ), [ $this, 'field_font_family' ], 'abstract-box-appearance', 'abstract_box_appearance' );

        /* Schema */
        add_settings_section( 'abstract_box_schema', '', '__return_false', 'abstract-box-schema' );
        add_settings_field( 'ab_enable_schema', __( 'Enable Schema Output', 'abstract-box' ), [ $this, 'field_enable_schema' ], 'abstract-box-schema', 'abstract_box_schema' );
        add_settings_field( 'ab_schema_type', __( 'Schema Type', 'abstract-box' ), [ $this, 'field_schema_type' ], 'abstract-box-schema', 'abstract_box_schema' );

        /* Advanced */
        add_settings_section( 'abstract_box_advanced', '', '__return_false', 'abstract-box-advanced' );
        add_settings_field( 'ab_custom_css_class', __( 'Custom CSS Class', 'abstract-box' ), [ $this, 'field_custom_css_class' ], 'abstract-box-advanced', 'abstract_box_advanced' );
        add_settings_field( 'ab_hover_effect', __( 'Hover Effect', 'abstract-box' ), [ $this, 'field_hover_effect' ], 'abstract-box-advanced', 'abstract_box_advanced' );
    }

    public function sanitize_options( $input ) {
        $defaults  = Helpers::get_defaults();
        $sanitized = array();

        $valid_styles               = array( 'default', 'custom' );
        $sanitized['style']         = in_array( $input['style'] ?? '', $valid_styles, true ) ? $input['style'] : $defaults['style'];

        $sanitized['use_theme_css'] = ! empty( $input['use_theme_css'] );

        $color_keys = array( 'title_color', 'text_color', 'bg_color', 'bg_color_end', 'accent_color' );
        foreach ( $color_keys as $ck ) {
            $sanitized[ $ck ] = sanitize_hex_color( $input[ $ck ] ?? '' ) ?: $defaults[ $ck ];
        }

        $sanitized['border_radius'] = absint( $input['border_radius'] ?? $defaults['border_radius'] );
        $sanitized['border_radius'] = min( 50, max( 0, $sanitized['border_radius'] ) );

        $valid_fonts                = array( 'sans-serif', 'serif', 'system' );
        $sanitized['font_family']   = in_array( $input['font_family'] ?? '', $valid_fonts, true ) ? $input['font_family'] : $defaults['font_family'];

        $sanitized['enable_schema'] = ! empty( $input['enable_schema'] );

        $valid_schema_types           = array( 'CreativeWork', 'ScholarlyArticle', 'Article' );
        $sanitized['schema_type']     = in_array( $input['schema_type'] ?? '', $valid_schema_types, true ) ? $input['schema_type'] : $defaults['schema_type'];

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

        include ABSTRACT_BOX_DIR . 'views/admin-settings.php';
    }

    public function hidden_fields_for_inactive_tabs( $active_tab ) {
        $options  = Helpers::get_options();
        $defaults = Helpers::get_defaults();

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

    public function render_preview() {
        // Values have already been validated by sanitize_options() at save time.
        // Re-running sanitize_hex_color() here would be redundant.
        $options = Helpers::get_options();

        $bg_color     = $options['bg_color'];
        $bg_color_end = $options['bg_color_end'];
        $accent_color = $options['accent_color'];
        $title_color  = $options['title_color'];
        $text_color   = $options['text_color'];
        $radius       = absint( $options['border_radius'] );
        $font_stack   = Helpers::font_stack( $options['font_family'] );

        $container_style  = 'font-family: ' . $font_stack . ';';
        $container_style .= 'background-image: linear-gradient(to right top, ' . $bg_color . ', ' . $bg_color_end . ');';
        $container_style .= 'border-radius: ' . $radius . 'px;';
        $container_style .= 'border-left: 3px solid ' . $accent_color . ';';
        $container_style .= 'padding: 16px 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.08);';

        $html  = '<div style="' . esc_attr( $container_style ) . '">';
        $html .= '<h2 style="' . esc_attr( 'color: ' . $title_color . '; font-size:18px; margin:0 0 8px; font-weight:600;' ) . '">';
        $html .= esc_html__( 'Abstract', 'abstract-box' );
        $html .= '</h2>';
        $html .= '<p style="' . esc_attr( 'color: ' . $text_color . '; font-size:14px; line-height:1.6; margin:0; text-align:justify;' ) . '">';
        $html .= esc_html__( 'This is a preview of how your abstract box will appear on posts. The colours, font family, and border radius shown here reflect your current settings.', 'abstract-box' );
        $html .= '</p></div>';

        return $html;
    }

    public function field_style() {
        $value = Helpers::get_option( 'style' );
        ?>
        <select name="abstract_box_options[style]" id="ab-style">
            <option value="default" <?php selected( $value, 'default' ); ?>><?php esc_html_e( 'Default', 'abstract-box' ); ?></option>
            <option value="custom" <?php selected( $value, 'custom' ); ?>><?php esc_html_e( 'Custom', 'abstract-box' ); ?></option>
        </select>
        <p class="description"><?php esc_html_e( 'Choose the base stylesheet for the abstract box.', 'abstract-box' ); ?></p>
        <?php
    }

    public function field_use_theme_css() {
        $value = Helpers::get_option( 'use_theme_css' );
        ?>
        <label>
            <input type="checkbox" name="abstract_box_options[use_theme_css]" value="1" <?php checked( $value ); ?> />
            <?php esc_html_e( 'Disable plugin styles and rely on your theme to style the abstract box.', 'abstract-box' ); ?>
        </label>
        <?php
    }

    public function field_color_presets() {
        ?>
        <div class="abstract-box-presets">
            <button type="button" class="button abstract-box-preset-btn" data-preset="dark"><?php esc_html_e('Dark Mode', 'abstract-box'); ?></button>
            <button type="button" class="button abstract-box-preset-btn" data-preset="sepia"><?php esc_html_e('Academic Sepia', 'abstract-box'); ?></button>
            <button type="button" class="button abstract-box-preset-btn" data-preset="ocean"><?php esc_html_e('Ocean Blue', 'abstract-box'); ?></button>
            <button type="button" class="button abstract-box-preset-btn" data-preset="default"><?php esc_html_e('Reset Default', 'abstract-box'); ?></button>
        </div>
        <p class="description"><?php esc_html_e( 'Instantly apply a pre-configured colour palette.', 'abstract-box' ); ?></p>
        <?php
    }

    public function field_color( $args ) {
        $key   = $args['key'];
        $value = Helpers::get_option( $key );
        ?>
        <input type="text"
               name="abstract_box_options[<?php echo esc_attr( $key ); ?>]"
               value="<?php echo esc_attr( $value ); ?>"
               class="abstract-box-color-picker"
               data-default-color="<?php echo esc_attr( Helpers::get_defaults()[ $key ] ); ?>" />
        <?php
    }

    public function field_border_radius() {
        $value = Helpers::get_option( 'border_radius' );
        ?>
        <input type="number" name="abstract_box_options[border_radius]" value="<?php echo esc_attr( $value ); ?>" min="0" max="50" step="1" class="small-text" />
        <span class="description">px</span>
        <?php
    }

    public function field_font_family() {
        $value = Helpers::get_option( 'font_family' );
        ?>
        <select name="abstract_box_options[font_family]" id="ab-font-family">
            <option value="sans-serif" <?php selected( $value, 'sans-serif' ); ?>><?php esc_html_e( 'Sans-Serif (Modernist)', 'abstract-box' ); ?></option>
            <option value="serif" <?php selected( $value, 'serif' ); ?>><?php esc_html_e( 'Serif (Traditional)', 'abstract-box' ); ?></option>
            <option value="system" <?php selected( $value, 'system' ); ?>><?php esc_html_e( 'System Default', 'abstract-box' ); ?></option>
        </select>
        <?php
    }

    public function field_enable_schema() {
        $value = Helpers::get_option( 'enable_schema' );
        ?>
        <label>
            <input type="checkbox" name="abstract_box_options[enable_schema]" value="1" <?php checked( $value ); ?> />
            <?php esc_html_e( 'Output schema.org JSON-LD structured data in the page head.', 'abstract-box' ); ?>
        </label>
        <?php
    }

    public function field_schema_type() {
        $value = Helpers::get_option( 'schema_type' );
        ?>
        <select name="abstract_box_options[schema_type]" id="ab-schema-type">
            <option value="CreativeWork" <?php selected( $value, 'CreativeWork' ); ?>>CreativeWork</option>
            <option value="ScholarlyArticle" <?php selected( $value, 'ScholarlyArticle' ); ?>>ScholarlyArticle</option>
            <option value="Article" <?php selected( $value, 'Article' ); ?>>Article</option>
        </select>
        <p class="description"><?php esc_html_e( 'CreativeWork is recommended to avoid conflicts with SEO plugin graphs.', 'abstract-box' ); ?></p>
        <?php
    }

    public function field_custom_css_class() {
        $value = Helpers::get_option( 'custom_css_class' );
        ?>
        <input type="text" name="abstract_box_options[custom_css_class]" value="<?php echo esc_attr( $value ); ?>" class="regular-text" placeholder="e.g. my-abstract" />
        <p class="description"><?php esc_html_e( 'Add a custom CSS class to every abstract box for targeted styling.', 'abstract-box' ); ?></p>
        <?php
    }

    public function field_hover_effect() {
        $value = Helpers::get_option( 'hover_effect' );
        ?>
        <label>
            <input type="checkbox" name="abstract_box_options[hover_effect]" value="1" <?php checked( $value ); ?> />
            <?php esc_html_e( 'Enable subtle lift-and-shadow on hover (desktop only).', 'abstract-box' ); ?>
        </label>
        <?php
    }
}
