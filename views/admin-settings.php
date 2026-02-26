<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap abstract-box-settings">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <nav class="nav-tab-wrapper abstract-box-tabs">
        <?php foreach ( $tabs as $slug => $label ) :
            $tab_url = add_query_arg(
                array(
                    'page' => 'abstract-box',
                    'tab'  => $slug,
                ),
                admin_url( 'options-general.php' )
            );
        ?>
            <a href="<?php echo esc_url( $tab_url ); ?>"
               class="nav-tab <?php echo esc_attr( ( $active_tab === $slug ) ? 'nav-tab-active' : '' ); ?>">
                <?php echo esc_html( $label ); ?>
            </a>
        <?php endforeach; ?>
    </nav>

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

            submit_button();
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
</div>
