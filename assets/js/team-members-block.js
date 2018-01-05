(function () {
	"use strict";

	const { __ } = wp.i18n;
	const { ColorPalette, registerBlockType, BlockControls, Toolbar, MediaUploadButton, Dashicon, BlockDescription, InspectorControls } = wp.blocks;
	const { TextControl, SelectControl } = InspectorControls;
	const { withAPIData } = wp.components;

	class TdCTeamMembers extends TdC_Component {
		constructor() {
			super(...arguments);

			this.onTitleChange = this._onTextAttributeChange.bind(this, 'title');
		}

		render() {
			const { team_members, attributes } = this.props;
			const { title } = attributes;
			if (!team_members.data) {
				return 'Loading Team Members Information...';
			}

			var content;
			if (!team_members.data.length) {
				content = wp.element.createElement(
					'strong',
					null,
					'There are no team members yet! Please add some.'
				);
			} else {
				content = wp.element.createElement(
					'div',
					{ className: 'team-members-outer-wrap' },
					title ? wp.element.createElement(
						'h3',
						null,
						title
					) : null,
					wp.element.createElement(
						'div',
						{ className: 'team-members-wrap' },
						team_members.data.map(function (member, i) {
							var image = null,
							    image_src;

							if (member.information.image) {
								if (member.information.image.sizes.large) {
									image_src = member.information.image.sizes.large;
								} else {
									image_src = member.information.image.url;
								}

								image = wp.element.createElement('img', { className: 'team-member-image', src: image_src });
							}

							return wp.element.createElement(
								'div',
								{ className: 'team-member', key: 'team-member-' + member.id + '-' + i },
								image,
								wp.element.createElement(
									'h4',
									null,
									member.information.name
								),
								member.information.position ? wp.element.createElement(
									'h5',
									{ className: 'member-position' },
									member.information.position
								) : null,
								member.information.short_bio ? wp.element.createElement(
									'p',
									{ className: 'member-short-bio' },
									member.information.short_bio
								) : null
							);
						})
					)
				);
			}

			return [focus && wp.element.createElement(
				InspectorControls,
				{ key: 'inspector' },
				wp.element.createElement(
					BlockDescription,
					null,
					wp.element.createElement(
						'p',
						null,
						__('Displays a list of Team Member.')
					)
				),
				wp.element.createElement(TextControl, {
					label: __('Section Title'),
					value: title,
					onChange: this.onTitleChange
				})
			), wp.element.createElement(
				'div',
				{ key: 'content' },
				content
			)];
		}
	}

	TdCTeamMembers = withAPIData(function (props, { type }) {
		return {
			team_members: '/wp/v2/' + type('pdev_team') + '?per_page=10&orderby=date&order=asc'
		};
	})(TdCTeamMembers);

	registerBlockType('tdc/team-members', {
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
		}
	});
})();