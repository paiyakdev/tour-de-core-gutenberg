(function(){
	"use strict";

	const { __ } = wp.i18n;
	const { ColorPalette, registerBlockType, BlockControls, Toolbar, MediaUploadButton, Dashicon, BlockDescription, InspectorControls } = wp.blocks;
	const { TextControl, SelectControl } = InspectorControls;
	const { withAPIData, Button, Spinner, ResponsiveWrapper } = wp.components;

	class TdCTeamMemberInformation extends TdC_Component {
		constructor() {
			super( ...arguments );

			this.onNameChange = this._onTextAttributeChange.bind( this, 'team_member_name' );
			this.onPositionChange = this._onTextAttributeChange.bind( this, 'position' );
		}

		onImageChange( image ) {
			this.props.setAttributes({
				imageId: image.id
			});
		}

		render() {
			const { attributes, image } = this.props;
			const { team_member_name, position } = attributes;

			var content = (
				<div className="team-members-outer-wrap">
					<div className="team-members-wrap">
						<TextControl
							label={ __( 'Team Member Name' ) }
							value={team_member_name}
							onChange={this.onNameChange}
						/>
						<TextControl
							label={ __( 'Position' ) }
							value={position}
							onChange={this.onPositionChange}
						/>
						<MediaUploadButton
							title={ __( 'Team Member Imge' ) }
							onSelect={ this.onImageChange }
							type="image"
						>
							{ image && !! image.data &&
								<ResponsiveWrapper
									naturalWidth={ image.data.media_details.width }
									naturalHeight={ image.data.media_details.height }
								>
									<img src={ image.data.source_url } alt={ __( 'Member Image' ) } />
								</ResponsiveWrapper>
							}
							{ image && image.isLoading && <Spinner /> }
						</MediaUploadButton>
					</div>
				</div>
			);

			return [
				focus && (
						<InspectorControls key="inspector">
							<BlockDescription>
								<p>{ __( 'Fills out information about this team member.' ) }</p>
							</BlockDescription>
						</InspectorControls>
				), <div key="content">{ content }</div>
			];
		}
	}

	TdCTeamMemberInformation = withAPIData( function( props ) {
		const { imageId } = props.attributes;
		return {
			image: imageId ? `/wp/v2/media/${ imageId }` : undefined
		};
	} )( TdCTeamMemberInformation );

	registerBlockType( 'tdc/team-member-information', {
		title: 'Team Member Info',

		icon: 'id-alt',

		attributes: {
			team_member_name: {
				type:	'string',
				source:	'meta',
				meta:	'team_member_name'
			},
			position: {
				type:	'string',
				source:	'meta',
				meta:	'position'
			},
			imageId: {
				type:	'integer',
				source:	'meta',
				meta:	'member_image'
			}
		},

		category: 'common',

		edit: TdCTeamMemberInformation,

		save() {
			return null;
		},
	} );
})();