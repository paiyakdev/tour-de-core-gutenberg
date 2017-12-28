(function(){
	"use strict";

	const { __ } = wp.i18n;
	const { ColorPalette, registerBlockType, BlockControls, Toolbar, MediaUploadButton, Dashicon, BlockDescription, InspectorControls } = wp.blocks;
	const { TextControl } = InspectorControls;

	class HelloWorld extends TdC_Component {
		constructor() {
			super( ...arguments );
		}

		updateContent( content ) {
			this.props.setAttributes({
				content: content
			});
		}

		updateColor( colorValue ) {
			this.props.setAttributes({
				color: colorValue
			});
		}

		render() {
			const { attributes, setAttributes, focus, setFocus, className, settings, toggleSelection } = this.props;
			const { content, color } = attributes;
			const blockStyle = { color: '#fff', padding: '20px' };
			if ( color ) {
				blockStyle.backgroundColor = color;
			}

			const blockDescription = (
				<BlockDescription>
					<p>{ __( 'We love Gutenberg!' ) }</p>
				</BlockDescription>
			);

			return [
				focus && (
						<InspectorControls key="inspector">
							{ blockDescription }
							<h3>{ __( 'Enter your message to the world!' ) }</h3>
							<TextControl label={ __( 'Message' ) } value={ content } onChange={ this.updateContent } help={ __( 'Words can be powerful.' ) } />
							<ColorPalette
								value={ color }
								onChange={this.updateColor}
							/>
						</InspectorControls>
				), <p key="content" style={ blockStyle }>{ content ? content : 'Enter text on the right.' }</p>
			];
		}
	}

	registerBlockType( 'tdc/hello-world', {
		title: 'Hello World (Step 1)',

		icon: 'universal-access-alt',

		attributes: {
			content: {
				type: 'string'
			},
			color: {
				type: 'string'
			}
		},

		category: 'layout',

		edit: HelloWorld,

		save( { attributes } ) {
			const { content, color } = attributes;
			const blockStyle = { color: '#fff', padding: '20px' };
			if ( color ) {
				blockStyle.backgroundColor = color;
			}

			return <p style={ blockStyle }>{ content }</p>;
		},
	} );
})();