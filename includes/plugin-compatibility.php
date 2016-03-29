<?php
/**
 * Plugin Compatibility
 *
 * Functions for compatibility with other plugins.
 *
 * @package     Give
 * @subpackage  Functions/Compatibility
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


/**
 * Disables the mandrill_nl2br filter while sending Give emails
 *
 * @since 1.4
 * @return void
 */
function give_disable_mandrill_nl2br() {
	add_filter( 'mandrill_nl2br', '__return_false' );
}
add_action( 'give_email_send_before', 'give_disable_mandrill_nl2br');
