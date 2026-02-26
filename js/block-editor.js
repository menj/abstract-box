/**
 * Abstract Box — Block Editor Script
 *
 * Registers the abstract-box/abstract Gutenberg block with a full
 * server-side render preview and inspector controls for all attributes
 * that the [abstract] shortcode accepts.
 *
 * @package AbstractBox
 * @since   2.0.0
 */

(function (wp) {
    var el = wp.element.createElement;
    var registerBlockType    = wp.blocks.registerBlockType;
    var useBlockProps        = wp.blockEditor.useBlockProps;
    var InspectorControls    = wp.blockEditor.InspectorControls;
    var RichText             = wp.blockEditor.RichText;
    var PanelBody            = wp.components.PanelBody;
    var TextControl          = wp.components.TextControl;
    var SelectControl        = wp.components.SelectControl;
    var ColorPalette         = wp.components.ColorPalette;
    var ServerSideRender     = wp.serverSideRender;

    registerBlockType('abstract-box/abstract', {
        title: 'Abstract Box',
        icon: 'book',
        category: 'text',
        attributes: {
            title:       { type: 'string',  default: 'Abstract' },
            subtitle:    { type: 'string',  default: '' },
            titleTag:    { type: 'string',  default: 'div' },
            bgColor:     { type: 'string',  default: '' },
            bgColorEnd:  { type: 'string',  default: '' },
            textColor:   { type: 'string',  default: '' },
            titleColor:  { type: 'string',  default: '' },
            accentColor: { type: 'string',  default: '' },
            content:     { type: 'string',  default: 'Your abstract content here.' }
        },

        edit: function (props) {
            var attributes    = props.attributes;
            var setAttributes = props.setAttributes;
            var blockProps    = useBlockProps();

            var titleTagOptions = [
                { label: 'div (default)',  value: 'div' },
                { label: 'h2',  value: 'h2' },
                { label: 'h3',  value: 'h3' },
                { label: 'h4',  value: 'h4' },
                { label: 'h5',  value: 'h5' },
                { label: 'h6',  value: 'h6' },
                { label: 'p',   value: 'p' },
                { label: 'span', value: 'span' }
            ];

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
                        label: 'Title Tag',
                        value: attributes.titleTag,
                        options: titleTagOptions,
                        onChange: function (val) { setAttributes({ titleTag: val }); }
                    })
                ),

                el(PanelBody, { title: 'Colour Overrides', initialOpen: false },
                    el('p', { style: { marginTop: 0, marginBottom: 4, fontWeight: 500 } }, 'Background Start'),
                    el(ColorPalette, {
                        colors: [],
                        value: attributes.bgColor,
                        onChange: function (val) { setAttributes({ bgColor: val || '' }); },
                        disableCustomColors: false,
                        clearable: true
                    }),
                    el('p', { style: { marginTop: 8, marginBottom: 4, fontWeight: 500 } }, 'Background End'),
                    el(ColorPalette, {
                        colors: [],
                        value: attributes.bgColorEnd,
                        onChange: function (val) { setAttributes({ bgColorEnd: val || '' }); },
                        disableCustomColors: false,
                        clearable: true
                    }),
                    el('p', { style: { marginTop: 8, marginBottom: 4, fontWeight: 500 } }, 'Text Colour'),
                    el(ColorPalette, {
                        colors: [],
                        value: attributes.textColor,
                        onChange: function (val) { setAttributes({ textColor: val || '' }); },
                        disableCustomColors: false,
                        clearable: true
                    }),
                    el('p', { style: { marginTop: 8, marginBottom: 4, fontWeight: 500 } }, 'Title Colour'),
                    el(ColorPalette, {
                        colors: [],
                        value: attributes.titleColor,
                        onChange: function (val) { setAttributes({ titleColor: val || '' }); },
                        disableCustomColors: false,
                        clearable: true
                    }),
                    el('p', { style: { marginTop: 8, marginBottom: 4, fontWeight: 500 } }, 'Accent Colour'),
                    el(ColorPalette, {
                        colors: [],
                        value: attributes.accentColor,
                        onChange: function (val) { setAttributes({ accentColor: val || '' }); },
                        disableCustomColors: false,
                        clearable: true
                    })
                )
            );

            var previewRender = el('div', { style: { marginBottom: '15px' } },
                el(ServerSideRender, {
                    block: 'abstract-box/abstract',
                    attributes: attributes,
                    LoadingResponsePlaceholder: function () {
                        return el('div', {}, 'Loading Preview...');
                    }
                })
            );

            var contentEditor = el(
                'div',
                {
                    style: {
                        padding: '12px',
                        background: '#f1f5f9',
                        border: '1px dashed #cbd5e1',
                        borderRadius: '6px'
                    }
                },
                el('p', {
                    style: {
                        margin: '0 0 8px 0',
                        fontSize: '13px',
                        fontWeight: 'bold',
                        color: '#475569'
                    }
                }, '\u270f\ufe0f Block Content Editor'),
                el(RichText, {
                    tagName: 'div',
                    value: attributes.content,
                    onChange: function (val) { setAttributes({ content: val }); },
                    placeholder: 'Write your abstract text here\u2026',
                    style: {
                        background: '#fff',
                        padding: '10px',
                        minHeight: '60px',
                        border: '1px solid #e2e8f0',
                        color: '#000'
                    }
                })
            );

            return el('div', blockProps, inspectorControls, previewRender, contentEditor);
        },

        save: function () {
            // Rendered entirely in PHP via render_callback (ServerSideRender).
            return null;
        }
    });
}(window.wp));
