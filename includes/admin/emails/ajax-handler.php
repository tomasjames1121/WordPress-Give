<?php
/**
 * This file contain code to handle email notification setting ajax.
 *
 * Register settings Include and setup custom metaboxes and fields.
 *
 * @package    Give
 * @subpackage Classes/Emails
 * @license    https://opensource.org/licenses/gpl-license GNU Public License
 * @link       https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

/**
 * Enabled & disable notification
 *
 * @since 1.8
 */
function give_set_notification_status_handler(){
	if( give_update_option( give_clean( $_POST['notification_id'] ) . '_notification', give_clean( $_POST['status'] ) ) ) {
		wp_send_json_success();
	}

	wp_send_json_error();
}
add_action( 'wp_ajax_give_set_notification_status', 'give_set_notification_status_handler' );