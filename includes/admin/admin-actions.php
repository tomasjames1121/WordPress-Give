<?php
/**
 * Admin Actions
 *
 * @package     Give
 * @subpackage  Admin/Actions
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Hide subscription notice if admin click on "Click here if already renewed" in subscription notice.
 *
 * @since 1.7
 * @return void
 */
function give_hide_subscription_notices() {

	// Hide subscription notices permanently.
	if ( ! empty( $_GET['_give_hide_license_notices_permanently'] ) ) {
		$current_user = wp_get_current_user();

		// check previously disabled notice ids.
		$already_dismiss_notices = ( $already_dismiss_notices = get_user_meta( $current_user->ID, '_give_hide_license_notices_permanently', true ) )
			? $already_dismiss_notices
			: array();

		// Get notice id.
		$notice_id = sanitize_text_field( $_GET['_give_hide_license_notices_permanently'] );

		if ( ! in_array( $notice_id, $already_dismiss_notices ) ) {
			$already_dismiss_notices[] = $notice_id;
		}

		// Store subscription ids.
		update_user_meta( $current_user->ID, '_give_hide_license_notices_permanently', $already_dismiss_notices );

		// Redirect user.
		wp_safe_redirect( remove_query_arg( '_give_hide_license_notices_permanently', $_SERVER['REQUEST_URI'] ) );
		exit();
	}

	// Hide subscription notices shortly.
	if ( ! empty( $_GET['_give_hide_license_notices_shortly'] ) ) {
		$current_user = wp_get_current_user();

		// Get notice id.
		$notice_id = sanitize_text_field( $_GET['_give_hide_license_notices_shortly'] );

		// Transient key name.
		$transient_key = "_give_hide_license_notices_shortly_{$current_user->ID}_{$notice_id}";

		if ( get_transient( $transient_key ) ) {
			return;
		}


		// Hide notice for 24 hours.
		set_transient( $transient_key, true, 24 * HOUR_IN_SECONDS );

		// Redirect user.
		wp_safe_redirect( remove_query_arg( '_give_hide_license_notices_shortly', $_SERVER['REQUEST_URI'] ) );
		exit();
	}
}

add_action( 'admin_init', 'give_hide_subscription_notices' );

/**
 * Load wp editor by ajax.
 *
 * @since 1.8
 */
function give_load_wp_editor() {
	if ( ! isset( $_POST['wp_editor'] ) ) {
		die();
	}

	$wp_editor                     = json_decode( base64_decode( $_POST['wp_editor'] ), true );
	$wp_editor[2]['textarea_name'] = $_POST['textarea_name'];

	wp_editor( $wp_editor[0], $_POST['wp_editor_id'], $wp_editor[2] );

	die();
}

add_action( 'wp_ajax_give_load_wp_editor', 'give_load_wp_editor' );


/**
 * Check saved core settings.
 * Note: some setting's value are depend upon other setting's value, To prevent any bug check them properly
 *  1. default_gateway: it's value will be auto set if not match to any active payment gateway value.
 *
 * @since 1.8
 *
 * @param $option_value
 * @param $option_name
 */
function give_check_saved_settings( $option_value, $option_name ) {
	// Set default payment gateway.
	if( ! in_array( $option_value['default_gateway'], $option_value['gateways'] ) ){
		$option_value['default_gateway'] = current( array_keys( $option_value['gateways'] ) );
		update_option( $option_name, $option_value );
	}
}
add_action( 'give_save_settings_give_settings', 'give_check_saved_settings', 10, 2 );
