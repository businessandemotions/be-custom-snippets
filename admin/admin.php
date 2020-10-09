<?php
/**
 * Admin.
 *
 * @package CustomSnippets
 */

namespace BE\CustomSnippets;

if ( ! defined( 'ABSPATH' ) ) {
	die( -1 );
}

class Admin {

	public static function render_editor( $name, $mode, $value = '' ) {
		?>
		<div class="editor" data-be-snippets-editor="<?php echo esc_attr( $mode ); ?>" data-be-snippets-name="<?php echo esc_attr( trim( $name ) ); ?>">
			<input type="hidden" name="<?php echo esc_attr( trim( $name ) ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		</div>
		<?php
	}

	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );

		add_action( 'load-post.php', array( $this, 'init_metabox' ) );
		add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );

	}

	public function assets() {

		wp_register_style( 'CodeMirror/Editor', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/codemirror.min.css', array(), '5.21.0' );
		wp_register_style( 'CodeMirror/Theme/pastel-on-dark', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/theme/pastel-on-dark.min.css', array( 'CodeMirror/Editor' ), '5.21.0' );

		wp_register_script( 'CodeMirror/Editor', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/codemirror.min.js', array(), '5.21.0', true );
		wp_register_script( 'CodeMirror/Mode/css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/mode/css/css.min.js', array( 'CodeMirror/Editor' ), '5.21.0', true );
		wp_register_script( 'CodeMirror/Mode/js', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/mode/javascript/javascript.min.js', array( 'CodeMirror/Editor' ), '5.21.0', true );
		wp_register_script( 'CodeMirror/Addon/matchbrackets', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.21.0/addon/edit/matchbrackets.min.js', array( 'CodeMirror/Editor' ), '5.21.0', true );

		wp_register_script( 'BE/CustomSnippets/Admin', Plugin::uri( 'assets/admin.js' ), array( 'CodeMirror/Editor', 'CodeMirror/Mode/css', 'CodeMirror/Mode/js', 'CodeMirror/Addon/matchbrackets' ), Plugin::get_version(), true );

		$screen = get_current_screen();
		if ( 'settings_page_be-custom-snippets' === $screen->id || in_array( $screen->id, array( 'post', 'page' ), true ) ) {
			wp_enqueue_script( 'BE/CustomSnippets/Admin' );
			wp_enqueue_style( 'CodeMirror/Editor' );
			wp_enqueue_style( 'CodeMirror/Theme/pastel-on-dark' );
		}

	}

	public function register_settings() {

		register_setting(
			'be-custom-snippets',
			'be_custom_snippets'
		);
		register_setting(
			'be-custom-snippets',
			'be_custom_snippets[general_css]'
		);

	}

	public function add_settings_page() {

		add_options_page(
			__( 'Custom Snippets', 'be-custom-snippets' ),
			__( 'Custom Snippets', 'be-custom-snippets' ),
			'manage_options',
			'be-custom-snippets',
			array( $this, 'render_settings_view' )
		);

	}

	public function render_settings_view() {
		include Plugin::path( 'admin/settings.php' );
	}

	/**
	 * Adds the meta box.
	 */
	public function add_metabox() {

		add_meta_box(
			'be-custom-snippets',
			__( 'Custom Snippets', 'be-custom-snippets' ),
			array( $this, 'render_metabox' ),
			array( 'post', 'page' ),
			'advanced',
			'default'
		);

	}

	/**
	 * Meta box initialization.
	 */
	public function init_metabox() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
	}

	/**
	 * Renders the meta box.
	 */
	public function render_metabox( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'be_custom_snippets_nonce_action', 'be_cs_security' );

		$values = get_post_meta( $post->ID, 'be_custom_snippets', true );

		?>
		<p><b><?php esc_html_e( 'Post CSS', 'be-custom-snippets' ); ?></b></p>
		<?php
		static::render_editor( 'be_custom_snippets[css]', 'css', $values['css'] ?? '' );

		?>
		<p><b><?php esc_html_e( 'Post JS', 'be-custom-snippets' ); ?></b></p>
		<?php
		static::render_editor( 'be_custom_snippets[js]', 'javascript', $values['js'] ?? '' );

	}

	/**
	 * Handles saving the meta box.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return null
	 */
	public function save_metabox( $post_id, $post ) {

		// Add nonce for security and authentication.
		$nonce_name   = isset( $_POST['be_cs_security'] ) ? $_POST['be_cs_security'] : '';
		$nonce_action = 'be_custom_snippets_nonce_action';

		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		update_post_meta( $post_id, 'be_custom_snippets', $_POST['be_custom_snippets'] );

	}

}
