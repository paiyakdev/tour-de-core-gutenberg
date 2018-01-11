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
			this.template = wp.template('team-member');
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
					wp.element.createElement(
						'div',
						{ className: 'sectionWrapper bg1' },
						wp.element.createElement(
							'div',
							{ className: 'container team-boxes' },
							title ? wp.element.createElement(
								'div',
								{ className: 'cell-12' },
								wp.element.createElement(
									'h3',
									{ className: 'block-head-secondary' },
									title
								)
							) : null,
							team_members.data.map(function (member, i) {
								return wp.element.createElement('div', { key: 'team-member-' + member.id + '-' + i, dangerouslySetInnerHTML: { __html: this.template(member.information) } });
							}.bind(this))
						)
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
			team_members: '/wp/v2/' + type('team') + '?per_page=10&orderby=date&order=asc'
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