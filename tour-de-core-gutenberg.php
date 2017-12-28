<?php
/**
 * Plugin Name:     Tour de Coure - Gutenberg Edition
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A custom plugin that creates some custom Gutenberg blocks!
 * Author:          Nikola Nikolov
 * Author URI:      https://paiyakdev.com
 * Text Domain:     tour-de-core-gutenberg
 * Domain Path:     /languages
 * Version:         0.1.1
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
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_gutenberg_scripts' ), 10 );
		add_action( 'init', array( __CLASS__, 'register_blocks' ), 10 );
	}

	public static function register_admin_scripts() {
		wp_register_script( 'tdc-base', plugins_url( 'assets/js/tdc-base.js', __FILE__ ), array( 'wp-blocks', 'wp-element' ), 'v0.1.1' );
		wp_register_script( 'tdc-gutenberg-hello-world-block', plugins_url( 'assets/js/hello-world-block.js', __FILE__ ), array( 'tdc-base' ), 'v0.1.1' );
		wp_register_script( 'tdc-gutenberg-recent-posts-block', plugins_url( 'assets/js/recent-posts-block.js', __FILE__ ), array( 'tdc-base' ), 'v0.1.1' );
	}

	public static function enqueue_gutenberg_scripts() {
		wp_enqueue_script( 'tdc-gutenberg-hello-world-block' );
		wp_enqueue_script( 'tdc-gutenberg-recent-posts-block' );
	}

	public static function register_blocks() {
		register_block_type(
			'tdc/recent-posts',
			array(
				'render_callback' => array( __CLASS__, 'render_recent_posts_block' ),
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
}

add_action( 'plugins_loaded', function() {
	if ( function_exists( 'register_block_type' ) ) {
		TdC_Gutenberg::start();
	}
} );
