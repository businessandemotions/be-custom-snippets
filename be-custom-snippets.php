<?php
/**
 * @link              https://businessandemotions.se
 * @since             1.0.0
 * @package           CustomSnippets
 *
 * @wordpress-plugin
 * Plugin Name:       Custom CSS/JS/HTML-snippets
 * Plugin URI:        https://businessandemotions.se
 * Description:
 * Version:           1.0.0
 * Author:            Business & Emotions
 * Author URI:        https://businessandemotoins.se
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       be-custom-snippets
 * Domain Path:       /languages
 */

namespace BE\CustomSnippets;

class Plugin {

	public static function path( $path ) {
		return plugin_dir_path( __FILE__ ) . $path;
	}

	public static function uri( $uri ) {
		return plugin_dir_url( __FILE__ ) . $uri;
	}

	public static function get_version() {
		return '1.0.0';
	}

	public function __construct() {

		$this->load_deps();

		$this->admin = new Admin();

		add_action( 'wp_footer', array( $this, 'load_css' ), 1000 );
		add_action( 'wp_footer', array( $this, 'load_js' ), 1000 );

	}

	private function load_deps() {

		require_once static::path( 'admin/admin.php' );

	}

	public function load_css() {

		$settings = get_option( 'be_custom_snippets', array() );

		$css = $settings['general_css'] ?? '';

		if ( is_singular() ) {

			$meta = get_post_meta( get_the_ID(), 'be_custom_snippets', true );
			$css .= $meta['css'];

		}
		$css = trim( apply_filters( 'BE/CustomSnippets/Load/CSS', $css ) );
		if ( ! empty( $css ) ) {
			echo "<style>\n" . $css . "\n</style>";
		}

	}

	public function load_js() {

		$settings = get_option( 'be_custom_snippets', array() );

		$js = $settings['general_js'] ?? '';

		if ( is_singular() ) {

			$meta = get_post_meta( get_the_ID(), 'be_custom_snippets', true );
			$js  .= $meta['js'];

		}
		$js = trim( apply_filters( 'BE/CustomSnippets/Load/JS', $js ) );
		if ( ! empty( $js ) ) {
			echo "<script>\n(function() {\n" . $js . "\n})()\n</script>";
		}

	}

}

$be_custom_snippets = new Plugin();
