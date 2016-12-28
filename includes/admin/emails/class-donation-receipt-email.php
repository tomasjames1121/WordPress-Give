<?php
/**
 * New Donation Email
 *
 * This class handles all email notification settings.
 *
 * @package     Give
 * @subpackage  Classes/Emails
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.8
 */

// Exit if access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Give_Donation_Receipt_Email' ) ) :

	/**
	 * Give_Donation_Receipt_Email
	 *
	 * @abstract
	 * @since       1.8
	 */
	class Give_Donation_Receipt_Email extends Give_Email_Notification {

		/**
		 * Create a class instance.
		 *
		 * @param   mixed[] $objects
		 *
		 * @access  public
		 * @since   1.8
		 */
		public function __construct( $objects = array() ) {
			$this->id          = 'donation-receipt';
			$this->label       = __( 'Donation Receipt', 'give' );
			$this->description = __( 'Donation Receipt Notification will be sent to donor when new donation received.', 'give' );

			$this->notification_status  = 'enabled';
			$this->recipient_group_name = __( 'Donor', 'give' );

			parent::__construct();
		}

		/**
		 * Get default email subject.
		 *
		 * @since  1.8
		 * @access public
		 * @return string
		 */
		public function get_default_email_subject() {
			return esc_attr__( 'Donation Receipt', 'give' );
		}


		/**
		 * Get default email message.
		 *
		 * @since  1.8
		 * @access public
		 *
		 * @param array $args Email Arguments.
		 *
		 * @return string
		 */
		public function get_default_email_message( $args = array() ) {
			$message = esc_html__( 'Dear', 'give' ) . " {name},\n\n";
			$message .= esc_html__( 'Thank you for your donation. Your generosity is appreciated! Here are the details of your donation:', 'give' ) . "\n\n";
			$message .= '<strong>' . esc_html__( 'Donor:', 'give' ) . '</strong> {fullname}' . "\n";
			$message .= '<strong>' . esc_html__( 'Donation:', 'give' ) . '</strong> {donation}' . "\n";
			$message .= '<strong>' . esc_html__( 'Donation Date:', 'give' ) . '</strong> {date}' . "\n";
			$message .= '<strong>' . esc_html__( 'Amount:', 'give' ) . '</strong> {amount}' . "\n";
			$message .= '<strong>' . esc_html__( 'Payment Method:', 'give' ) . '</strong> {payment_method}' . "\n";
			$message .= '<strong>' . esc_html__( 'Payment ID:', 'give' ) . '</strong> {payment_id}' . "\n";
			$message .= '<strong>' . esc_html__( 'Receipt ID:', 'give' ) . '</strong> {receipt_id}' . "\n\n";
			$message .= '{receipt_link}' . "\n\n";
			$message .= "\n\n";
			$message .= esc_html__( 'Sincerely,', 'give' ) . "\n";
			$message .= '{sitename}' . "\n";


			/**
			 * Filter the donation receipt email message
			 *
			 * @since 1.8
			 *
			 * @param string $message
			 */
			return apply_filters( 'give_default_donation_receipt_email', $message );
		}


		/**
		 * Send preview email.
		 *
		 * @since  1.8
		 * @access public
		 */
		public function send_preview_email() {
			$subject = $this->get_email_subject();
			$subject = give_do_email_tags( $subject, 0 );

			$attachments = apply_filters( 'give_receipt_attachments', array(), 0, array() );

			$message = give_email_preview_template_tags( give_get_email_body_content( 0, array() ) );

			$this->email->__set( 'heading', esc_html__( 'Donation Receipt', 'give' ) );

			$this->email->send( give_get_admin_notice_emails(), $subject, $message, $attachments );
		}
	}

endif; // End class_exists check

return new Give_Donation_Receipt_Email();