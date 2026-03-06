/**
 * Abstract Box — Block Editor Script
 *
 * Registers the abstract-box/abstract Gutenberg block using InnerBlocks
 * for content editing (correct dynamic block architecture) and
 *
 * Content lives in InnerBlocks inner HTML, not as a string attribute.
 * The render_callback receives it as $content (second parameter).
 *
 * @package AbstractBox
 * @since   2.1.6
 */

(function (wp) {
    var el               = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var useBlockProps    = wp.blockEditor.useBlockProps;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var InnerBlocks      = wp.blockEditor.InnerBlocks;
    var PanelBody        = wp.components.PanelBody;
    var TextControl      = wp.components.TextControl;
    var SelectControl    = wp.components.SelectControl;
    var ColorPalette     = wp.components.ColorPalette;

    registerBlockType('abstract-box/abstract', {
        title:    'Abstract Box',
        icon:     'book',
        category: 'text',

        // Content is NOT an attribute — it lives in InnerBlocks inner HTML.
        // The PHP render_callback receives it as $content (second parameter).
        attributes: {
            title:       { type: 'string', default: 'Abstract' },
            subtitle:    { type: 'string', default: '' },
            titleTag:    { type: 'string', default: 'div' },
            bgColor:     { type: 'string', default: '' },
            bgColorEnd:  { type: 'string', default: '' },
            textColor:   { type: 'string', default: '' },
            titleColor:  { type: 'string', default: '' },
            accentColor: { type: 'string', default: '' },
            style:       { type: 'string', default: '' }
        },

        edit: function (props) {
            var attributes    = props.attributes;
            var setAttributes = props.setAttributes;
            var blockProps    = useBlockProps();

            var titleTagOptions = [
                { label: 'div (default)', value: 'div' },
                { label: 'h2',  value: 'h2' },
                { label: 'h3',  value: 'h3' },
                { label: 'h4',  value: 'h4' },
                { label: 'h5',  value: 'h5' },
                { label: 'h6',  value: 'h6' },
                { label: 'p',   value: 'p'  },
                { label: 'span', value: 'span' }
            ];

            // Inspector controls — settings and colour overrides panels
            var inspectorControls = el(InspectorControls, {},

                el(PanelBody, { title: 'Abstract Settings', initialOpen: true },
                    el(TextControl, {
                        label: 'Title',
                        value: attributes.title,
                        onChange: function (val) { setAttributes({ title: val }); }
                    }),
                    el(TextControl, {
                        label: 'Subtitle',
                        value: attributes.subtitle,
                        onChange: function (val) { setAttributes({ subtitle: val }); }
                    }),
                    el(SelectControl, {
                        label:    'Title Tag',
                        value:    attributes.titleTag,
                        options:  titleTagOptions,
                        onChange: function (val) { setAttributes({ titleTag: val }); }
                    }),
                    el(SelectControl, {
                        label:   'Box Style',
                        value:   attributes.style,
                        options: [
                            { label: 'Global setting', value: '' },
                            { label: 'Modern',         value: 'modern' },
                            { label: 'Academic',       value: 'academic' },
                            { label: 'Minimal',        value: 'minimal' },
                            { label: 'Card',           value: 'card' },
                            { label: 'Ruled',          value: 'ruled' },
                            { label: 'Editorial',      value: 'editorial' },
                            { label: 'Summary',        value: 'summary' },
                            { label: 'Bulletin',       value: 'bulletin' },
                        ],
                        onChange: function (val) { setAttributes({ style: val }); }
                    })
                ),

                el(PanelBody, { title: 'Colour Overrides', initialOpen: false },
                    el('p', { style: { marginTop: 0, marginBottom: 4, fontWeight: 500 } }, 'Background Start'),
                    el(ColorPalette, {
                        colors: [], value: attributes.bgColor, disableCustomColors: false, clearable: true,
                        onChange: function (val) { setAttributes({ bgColor: val || '' }); }
                    }),
                    el('p', { style: { marginTop: 8, marginBottom: 4, fontWeight: 500 } }, 'Background End'),
                    el(ColorPalette, {
                        colors: [], value: attributes.bgColorEnd, disableCustomColors: false, clearable: true,
                        onChange: function (val) { setAttributes({ bgColorEnd: val || '' }); }
                    }),
                    el('p', { style: { marginTop: 8, marginBottom: 4, fontWeight: 500 } }, 'Text Colour'),
                    el(ColorPalette, {
                        colors: [], value: attributes.textColor, disableCustomColors: false, clearable: true,
                        onChange: function (val) { setAttributes({ textColor: val || '' }); }
                    }),
                    el('p', { style: { marginTop: 8, marginBottom: 4, fontWeight: 500 } }, 'Title Colour'),
                    el(ColorPalette, {
                        colors: [], value: attributes.titleColor, disableCustomColors: false, clearable: true,
                        onChange: function (val) { setAttributes({ titleColor: val || '' }); }
                    }),
                    el('p', { style: { marginTop: 8, marginBottom: 4, fontWeight: 500 } }, 'Accent Colour'),
                    el(ColorPalette, {
                        colors: [], value: attributes.accentColor, disableCustomColors: false, clearable: true,
                        onChange: function (val) { setAttributes({ accentColor: val || '' }); }
                    })
                )
            );

            // InnerBlocks — the correct way to handle editable content in a
            // dynamic block. Content is stored as inner HTML in the block grammar,
            // not as a string attribute. The PHP render_callback receives it as
            // the $content parameter.
            var innerBlocks = el(InnerBlocks, {
                template: [
                    [ 'core/paragraph', { placeholder: 'Write your abstract here\u2026 (paragraphs, lists, and headings are all supported)' } ]
                ],
                templateLock: false,
                allowedBlocks: [
                    'core/paragraph',
                    'core/list',
                    'core/heading',
                    'core/quote',
                    'core/separator'
                ]
            });

            // Wrap InnerBlocks in a styled container so authors can see context
            var contentArea = el(
                'div',
                {
                    style: {
                        padding:      '16px',
                        background:   '#f8fafc',
                        border:       '1px dashed #cbd5e1',
                        borderRadius: '6px',
                        marginTop:    '12px'
                    }
                },
                el('p', {
                    style: {
                        margin:     '0 0 8px 0',
                        fontSize:   '11px',
                        fontWeight: '600',
                        color:      '#94a3b8',
                        textTransform: 'uppercase',
                        letterSpacing: '0.06em'
                    }
                }, 'Abstract Content'),
                innerBlocks
            );

            return el('div', blockProps, inspectorControls, contentArea);
        },

        // save() must return InnerBlocks.Content so WordPress serialises the
        // inner blocks into the post's block grammar. The outer wrapper is
        // rendered entirely server-side via render_callback, so we return only
        // the inner content here.
        save: function () {
            return el(InnerBlocks.Content, {});
        }
    });

}(window.wp));
