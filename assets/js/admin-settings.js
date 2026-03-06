/**
 * Abstract Box — Admin Settings Script
 *
 * All event binding is scoped to .abstract-box-settings.
 * No global namespace pollution; no globals written outside the IIFE.
 *
 * @package AbstractBox
 * @since   2.2.3
 */

(function ($) {
    'use strict';

    var WRAPPER  = '.abstract-box-settings';
    var PREVIEW  = '#ab-live-preview';

    var STYLE_CLASSES = [
        'abstract-box--modern',
        'abstract-box--academic',
        'abstract-box--minimal',
        'abstract-box--card',
        'abstract-box--ruled',
        'abstract-box--editorial',
        'abstract-box--summary',
    ];

    var FONT_STACKS = (window.abstractBoxAdmin && window.abstractBoxAdmin.fontStacks)
        ? window.abstractBoxAdmin.fontStacks
        : {
            'sans-serif': "'Helvetica Neue', Helvetica, Arial, sans-serif",
            'serif':      "Georgia, 'Times New Roman', Times, serif",
            'system':     "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
        };

    /* ── Contrast checker ─────────────────────────────────────────── */

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

    function checkContrast($wrapper) {
        var bg   = $wrapper.find('input[name="abstract_box_options[bg_color]"]').val();
        var text = $wrapper.find('input[name="abstract_box_options[text_color]"]').val();
        if (!bg || !text) { return; }

        var rgb1 = getRGB(bg), rgb2 = getRGB(text);
        if (!rgb1 || !rgb2) { return; }

        var lum1     = luminance(rgb1.r, rgb1.g, rgb1.b);
        var lum2     = luminance(rgb2.r, rgb2.g, rgb2.b);
        var contrast = (Math.max(lum1, lum2) + 0.05) / (Math.min(lum1, lum2) + 0.05);
        var $warning = $wrapper.find('#abstract-box-contrast-warning');

        if (contrast < 4.5 && contrast > 0) {
            var msg = '⚠️ Low contrast ratio (' + contrast.toFixed(1) + ':1) between Background and Text colours.';
            if ($warning.length === 0) {
                $wrapper.find('.abstract-box-preview-container').after(
                    '<p id="abstract-box-contrast-warning" style="color:#b91c1c;margin-top:10px;font-size:13px;font-weight:500;">' + msg + '</p>'
                );
            } else {
                $warning.text(msg).show();
            }
        } else {
            $warning.hide();
        }
    }

    /* ── Live preview updater ─────────────────────────────────────── */

    function updatePreview($wrapper) {
        var $preview = $wrapper.find(PREVIEW);
        if ($preview.length === 0) { return; }

        /* Style — swap modifier class */
        var style    = $wrapper.find('input[name="abstract_box_options[style]"]:checked').val() || 'modern';
        var modifier = (style !== 'modern' && style !== 'default') ? 'abstract-box--' + style : '';

        $preview.removeClass(STYLE_CLASSES.join(' '));
        if (modifier) {
            $preview.addClass(modifier);
        }

        /* Colours — set CSS custom properties directly on the element */
        var bgColor     = $wrapper.find('input[name="abstract_box_options[bg_color]"]').val()     || '';
        var bgColorEnd  = $wrapper.find('input[name="abstract_box_options[bg_color_end]"]').val() || '';
        var accentColor = $wrapper.find('input[name="abstract_box_options[accent_color]"]').val() || '';
        var titleColor  = $wrapper.find('input[name="abstract_box_options[title_color]"]').val()  || '';
        var textColor   = $wrapper.find('input[name="abstract_box_options[text_color]"]').val()   || '';

        if (bgColor)     { $preview[0].style.setProperty('--ab-bg-color',     bgColor);     }
        if (bgColorEnd)  { $preview[0].style.setProperty('--ab-bg-color-end', bgColorEnd);  }
        if (accentColor) { $preview[0].style.setProperty('--ab-accent-color', accentColor); }

        var bulletColor = $wrapper.find('input[name="abstract_box_options[bullet_color]"]').val() || '';
        var stripeColor = $wrapper.find('input[name="abstract_box_options[stripe_color]"]').val() || '';

        if (bulletColor) {
            $preview[0].style.setProperty('--ab-bullet-color', bulletColor);
        } else {
            $preview[0].style.removeProperty('--ab-bullet-color');
        }
        if (stripeColor) {
            $preview[0].style.setProperty('--ab-stripe-color', stripeColor);
        } else {
            $preview[0].style.removeProperty('--ab-stripe-color');
        }
        if (titleColor)  { $preview[0].style.setProperty('--ab-title-color',  titleColor);  }
        if (textColor)   { $preview[0].style.setProperty('--ab-text-color',   textColor);   }

        /* Border radius */
        var radius = parseInt( $wrapper.find('input[name="abstract_box_options[border_radius]"]').val(), 10 );
        if (!isNaN(radius)) {
            $preview[0].style.setProperty('--ab-border-radius', radius + 'px');
        }

        /* Font family */
        var fontKey   = $wrapper.find('select[name="abstract_box_options[font_family]"]').val() || 'sans-serif';
        var fontStack = FONT_STACKS[fontKey] || FONT_STACKS['sans-serif'];
        $preview[0].style.setProperty('--ab-font-family', fontStack);
    }

    /* ── Colour presets ───────────────────────────────────────────── */

    var PRESETS = {
        'default': {
            bg_color:     '#f8fafc',
            bg_color_end: '#ffffff',
            text_color:   '#334155',
            title_color:  '#1e293b',
            accent_color: '#3b82f6',
            style:        'modern'
        },
        dark: {
            bg_color:     '#1e293b',
            bg_color_end: '#334155',
            text_color:   '#cbd5e1',
            title_color:  '#f8fafc',
            accent_color: '#3b82f6',
            style:        'card'
        },
        sepia: {
            bg_color:     '#fdf6e3',
            bg_color_end: '#eee8d5',
            text_color:   '#657b83',
            title_color:  '#586e75',
            accent_color: '#b58900',
            style:        'academic'
        },
        ocean: {
            bg_color:     '#f0f9ff',
            bg_color_end: '#e0f2fe',
            text_color:   '#0369a1',
            title_color:  '#075985',
            accent_color: '#0284c7',
            style:        'modern'
        },
        forest: {
            bg_color:     '#f0fdf4',
            bg_color_end: '#dcfce7',
            text_color:   '#166534',
            title_color:  '#14532d',
            accent_color: '#16a34a',
            style:        'ruled'
        },
        rose: {
            bg_color:     '#fff1f2',
            bg_color_end: '#ffe4e6',
            text_color:   '#9f1239',
            title_color:  '#881337',
            accent_color: '#e11d48',
            style:        'minimal'
        },
        midnight: {
            bg_color:     '#1e1b4b',
            bg_color_end: '#312e81',
            text_color:   '#c7d2fe',
            title_color:  '#e0e7ff',
            accent_color: '#818cf8',
            style:        'card'
        },
        sand: {
            bg_color:     '#fefce8',
            bg_color_end: '#fef9c3',
            text_color:   '#78350f',
            title_color:  '#713f12',
            accent_color: '#d97706',
            style:        'academic'
        }
    };

    /* ── Init ─────────────────────────────────────────────────────── */

    $(document).ready(function () {
        var $wrapper = $(WRAPPER);
        if ($wrapper.length === 0) { return; }

        /* Colour pickers */
        $wrapper.find('.abstract-box-color-picker').wpColorPicker({
            change: function () {
                setTimeout(function () {
                    checkContrast($wrapper);
                    updatePreview($wrapper);
                }, 50);
            },
            clear: function () {
                setTimeout(function () {
                    checkContrast($wrapper);
                    updatePreview($wrapper);
                }, 50);
            }
        });

        /* Style radio buttons */
        $wrapper.on('change', 'input[name="abstract_box_options[style]"]', function () {
            updatePreview($wrapper);
        });

        /* Font family select */
        $wrapper.on('change', 'select[name="abstract_box_options[font_family]"]', function () {
            updatePreview($wrapper);
        });

        /* Border radius input */
        $wrapper.on('input change', 'input[name="abstract_box_options[border_radius]"]', function () {
            updatePreview($wrapper);
        });

        /* Preset buttons */
        $wrapper.on('click', '.abstract-box-preset-btn', function () {
            var preset = $(this).data('preset');
            var colors = PRESETS[preset] || PRESETS['default'];

            $.each(colors, function (key, val) {
                if (key === 'style') {
                    $wrapper.find('input[name="abstract_box_options[style]"][value="' + val + '"]')
                            .prop('checked', true);
                    return;
                }
                $wrapper.find('input[name="abstract_box_options[' + key + '"]').iris('color', val);
            });

            setTimeout(function () {
                checkContrast($wrapper);
                updatePreview($wrapper);
            }, 100);
        });

        /* Initial render */
        checkContrast($wrapper);
        updatePreview($wrapper);
    });

}(jQuery));
