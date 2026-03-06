<?php
/**
 * Admin settings page view.
 *
 * Variables available from Settings::render_settings_page():
 *   $tabs       array  Slug => label map.
 *   $active_tab string Current tab slug.
 *
 * @package AbstractBox
 */
defined( 'ABSPATH' ) || exit;

/**
 * Allowed attributes for inline SVG icons used in tab navigation.
 * These icons are developer-defined strings, not user input.
 */
$ab_svg_kses = array(
    'svg'  => array(
        'viewbox'   => true,
        'fill'      => true,
        'xmlns'     => true,
        'width'     => true,
        'height'    => true,
        'aria-hidden' => true,
    ),
    'path' => array( 'd' => true, 'fill' => true, 'fill-rule' => true ),
    'g'    => array( 'fill' => true ),
);

$ab_tab_icons = array(
    'appearance' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>',
    'schema'     => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/></svg>',
    'advanced'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.63-.07.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>',
    'usage'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>',
);
?>
<div class="wrap abstract-box-settings">

    <?php /* WP requires an <h1> in .wrap for notices to anchor to. Hidden via CSS. */ ?>
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <!-- ── Header card ─────────────────────────────────────────────── -->
    <div class="ab-header">
        <div class="ab-header__left">
            <div class="ab-header__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M4 4h16v2H4V4zm0 4h16v2H4V8zm0 4h10v2H4v-2zm0 4h16v2H4v-2z"/>
                </svg>
            </div>
            <div>
                <div class="ab-header__title"><?php esc_html_e( 'Abstract Box', 'abstract-box' ); ?></div>
                <div class="ab-header__subtitle"><?php esc_html_e( 'Structured academic abstract blocks for WordPress', 'abstract-box' ); ?></div>
            </div>
        </div>
        <div class="ab-header__meta">
            <span class="ab-header__version">v<?php echo esc_html( ABSTRACT_BOX_VERSION ); ?></span>
        </div>
    </div><!-- .ab-header -->

    <!-- ── Tab navigation ──────────────────────────────────────────── -->
    <nav class="nav-tab-wrapper abstract-box-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Settings sections', 'abstract-box' ); ?>">
        <?php
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables passed from Settings::render_settings_page(), not globals.
        foreach ( $tabs as $slug => $label ) :
            $tab_url   = add_query_arg(
                array( 'page' => 'abstract-box', 'tab' => $slug ),
                admin_url( 'options-general.php' )
            );
            $is_active = ( $active_tab === $slug );
            $icon_html = isset( $ab_tab_icons[ $slug ] ) ? $ab_tab_icons[ $slug ] : '';
        ?>
            <a href="<?php echo esc_url( $tab_url ); ?>"
               class="nav-tab <?php echo $is_active ? 'nav-tab-active' : ''; ?>"
               role="tab"
               aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
                <?php echo wp_kses( $icon_html, $ab_svg_kses ); ?>
                <?php echo esc_html( $label ); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- ── Tab panel ───────────────────────────────────────────────── -->
    <div class="ab-tab-panel" role="tabpanel">

        <?php if ( 'usage' === $active_tab ) : ?>

            <?php include ABSTRACT_BOX_DIR . 'includes/views/usage.php'; ?>

        <?php else : ?>

            <form method="post" action="options.php" class="abstract-box-form">
                <?php
                switch ( $active_tab ) {
                    case 'schema':
                        $this->render_schema_tab();
                        break;
                    case 'advanced':
                        $this->render_advanced_tab();
                        break;
                    default:
                        $this->render_appearance_tab();
                        break;
                }

                $this->hidden_fields_for_inactive_tabs( $active_tab );
                ?>
                <div class="submit">
                    <input type="submit"
                           name="submit"
                           id="submit"
                           class="button button-primary"
                           value="<?php esc_attr_e( 'Save Settings', 'abstract-box' ); ?>" />
                </div>
            </form>

            <?php if ( ! in_array( $active_tab, array( 'schema', 'advanced' ), true ) ) : ?>
            <div class="abstract-box-preview-panel">
                <h3><?php esc_html_e( 'Preview', 'abstract-box' ); ?></h3>
                <div class="abstract-box-preview-container">
                    <?php
                    echo wp_kses(
                        $this->render_preview(),
                        array(
                            'div' => array( 'id' => array(), 'class' => array(), 'style' => array() ),
                            'p'   => array(),
                        )
                    );
                    ?>
                </div>
            </div>
            <?php endif; ?>

        <?php endif; ?>

    </div><!-- .ab-tab-panel -->

    <!-- ── Footer ──────────────────────────────────────────────────── -->
    <footer class="ab-footer">
        <p>
            <?php
            printf(
                /* translators: %s: linked author name */
                esc_html__( 'Developed by %s', 'abstract-box' ),
                '<a href="https://github.com/menj" target="_blank" rel="noopener noreferrer">MENJ</a>'
            );
            ?>
        </p>
    </footer>

</div><!-- .wrap.abstract-box-settings -->
