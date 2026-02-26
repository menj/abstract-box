/**
 * Abstract Box — Customizer Live Preview
 *
 * Updates CSS custom properties in real time as the user
 * adjusts colour and style settings in the Customizer.
 *
 * @package AbstractBox
 * @since   2.0.0
 */

(function ($) {
    'use strict';

    var propertyMap = {
        'abstract_box_options[title_color]': '--ab-title-color',
        'abstract_box_options[text_color]': '--ab-text-color',
        'abstract_box_options[bg_color]': '--ab-bg-color',
        'abstract_box_options[bg_color_end]': '--ab-bg-color-end',
        'abstract_box_options[accent_color]': '--ab-accent-color',
    };

    $.each(propertyMap, function (settingKey, cssProperty) {
        wp.customize(settingKey, function (value) {
            value.bind(function (newVal) {
                document.documentElement.style.setProperty(cssProperty, newVal);
            });
        });
    });

    // Border radius.
    wp.customize('abstract_box_options[border_radius]', function (value) {
        value.bind(function (newVal) {
            var parsedVal = parseInt(newVal, 10) || 0;
            document.documentElement.style.setProperty('--ab-border-radius', parsedVal + 'px');
        });
    });

})(jQuery);
