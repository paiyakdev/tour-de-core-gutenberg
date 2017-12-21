<?php
/**
 * Plugin Name:     Tour de Coure - Gutenberg Edition
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A custom plugin that creates some custom Gutenberg blocks!
 * Author:          Nikola Nikolov
 * Author URI:      https://paiyakdev.com
 * Text Domain:     tour-de-core-gutenberg
 * Domain Path:     /languages
 * Version:         0.1.0
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
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_gutenberg_scripts' ), 10 );
	}

	public static function enqueue_gutenberg_scripts() {
		wp_enqueue_script( 'tdc-gutenberg-hello-world-block', plugins_url( 'assets/js/hello-world-block.js', __FILE__ ), array( 'wp-blocks', 'wp-element' ) );
	}
}
 
TdC_Gutenberg::start();
