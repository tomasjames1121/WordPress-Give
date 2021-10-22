<?php

namespace Give\InPluginUpsells;

/**
 * @unreleased
 */
class AdminPage {
	/**
	 * Register menu item
	 */
	public function register() {
		add_submenu_page(
			'edit.php?post_type=give_forms',
			esc_html__( 'GiveWP Add-ons', 'give' ),
			esc_html__( 'Add-ons', 'give' ),
			'manage_give_settings',
			'give-add-ons',
			[ $this, 'render' ]
		);
	}

	/**
	 * Load scripts
	 */
	public function loadScripts() {
		wp_enqueue_script(
			'give-in-plugin-upsells',
			GIVE_PLUGIN_URL . 'assets/dist/js/give-in-plugin-upsells.js',
			[],
			GIVE_VERSION, true
		);

		wp_localize_script(
			'give-in-plugin-upsells',
			'GiveAddons',
			( new AddonsRepository() )->getAddons()
		);
	}

	/**
	 * Render admin page
	 */
	public function render() {
		echo '<div id="give-in-plugin-upsells">Aloha!</div>';
	}

	/**
	 * Helper function to determine if current page is Give Add-ons admin page
	 *
	 * @return bool
	 */
	public static function isShowing() {
		return isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] === 'give-add-ons';
	}
}
