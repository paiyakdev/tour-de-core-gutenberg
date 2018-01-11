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
			this.template = wp.template( 'team-member' );
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
						<div className="sectionWrapper bg1">
							<div className="container team-boxes">
								{ title ?
									<div className="cell-12">
										<h3 className="block-head-secondary">{title}</h3>
									</div> : null
								}

								{team_members.data.map(function(member, i) {
									return (
										<div key={'team-member-' + member.id + '-' + i} dangerouslySetInnerHTML={{__html: this.template(member.information)}} />
									);
								}.bind(this))}
							</div>
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
			team_members: '/wp/v2/' + type( 'team' ) + '?per_page=10&orderby=date&order=asc'
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