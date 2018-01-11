<?php
/**
 * Plugin Name:     Tour de Coure - Gutenberg Edition
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A custom plugin that creates some custom Gutenberg blocks!
 * Author:          Nikola Nikolov
 * Author URI:      https://paiyakdev.com
 * Text Domain:     tour-de-core-gutenberg
 * Domain Path:     /languages
 * Version:         0.1.4
 *
 * @package         Tour_De_Core_Gutenberg
 */

class TdC_Gutenberg {
	public static function start() {
		static $started = false;
 
		if ( ! $started ) {
			self::add_filters();
 
			self::add_actions();
 
			$started = true;
		}
	}
 
	protected static function add_filters() {
		// Add all filters here
	}
 
	protected static function add_actions() {
		// Add all actions here
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_admin_scripts' ), 1 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_admin_scripts' ), 1 );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_gutenberg_scripts' ), 10 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend_assets' ), 10 );
		add_action( 'init', array( __CLASS__, 'register_blocks' ), 10 );
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest_api_fields' ), 10 );
		add_action( 'init', array( __CLASS__, 'register_team_post_type' ) );
	}
	
	public static function register_rest_api_fields() {
		register_rest_field(
			'team',
			'information',
			array(
				'get_callback'	=> array( __CLASS__, 'get_team_member_meta_for_api' ),
				'schema'		=> null,
			)
		);

		register_rest_field(
			'team',
			'team_member_name',
			array(
				'get_callback'		=> array( __CLASS__, 'get_meta_for_rest_api' ),
				'update_callback'	=> array( __CLASS__, 'update_meta_for_rest_api' ),
				'schema'			=> null,
			)
		);

		register_meta(
			'post', 'team_member_name', array(
					'show_in_rest' => true,
					'single'       => true,
					'type'         => 'string',
			)
		);

		register_meta(
			'post', 'member_image', array(
					'show_in_rest' => true,
					'single'       => true,
					'type'         => 'integer',
			)
		);

		register_meta(
			'post', 'position', array(
					'show_in_rest' => true,
					'single'       => true,
					'type'         => 'string',
			)
		);
	}

	public static function get_meta_for_rest_api( $object, $key ) {
		if ( 'team' != $object['type'] ) {
			return null;
		}

		return get_post_meta( $object['id'], $key, true );
	}

	public static function update_meta_for_rest_api( $value, $object, $key ) {
		if ( 'team' != $object['type'] ) {
			return null;
		}

		update_post_meta( $object['id'], $key, $value );
	}

	public static function get_team_member_meta_for_api( $object ) {
		$id = $object['id'];
		$data = array(
			'name'				=> get_field( 'team_member_name', $id ),
			'image'				=> get_field( 'member_image', $id, false ),
			'position'			=> get_field( 'position', $id ),
			'short_bio'			=> get_field( 'short_bio', $id ),
			'contact_details'	=> get_field( 'contact_details', $id ),
		);

		if ( ! empty( $data['image'] ) ) {
			$data['image'] = wp_get_attachment_image_src( $data['image'], 'large' );
			$data['image'] = $data['image'][0];
		} else {
			$data['image'] = '';
		}

		return $data;
	}

	public static function register_admin_scripts() {
		if ( apply_filters( 'tdc/blocks/default_styling', true ) ) {
			wp_register_style( 'tdc-gutenberg-blocks', plugins_url( 'assets/css/blocks.css', __FILE__ ) );
		}
		wp_register_script( 'tdc-base', plugins_url( 'assets/js/tdc-base.js', __FILE__ ), array( 'wp-blocks', 'wp-element' ), 'v0.1.4' );
		wp_register_script( 'tdc-gutenberg-hello-world-block', plugins_url( 'assets/js/hello-world-block.js', __FILE__ ), array( 'tdc-base' ), 'v0.1.4' );
		wp_register_script( 'tdc-gutenberg-recent-posts-block', plugins_url( 'assets/js/recent-posts-block.js', __FILE__ ), array( 'tdc-base' ), 'v0.1.4' );
		wp_register_script( 'tdc-gutenberg-team-members-block', plugins_url( 'assets/js/team-members-block.js', __FILE__ ), array( 'tdc-base', 'wp-util' ), 'v0.1.4' );
		wp_register_script( 'tdc-gutenberg-team-member-information-block', plugins_url( 'assets/js/team-member-information-block.js', __FILE__ ), array( 'tdc-base' ), 'v0.1.4' );
	}

	public static function enqueue_gutenberg_scripts() {
		if ( apply_filters( 'tdc/blocks/default_styling', true ) ) {
			wp_enqueue_style( 'tdc-gutenberg-blocks' );
		}
		wp_enqueue_script( 'tdc-gutenberg-hello-world-block' );
		wp_enqueue_script( 'tdc-gutenberg-recent-posts-block' );
		wp_enqueue_script( 'tdc-gutenberg-team-members-block' );
		if ( 'team' == get_post_type() ) {
			wp_enqueue_script( 'tdc-gutenberg-team-member-information-block' );
		}

		add_action( 'admin_footer', array( __CLASS__, 'render_team_members_gutenberg_template' ) );
	}

	public static function enqueue_frontend_assets() {
		if ( apply_filters( 'tdc/blocks/default_styling', true ) ) {
			wp_enqueue_style( 'tdc-gutenberg-blocks' );
		}
	}

	public static function register_blocks() {
		register_block_type(
			'tdc/recent-posts',
			array(
				'render_callback' => array( __CLASS__, 'render_recent_posts_block' ),
			)
		);

		register_block_type(
			'tdc/team-members',
			array(
				'render_callback' => array( __CLASS__, 'render_team_members_block' ),
			)
		);
	}

	public static function render_recent_posts_block( $attributes ) {
		$recent_post = get_posts( array(
			'post_type'		=> $attributes['post_type'],
			'numberposts'	=> 1,
		) );

		if ( ! $recent_post ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return sprintf( '<strong>There are no posts from the %s post type!</strong>', esc_html( $attributes['post_type'] ) );
			} else {
				return '';
			}
		}

		return '<p>Most recent post: ' . esc_html( get_the_title( $recent_post[0]->ID ) ) . '</p>';
	}

	public static function render_team_members_block( $attributes ) {
		ob_start();

		$team_member = get_posts( array(
			'post_per_page'	=> -1,
			'post_type'		=> 'team',
			'orderby' => 'date',
			'order' => 'ASC',
		) ); ?>
		<div id="team" class="anchor-target"></div>
		<div class="sectionWrapper bg1">
			<div class="container team-boxes">
				<?php if ( ! empty( $attributes['title'] ) ) { ?>
					<div class="cell-12">
						<h3 class="block-head-secondary"><?php echo $attributes['title'] ?></h3>
					</div>
				<?php }
				foreach ( $team_member as $team ) {
					$data = array(
						'position'	=> get_field( 'position', $team->ID ),
						'short_bio'	=> get_field( 'short_bio', $team->ID ),
						'name'		=> get_field( 'team_member_name', $team->ID ),
						'image'		=> get_field( 'member_image', $team->ID ),
						'contact'	=> get_field( 'contact_details', $team->ID ),
					);
					if ( ! empty( $data['image'] ) ) {
						$data['image'] = wp_get_attachment_image_src( $data['image'], 'large' );
						$data['image'] = $data['image'][0];
					}

					self::render_tempalte( 'team-member', $data );
				} ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function register_team_post_type() {
		$labels = array(
			'name'               => _x( 'Team Members', 'post type general name', 'tour-de-core-gutenberg' ),
			'singular_name'      => _x( 'Team Member', 'post type singular name', 'tour-de-core-gutenberg' ),
			'menu_name'          => _x( 'Team', 'admin menu', 'tour-de-core-gutenberg' ),
			'name_admin_bar'     => _x( 'Team Member', 'add new on admin bar', 'tour-de-core-gutenberg' ),
			'add_new'            => _x( 'Add New', 'team member', 'tour-de-core-gutenberg' ),
			'add_new_item'       => __( 'Add New Team Member', 'tour-de-core-gutenberg' ),
			'new_item'           => __( 'New Team Member', 'tour-de-core-gutenberg' ),
			'edit_item'          => __( 'Edit Team Member', 'tour-de-core-gutenberg' ),
			'view_item'          => __( 'View Team Member', 'tour-de-core-gutenberg' ),
			'all_items'          => __( 'All Team Members', 'tour-de-core-gutenberg' ),
			'search_items'       => __( 'Search Team Members', 'tour-de-core-gutenberg' ),
			'parent_item_colon'  => __( 'Parent Team Members:', 'tour-de-core-gutenberg' ),
			'not_found'          => __( 'No team members found.', 'tour-de-core-gutenberg' ),
			'not_found_in_trash' => __( 'No team members found in Trash.', 'tour-de-core-gutenberg' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'team' ),
			'capability_type'    => 'post',
			'has_archive'        => 'team',
			'hierarchical'       => false,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-groups',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'template_lock'      => 'all',
			'template' => array(
				array( 'tdc/team-member-information', array(
				) ),
			),
		);

		register_post_type( 'team', $args );
	}

	public static function render_tempalte( $template_name, $tmpl_data = null ) {
		$template = locate_template( 'tdc/' . $template_name );
		if ( ! $template ) {
			$template = dirname( __FILE__ ) . '/includes/templates/' . $template_name . '.php';
		}

		if ( ! $template || ! file_exists( $template ) ) {
			return;
		}

		include $template;
	}

	public static function render_team_members_gutenberg_template() {
		self::render_tempalte( 'team-member' );
	}
}

add_action( 'plugins_loaded', function() {
	if ( function_exists( 'register_block_type' ) ) {
		TdC_Gutenberg::start();
	}
} );
