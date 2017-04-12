<?php

/*
Plugin Name: Simple TinyMCE shortcode button
Plugin URI: https://github.com/gripgrip/m-simple-button
Description: A simple WordPress plugin that adds a button to TinyMCE to generate a shortcode.
Version: 1.0.0
Author: Mircea Sandu
Author URI: https://mircian.com
License: GPL2
*/

/**
 * Class msb_main
 */
class msb_main {

	/**
	 * @var string Version of the plugin
	 */
	public $version = '1.0.0';

	/**
	 * msb_main constructor.
	 */
	public function __construct() {

		// define plugin constants
		$this->define_constants();

		// filter the buttons in the TinyMCE editor - see https://codex.wordpress.org/Plugin_API/Filter_Reference/mce_buttons,_mce_buttons_2,_mce_buttons_3,_mce_buttons_4 for other buttons
		add_filter( 'mce_buttons', array( $this, 'add_embed_button' ), 140 );

		// filter the external js plugins loaded in the editor
		add_filter( 'mce_external_plugins', array( $this, 'add_embed_button_js' ) );

		// add the actual shortcode being output
		add_shortcode('msb_video', array($this, 'video_shortcode'));

	}

	/**
	 * Define plugin constants
	 */
	private function define_constants() {

		define( 'MSB_PLUGIN_FILE', __FILE__ );
		define( 'MSB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'MSB_VERSION', $this->version );

	}

	/**
	 * @param $buttons array of buttons displayed in TinyMCE
	 *
	 * @return array
	 */
	public function add_embed_button( $buttons ) {

		array_push( $buttons, '|', 'msb_button' );

		return $buttons;

	}

	/**
	 * @param $plugin_array array of plugins loaded by TinyMCE
	 *
	 * @return array
	 */
	function add_embed_button_js( $plugin_array ) {

		$plugin_array['msb_button'] = plugin_dir_url( MSB_PLUGIN_FILE ) . 'assets/js/embed_button.js';

		return $plugin_array;

	}

	/**
	 * @param $atts array of shortcode attributes
	 *
	 * @return false|string
	 */
	public function video_shortcode($atts) {

		$a = shortcode_atts(array(
			'video' => false,
			'autoplay' => false,
			'width' => false,
			'height' => false
		), $atts, 'ttu_video');

		$embed = false;

		if ( $a['video'] ) {

			// add autoplay using a filter
			if ( $a['autoplay'] ) {
				add_filter('oembed_result',array($this, 'oembed_result') , 15, 3);
			}

			// build the shortcode based on options
			$args = array();
			if ( $a['width'] ) {
				$args['width'] = $a['width'];
			}
			if ( $a['height'] ) {
				$args['height'] = $a['height'];
			}
			$embed = wp_oembed_get( esc_url($a['video']), $args );

			// remove the filter to prevent changing other oembeds
			if ( $a['autoplay'] ) {
				remove_filter( 'oembed_result', array( $this, 'oembed_result' ), 15 );
			}

		}

		return $embed;

	}

	/**
	 * @param $html string with the html of the embed
	 * @param $url string the url used to generate the embed
	 * @param $args array options sent as arguments to the wp_oembed_get function
	 *
	 * @return mixed
	 */
	function oembed_result($html, $url, $args) {

		// $args includes custom arguments
		// modify $html as you need
		if ( isset($args['autoplay']) && $args['autoplay'] ) {
			$html = str_replace('feature=oembed', 'feature=oembed&autoplay=1', $html);
		}

		return $html;
	}

}

new msb_main();