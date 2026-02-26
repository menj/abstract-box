<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap abstract-box-settings">

    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <!-- ── Plugin header card ──────────────────────────────────────── -->
    <div class="ab-header">
        <div class="ab-header__left">
            <div class="ab-header__icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
    </div>

    <!-- ── Tabs ────────────────────────────────────────────────────── -->
    <nav class="nav-tab-wrapper abstract-box-tabs">
        <?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables passed from Settings::render_settings_page(), not globals.
        foreach ( $tabs as $slug => $label ) :
            $tab_url = add_query_arg(
                array(
                    'page' => 'abstract-box',
                    'tab'  => $slug,
                ),
                admin_url( 'options-general.php' )
            );
            $icons = array(
                'appearance' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>',
                'schema'     => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/></svg>',
                'advanced'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.63-.07.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>',
                'usage'      => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>',
            );
            $icon = isset( $icons[ $slug ] ) ? $icons[ $slug ] : '';
        ?>
            <a href="<?php echo esc_url( $tab_url ); ?>"
               class="nav-tab <?php echo esc_attr( ( $active_tab === $slug ) ? 'nav-tab-active' : '' ); ?>">
                <?php echo $icon; // SVG — no user input, safe. ?>
                <?php echo esc_html( $label ); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- ── Tab panel ───────────────────────────────────────────────── -->
    <div class="ab-tab-panel">

        <?php if ( 'usage' === $active_tab ) : ?>

            <?php include ABSTRACT_BOX_DIR . 'views/usage.php'; ?>

        <?php else : ?>

            <form method="post" action="options.php" class="abstract-box-form">
                <?php
                settings_fields( 'abstract_box_group' );

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

                $this->hidden_fields_for_inactive_tabs( $active_tab );

                submit_button( __( 'Save Settings', 'abstract-box' ) );
                ?>
            </form>

            <div class="abstract-box-preview-panel">
                <h3><?php esc_html_e( 'Preview', 'abstract-box' ); ?></h3>
                <div class="abstract-box-preview-container">
                    <?php
                    echo wp_kses(
                        $this->render_preview(),
                        array(
                            'div' => array( 'style' => array() ),
                            'h2'  => array( 'style' => array() ),
                            'p'   => array( 'style' => array() ),
                        )
                    );
                    ?>
                </div>
            </div>

        <?php endif; ?>

    </div><!-- .ab-tab-panel -->

</div><!-- .wrap -->
