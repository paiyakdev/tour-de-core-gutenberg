(function(){
	"use strict";

	const { __ } = wp.i18n;
	const { ColorPalette, registerBlockType, BlockControls, Toolbar, MediaUploadButton, Dashicon, BlockDescription, InspectorControls } = wp.blocks;
	const { TextControl, SelectControl } = InspectorControls;
	const { withAPIData } = wp.components;

	class TdCTeamMembers extends TdC_Component {
		constructor() {
			super( ...arguments );

			this.onTitleChange = this._onTextAttributeChange.bind( this, 'title' );
		}

		render() {
			const { team_members, attributes } = this.props;
			const { title } = attributes;
			if ( ! team_members.data ) {
				return 'Loading Team Members Information...';
			}

			var content;
			if ( ! team_members.data.length ) {
				content = <strong>There are no team members yet! Please add some.</strong>;
			} else {
				content = (
					<div className="team-members-outer-wrap">
						{title ? <h3>{title}</h3> : null}
						<div className="team-members-wrap">
							{team_members.data.map(function(member, i) {
								var image = null,
									image_src;

								if ( member.information.image ) {
									if ( member.information.image.sizes.large ) {
										image_src = member.information.image.sizes.large;
									} else {
										image_src = member.information.image.url;
									}

									image = <img className="team-member-image" src={image_src} />;
								}

								return (
									<div className="team-member" key={'team-member-' + member.id + '-' + i}>
										{image}
										<h4>{member.information.name}</h4>
										{ member.information.position ? <h5 className="member-position">{member.information.position}</h5> : null }
										{ member.information.short_bio ? <p className="member-short-bio">{member.information.short_bio}</p> : null }
									</div>
								);
							})}
						</div>
					</div>
				);
			}

			return [
				focus && (
						<InspectorControls key="inspector">
							<BlockDescription>
								<p>{ __( 'Displays a list of Team Member.' ) }</p>
							</BlockDescription>
							<TextControl
								label={ __( 'Section Title' ) }
								value={title}
								onChange={this.onTitleChange}
							/>
						</InspectorControls>
				), <div key="content">{ content }</div>
			];
		}
	}

	TdCTeamMembers = withAPIData( function( props, { type } ) {
		return {
			team_members: '/wp/v2/' + type( 'pdev_team' ) + '?per_page=10&orderby=date&order=asc'
		};
	} )( TdCTeamMembers );

	registerBlockType( 'tdc/team-members', {
		title: 'Paiyakdev Team',

		icon: 'groups',

		attributes: {
			title: {
				type: 'string'
			}
		},

		category: 'common',

		edit: TdCTeamMembers,

		save() {
			return null;
		},
	} );
})();