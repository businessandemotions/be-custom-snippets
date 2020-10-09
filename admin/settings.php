<?php
/**
 * Plugin settings.
 *
 * @package CustomSnippets
 */

namespace BE\CustomSnippets;

if ( ! defined( 'ABSPATH' ) ) {
	die( -1 );
}

?>

<div class="wrap">
	<form id="main-form" method="post" action="options.php">
		<?php settings_fields( 'be-custom-snippets' ); ?>
		<?php do_settings_sections( 'be-custom-snippets' ); ?>
		<nav class="nav-tab-wrapper">
			<a href="#css" class="nav-tab nav-tab-active">CSS</a>
			<a href="#js" class="nav-tab">JS</a>
		</nav>
		<div class="tab-content" data-tab-content="#css">
			<p><b><?php esc_html_e( 'Global CSS', 'be-custom-snippets' ); ?></b></p>
			<?php Admin::render_editor( 'be_custom_snippets[general_css]', 'javascript', get_option( 'be_custom_snippets', array() )['general_css'] ?? '' ); ?>
		</div>
		<div class="tab-content" data-tab-content="#js" style="display: none;">
			<p><b><?php esc_html_e( 'Global JS', 'be-custom-snippets' ); ?></b></p>
			<?php Admin::render_editor( 'be_custom_snippets[general_js]', 'javascript', get_option( 'be_custom_snippets', array() )['general_js'] ?? '' ); ?>
		</div>
		<script>
			(function() {
				var tabContents = document.querySelectorAll('[data-tab-content]');
				var tabs = document.querySelectorAll('.nav-tab-wrapper a');
				window.addEventListener('hashchange', function() {
					var selectedTabContent = document.querySelector('[data-tab-content="' + window.location.hash + '"]');
					var selectedTab = document.querySelector('[href="' + window.location.hash + '"]');
					Array.prototype.forEach.call(tabContents, function(content) {
						content.style.display = 'none';
					});
					Array.prototype.forEach.call(tabs, function(tab) {
						tab.classList.remove('data-tab-content');
					});
					if (selectedTabContent) {
						selectedTabContent.style.display = '';
					}
					if (selectedTab) {
						selectedTab.classList.add('nav-tab-active');
					}
				})
			})()
		</script>
		<input type="submit" class="button button-primary" value="Submit" />
	</form>
</div>
