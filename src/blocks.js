//  Import CSS.
import './scss/style.scss';
import './scss/editor.scss';

const {__} = wp.i18n;
const {registerBlockType} = wp.blocks;
const {SelectControl, PanelBody, PanelRow, TextControl} = wp.components;
const {InspectorControls, ColorPalette} = wp.editor;

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType('radiustheme/tlp-team', {
    title: __('Tlp Team', "the-team"),
    icon: 'grid-view',
    description: __('This is the tlp team settings section', "the-team"),
    category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
    keywords: [
        __('Tlp Team', "the-team"),
        __('tlp-team', "the-team"),
    ],
    attributes: {
        layout: {
            type: 'number',
            default: 1,
        },
        column: {
            type: 'number',
            default: 4,
        },
        orderby: {
            type: 'string',
            default: null,
        },
        order: {
            type: 'string',
            default: null,
        },
        id: {
            type: 'number',
            default: null,
        },
        member: {
            type: 'number',
            default: 0,
        },
        wrapperClass: {
            type: 'string',
            default: null,
        },
        nameColor: {
            type: 'string',
            default: null
        },
        designationColor: {
            type: 'string',
            default: null
        },
        sdColor: {
            type: 'string',
            default: null
        }
    },

    /**
     * The edit function describes the structure of your block in the context of the editor.
     * This represents what the editor will render when the block is used.
     *
     * The "edit" property must be a valid function.
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
     */
    edit: function (props) {
        let {attributes: {layout, column, orderby, order, nameColor, designationColor, sdColor, id, member, wrapperClass}, setAttributes} = props;
        let layouts = [{value: 0, label: __("Default", "the-team")}];
        let columns = [{value: 0, label: __("Default", "the-team")}];
        let orderByS = [{value: 0, label: __("Default", "the-team")}];
        let orders = [{value: 0, label: __("Default", "the-team")}];
        if (tlpTeam.layout) {
            for (const [id, title] of Object.entries(tlpTeam.layout)) {
                layouts.push({
                    value: id,
                    label: title
                });
            }
        }
        if (tlpTeam.column) {
            for (const [id, title] of Object.entries(tlpTeam.column)) {
                columns.push({
                    value: id,
                    label: title
                });
            }
        }
        if (tlpTeam.orderby) {
            for (const [id, title] of Object.entries(tlpTeam.orderby)) {
                orderByS.push({
                    value: id,
                    label: title
                });
            }
        }
        if (tlpTeam.order) {
            for (const [id, title] of Object.entries(tlpTeam.order)) {
                orders.push({
                    value: id,
                    label: title
                });
            }
        }
        return (
            [
                <InspectorControls>
                    <PanelBody title={__('Layout & Filter Settings', 'tlp-team')} initialOpen={true}>
                        <SelectControl
                            label={__('Layout:', "tlp-team")}
                            options={layouts}
                            value={layout}
                            onChange={(val) => setAttributes({layout: Number(val)})}
                        />
                        <SelectControl
                            label={__('Column / Number to Display at slider:', "tlp-team")}
                            options={columns}
                            value={column}
                            onChange={(val) => setAttributes({column: Number(val)})}
                        />
                        <SelectControl
                            label={__('Order By:', "tlp-team")}
                            options={orderByS}
                            value={orderby}
                            onChange={(val) => setAttributes({orderby: val})}
                        />
                        <SelectControl
                            label={__('Order:', "tlp-team")}
                            options={orders}
                            value={order}
                            onChange={(val) => setAttributes({order: val})}
                        />
                    </PanelBody>
                    <PanelBody title={__('Filter Settings', 'tlp-team')} initialOpen={false}>
                        <TextControl
                            label={__('Include Member Only', 'tlp-team')}
                            value={id}
                            onChange={(val) => setAttributes({id: val})}
                            help={__("List of member IDs (comma-separated values, for example: 1,2,3)", "tlp-team")}
                        />
                        <TextControl
                            label={__('Total Member (Only number is allowed)', 'tlp-team')}
                            value={member ? member : null}
                            onChange={(val) => setAttributes({member: Number(val)})}
                            help={__("Leave it blank to display all. (Only number is allowed)", "tlp-team")}
                        />
                    </PanelBody>
                    <PanelBody title={__('Style Settings', 'tlp-team')} initialOpen={false}>
                        <label>{__('Name color:', "tlp-team")}</label>
                        <ColorPalette
                            value={nameColor}
                            onChange={(val) => setAttributes({nameColor: val})}
                        />
                        <label>{__('Designation color:', "tlp-team")}</label>
                        <ColorPalette
                            value={designationColor}
                            onChange={(val) => setAttributes({designationColor: val})}
                        />
                        <label>{__('Short description color', 'tlp-team')}</label>
                        <ColorPalette
                            value={sdColor}
                            onChange={(val) => setAttributes({sdColor: val})}
                        />
                        <TextControl
                            label={__('Wrapper Class', 'tlp-team')}
                            value={wrapperClass}
                            onChange={(val) => setAttributes({wrapperClass: val})}
                        />
                    </PanelBody>
                </InspectorControls>
                ,
                <div className={props.className}>
                    <div><span><img src={tlpTeam.icon}/></span> <span>{__('Tlp Team', "the-team")}</span></div>
                    <div className="tlp-team-gb-shortcode-wrapper">
                        <div>{__("Layout", "tlp-team")}: {(layout ? layout : 1)}</div>
                        <div>{__("Column", "tlp-team")}: {(column ? column : 4)}</div>
                        {orderby ? <div>{__("Order By", "tlp-team")}: {orderby}</div> : ''}
                        {order ? <div>{__("Order", "tlp-team")}: {order}</div> : ''}
                        {id ? <div>{__("Include Member Only", "tlp-team")}: {id}</div> : ''}
                        {member ? <div>{__("Total Member", "tlp-team")}: {member}</div> : ''}
                        {nameColor ? <div>{__("Name color", "tlp-team")}: <span style={{
                            backgroundColor: nameColor,
                            width: '30px',
                            height: '15px',
                            display: 'inline-block'
                        }}></span></div> : ''}
                        {designationColor ? <div>{__("Designation color", "tlp-team")}: <span style={{
                            backgroundColor: designationColor,
                            width: '30px',
                            height: '15px',
                            display: 'inline-block'
                        }}></span></div> : ''}
                        {sdColor ? <div>{__('Short description color', 'tlp-team')}: <span style={{
                            backgroundColor: sdColor,
                            width: '30px',
                            height: '15px',
                            display: 'inline-block'
                        }}></span></div> : ''}
                        {wrapperClass ? <div>{__("Wrapper Class", "tlp-team")}: {wrapperClass}</div> : ''}
                    </div>
                </div>
            ]
        );
    },

    /**
     * The save function defines the way in which the different attributes should be combined
     * into the final markup, which is then serialized by Gutenberg into post_content.
     *
     * The "save" property must be specified and must be a valid function.
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
     */
    save: function () {
        return null;
    },
});