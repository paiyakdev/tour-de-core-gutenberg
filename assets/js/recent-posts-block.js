(function () {
	"use strict";

	const { __ } = wp.i18n;
	const { ColorPalette, registerBlockType, BlockControls, Toolbar, MediaUploadButton, Dashicon, BlockDescription, InspectorControls } = wp.blocks;
	const { TextControl, SelectControl } = InspectorControls;
	const { withAPIData } = wp.components;
	const post_type_options = [{
		label: __('Posts'),
		value: 'post'
	}, {
		label: __('Pages'),
		value: 'page'
	}];

	class TdCRecentPosts extends TdC_Component {
		constructor() {
			super(...arguments);
		}

		onPostTypeChange(post_type) {
			this.props.setAttributes({
				post_type: post_type
			});
		}

		render() {
			const { posts, attributes } = this.props;
			const { post_type } = attributes;
			if (!posts.data) {
				return 'Loading...';
			}

			var content;
			if (!posts.data.length) {
				content = wp.element.createElement(
					'strong',
					null,
					'There are no posts from the ',
					post_type,
					' post type!'
				);
			} else {
				content = 'Most recent post: ' + posts.data[0].title.rendered;
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
						__('This will display some posts for you!')
					)
				),
				wp.element.createElement(SelectControl, {
					label: 'Post Type',
					value: post_type,
					options: post_type_options,
					onChange: this.onPostTypeChange
				})
			), wp.element.createElement(
				'p',
				{ key: 'content' },
				content
			)];
		}
	}
	TdCRecentPosts = withAPIData(function (props, { type }) {
		return {
			posts: '/wp/v2/' + type(props.attributes.post_type) + '?per_page=1'
		};
	})(TdCRecentPosts);

	registerBlockType('tdc/recent-posts', {
		title: 'Recent Posts( Step 2 )',

		icon: 'admin-page',

		attributes: {
			post_type: {
				type: 'string',
				default: 'post'
			}
		},

		category: 'common',

		edit: TdCRecentPosts,

		save() {
			return null;
		}
	});
})();