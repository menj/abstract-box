<?php
/**
 * Shortcode view template.
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="<?php echo esc_attr( $class_attr ); ?>" <?php if ( ! empty( $inline_style ) ) { echo 'style="' . esc_attr( $inline_style ) . '"'; } ?>>
    <<?php echo esc_html( $title_tag ); ?> class="abstract-box__title"><?php echo esc_html( $atts['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
    <?php if ( ! empty( $atts['subtitle'] ) ) : ?>
        <div class="abstract-box__subtitle"><?php echo esc_html( $atts['subtitle'] ); ?></div>
    <?php endif; ?>
    <div class="abstract-box__content">
        <?php echo do_shortcode( $content ); // Removed wp_kses_post to allow embeds ?>
    </div>
</div>
