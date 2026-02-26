/**
 * Abstract Box — Admin Settings Script
 *
 * Initialises WordPress colour pickers on the settings page.
 *
 * @package AbstractBox
 * @since   2.0.0
 */

(function ($) {
    'use strict';

    function getRGB(hex) {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    function luminance(r, g, b) {
        var a = [r, g, b].map(function (v) {
            v /= 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
    }

    function checkContrast() {
        var bg = $('input[name="abstract_box_options[bg_color]"]').val();
        var text = $('input[name="abstract_box_options[text_color]"]').val();
        if (bg && text) {
            var rgb1 = getRGB(bg), rgb2 = getRGB(text);
            if (!rgb1 || !rgb2) return;
            var lum1 = luminance(rgb1.r, rgb1.g, rgb1.b);
            var lum2 = luminance(rgb2.r, rgb2.g, rgb2.b);
            var contrast = (Math.max(lum1, lum2) + 0.05) / (Math.min(lum1, lum2) + 0.05);

            var $warning = $('#abstract-box-contrast-warning');
            if (contrast < 4.5 && contrast > 0) {
                if ($warning.length === 0) {
                    $('.abstract-box-preview-container').after('<p id="abstract-box-contrast-warning" style="color: #d63638; margin-top: 12px; font-weight: 500; font-size: 13px;">⚠️ Warning: The contrast ratio between your Background and Text colours is low (' + contrast.toFixed(1) + ':1). This may be hard for some users to read.</p>');
                } else {
                    $warning.text('⚠️ Warning: The contrast ratio between your Background and Text colours is low (' + contrast.toFixed(1) + ':1). This may be hard for some users to read.').show();
                }
            } else {
                $warning.hide();
            }
        }
    }

    $(document).ready(function () {
        // Initialise all colour picker inputs with a change event listener.
        $('.abstract-box-color-picker').wpColorPicker({
            change: function (event, ui) {
                setTimeout(checkContrast, 100);
            },
            clear: function () {
                setTimeout(checkContrast, 100);
            }
        });

        // Handle preset buttons
        $('.abstract-box-preset-btn').on('click', function () {
            var preset = $(this).data('preset');
            var colors = {};

            switch (preset) {
                case 'dark':
                    colors = {
                        'bg_color': '#1e293b',
                        'bg_color_end': '#334155',
                        'text_color': '#cbd5e1',
                        'title_color': '#f8fafc',
                        'accent_color': '#3b82f6'
                    };
                    break;
                case 'sepia':
                    colors = {
                        'bg_color': '#fdf6e3',
                        'bg_color_end': '#eee8d5',
                        'text_color': '#657b83',
                        'title_color': '#586e75',
                        'accent_color': '#b58900'
                    };
                    break;
                case 'ocean':
                    colors = {
                        'bg_color': '#f0f9ff',
                        'bg_color_end': '#e0f2fe',
                        'text_color': '#0369a1',
                        'title_color': '#075985',
                        'accent_color': '#0284c7'
                    };
                    break;
                case 'default':
                default:
                    colors = {
                        'bg_color': '#f8fafc',
                        'bg_color_end': '#ffffff',
                        'text_color': '#334155',
                        'title_color': '#1e293b',
                        'accent_color': '#3b82f6'
                    };
                    break;
            }

            $.each(colors, function (key, hex) {
                var $input = $('input[name="abstract_box_options[' + key + ']"]');
                $input.iris('color', hex);
            });

            setTimeout(checkContrast, 100);
        });

        // Run on initial load
        checkContrast();
    });
})(jQuery);
