<?php
/**
 * Plugin Name:     Tour de Coure - Gutenberg Edition
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A custom plugin that creates some custom Gutenberg blocks!
 * Author:          Nikola Nikolov
 * Author URI:      https://paiyakdev.com
 * Text Domain:     tour-de-core-gutenberg
 * Domain Path:     /languages
 * Version:         0.1.3
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
	}
	
	public static function register_rest_api_fields() {
		register_rest_field(
			'pdev_team',
			'information',
			array(
				'get_callback'	=> array( __CLASS__, 'get_team_member_meta_for_api' ),
				'schema'		=> null,
			)
		);

		register_rest_field(
			'pdev_team',
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
		if ( 'pdev_team' != $object['type'] ) {
			return null;
		}

		return get_post_meta( $object['id'], $key, true );
	}

	public static function update_meta_for_rest_api( $value, $object, $key ) {
		if ( 'pdev_team' != $object['type'] ) {
			return null;
		}

		update_post_meta( $object['id'], $key, $value );
	}

	public static function get_team_member_meta_for_api( $object ) {
		$id = $object['id'];
		$data = array(
			'name'				=> get_field( 'team_member_name', $id ),
			'image'				=> get_field( 'member_image', $id ),
			'position'			=> get_field( 'position', $id ),
			'short_bio'			=> get_field( 'short_bio', $id ),
			'contact_details'	=> get_field( 'contact_details', $id ),
		);

		return $data;
	}

	public static function register_admin_scripts() {
		if ( apply_filters( 'tdc/blocks/default_styling', true ) ) {
			wp_register_style( 'tdc-gutenberg-blocks', plugins_url( 'assets/css/blocks.css', __FILE__ ) );
		}
		wp_register_script( 'tdc-base', plugins_url( 'assets/js/tdc-base.js', __FILE__ ), array( 'wp-blocks', 'wp-element' ), 'v0.1.3' );
		wp_register_script( 'tdc-gutenberg-hello-world-block', plugins_url( 'assets/js/hello-world-block.js', __FILE__ ), array( 'tdc-base' ), 'v0.1.3' );
		wp_register_script( 'tdc-gutenberg-recent-posts-block', plugins_url( 'assets/js/recent-posts-block.js', __FILE__ ), array( 'tdc-base' ), 'v0.1.3' );
		wp_register_script( 'tdc-gutenberg-team-members-block', plugins_url( 'assets/js/team-members-block.js', __FILE__ ), array( 'tdc-base' ), 'v0.1.3' );
		wp_register_script( 'tdc-gutenberg-team-member-information-block', plugins_url( 'assets/js/team-member-information-block.js', __FILE__ ), array( 'tdc-base' ), 'v0.1.3' );
	}

	public static function enqueue_gutenberg_scripts() {
		if ( apply_filters( 'tdc/blocks/default_styling', true ) ) {
			wp_enqueue_style( 'tdc-gutenberg-blocks' );
		}
		wp_enqueue_script( 'tdc-gutenberg-hello-world-block' );
		wp_enqueue_script( 'tdc-gutenberg-recent-posts-block' );
		wp_enqueue_script( 'tdc-gutenberg-team-members-block' );
		if ( 'pdev_team' == get_post_type() ) {
			wp_enqueue_script( 'tdc-gutenberg-team-member-information-block' );
		}
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
			'post_type'		=> 'pdev_team',
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
				foreach ( $team_member as $team ) :
					$member_position = get_field( 'position', $team->ID );
					$member_short_bio = get_field( 'short_bio', $team->ID );
					$member_name = get_field( 'team_member_name', $team->ID );
					$member_image = get_field( 'member_image', $team->ID );
					$member_contact = get_field( 'contact_details', $team->ID ); ?>
		
					<div class="cell-3">
						<div class="team-box">
							<div class="team-img">
								<?php if ( $member_image ) : ?>
									<img alt="<?php echo ( $member_name ); ?>" src="<?php echo esc_attr( $member_image['url'] ); ?>">
								<?php else : ?>
									<img alt="<?php echo ( $member_name ); ?>" src="<?php bloginfo( 'template_directory' ); ?>/images/faceless-team-member.jpg">
								<?php endif; ?>
								<h3><?php echo $member_name; ?></h3>
							</div>
							<div class="team-details">
								<h3 class="gry-bg"><?php echo $member_name; ?></h3>
								<?php if ( $member_position ) : ?>
									<div class="t-position"><?php echo $member_position; ?></div>
								<?php endif; ?>
								<?php echo wpautop( $member_short_bio ); ?>
		
								<div class="team-socials">
									<ul>
										<?php foreach ($member_contact as $contact) :
											switch ($contact['type']) {
												case 'email': ?>
													<li>
														<a href="mailto:<?php echo $contact['url_input']; ?>">
															<i class="fa fa-envelope"></i>
														</a>
													</li><?php
													break;
												
												case 'twitter': ?>
													<li>
														<a href="<?php echo $contact['url_input']; ?>">
															<i class="fa fa-twitter"></i>
														</a>
													</li> <?php
													break;
		
												case 'linkedin': ?>
													<li>
														<a href="<?php echo $contact['url_input']; ?>">
															<i class="fa fa-linkedin"></i>
														</a>
													</li><?php
													break;
		
												case 'skype': ?>
													<li>
														<a href="skype:<?php echo $contact['url_input']; ?>?chat">
															<i class="fa fa-skype"></i>
														</a>
													</li><?php
													break;
		
												case 'google_plus': ?>
													<li>
														<a href="<?php echo $contact['url_input']; ?>">
															<i class="fa fa-google-plus"></i>
														</a>
													</li><?php
													break;
		
												case 'facebook': ?>
													<li>
														<a href="<?php echo $contact['url_input']; ?>">
															<i class="fa fa-facebook"></i>
														</a>
													</li><?php
													break;
											}?>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

add_action( 'plugins_loaded', function() {
	if ( function_exists( 'register_block_type' ) ) {
		TdC_Gutenberg::start();
	}
} );
