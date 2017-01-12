<?php
/**
 * Donation Receipt Email
 *
 * This class handles all email notification settings.
 *
 * @package     Give
 * @subpackage  Classes/Emails
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.9
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
	 * @since       1.9
	 */
	class Give_Donation_Receipt_Email extends Give_Email_Notification {
		/* @var Give_Payment $payment*/
		public $payment;

		/**
		 * Create a class instance.
		 *
		 * @access  public
		 * @since   1.9
		 */
		public function init() {
			$this->id          = 'donation-receipt';
			$this->label       = __( 'Donation Receipt', 'give' );
			$this->description = __( 'Donation Receipt Notification will be sent to donor when new donation received.', 'give' );

			$this->notification_status  = 'enabled';
			$this->recipient_group_name = __( 'Donor', 'give' );
			$this->form_metabox_setting = true;

			// Initialize empty payment.
			$this->payment = new Give_Payment(0);

			$this->load();

			add_action( "give_{$this->id}_email_notification", array( $this, 'send_donation_receipt' ) );
			add_action( 'give_email_links', array( $this, 'resend_donation_receipt' ) );
		}

		/**
		 * Get default email subject.
		 *
		 * @since  1.9
		 * @access public
		 * @return string
		 */
		public function get_default_email_subject() {
			/**
			 * Filter the default subject.
			 *
			 * @since 1.9
			 */
			return apply_filters( "give_{$this->id}_get_default_email_subject", esc_attr__( 'Donation Receipt', 'give' ), $this );
		}


		/**
		 * Get default email message.
		 *
		 * @since  1.9
		 * @access public
		 *
		 * @return string
		 */
		public function get_default_email_message() {
			/**
			 * Filter the donation receipt email message
			 *
			 * @since 1.9
			 *
			 * @param string $message
			 */
			return apply_filters( "give_{$this->id}_get_default_email_message", give_get_default_donation_receipt_email(), $this );
		}


		/**
		 * Get email subject.
		 *
		 * @since 1.9
		 * @access public
		 * @return string
		 */
		public function get_email_subject() {
			$subject = wp_strip_all_tags( give_get_option( "{$this->id}_email_subject", $this->get_default_email_subject() ) );

			/**
			 * Filters the donation email receipt subject.
			 * Note: This filter will deprecate soon.
			 *
			 * @since 1.0
			 */
			$subject = apply_filters( 'give_donation_subject', $subject, $this->payment->ID );

			/**
			 * Filters the donation email receipt subject.
			 *
			 * @since 1.9
			 */
			$subject = apply_filters( "give_{$this->id}_get_email_subject", $subject, $this );

			return $subject;
		}


		/**
		 * Get email message.
		 *
		 * @since  1.9
		 * @access public
		 * @return string
		 */
		public function get_email_message() {
			$message = give_get_option( "{$this->id}_email_message", $this->get_default_email_message() );

			/**
			 * Filter message on basis of email template
			 * Note: This filter will deprecate soon.
			 *
			 * @since 1.0
			 */
			$message = apply_filters( 'give_donation_receipt_' . Give()->emails->get_template(), $message, $this->payment->ID, $this->payment->payment_meta );

			/**
			 * Filter the message
			 * Note: This filter will deprecate soon.
			 *
			 * @since 1.0
			 */
			$message = apply_filters( 'give_donation_receipt', $message, $this->payment->ID, $this->payment->payment_meta );

			/**
			 * Filter the message
			 *
			 * @since 1.9
			 */
			$message = apply_filters( "give_{$this->id}_get_email_message", $message, $this );
			return $message;
		}

		/**
		 * Get the recipient attachments.
		 *
		 * @since  1.9
		 * @access public
		 * @return array
		 */
		public function get_email_attachments() {
			/**
			 * Filter the attachments.
			 * Note: this filter will deprecate soon.
			 *
			 * @since 1.0
			 */
			$attachments = apply_filters( 'give_receipt_attachments', array(), $this->payment->ID, $this->payment->payment_meta );

			/**
			 * Filter the attachments.
			 *
			 * @since 1.9
			 */
			$attachments = apply_filters( "give_{$this->id}_get_email_attachments", $attachments, $this );

			return $attachments;
		}


		/**
		 * Set email data.
		 *
		 * @since 1.9
		 */
		public function setup_email_data() {
			// Set recipient email.
			$this->recipient_email = $this->payment->email;

			/**
			 * Filters the from name.
			 *
			 * @param int $payment_id Payment id.
			 * @param mixed $payment_data Payment meta data.
			 *
			 * @since 1.0
			 */
			$from_name = apply_filters( 'give_donation_from_name', Give()->emails->get_from_name(), $this->payment->ID, $this->payment->payment_meta );

			/**
			 * Filters the from email.
			 *
			 * @param int $payment_id Payment id.
			 * @param mixed $payment_data Payment meta data.
			 *
			 * @since 1.0
			 */
			$from_email = apply_filters( 'give_donation_from_address', Give()->emails->get_from_address(), $this->payment->ID, $this->payment->payment_meta );

			Give()->emails->__set( 'from_name', $from_name );
			Give()->emails->__set( 'from_email', $from_email );
			Give()->emails->__set( 'heading', esc_html__( 'Donation Receipt', 'give' ) );

			/**
			 * Filters the donation receipt's email headers.
			 *
			 * @param int $payment_id Payment id.
			 * @param mixed $payment_data Payment meta data.
			 *
			 * @since 1.0
			 */
			$headers = apply_filters( 'give_receipt_headers', Give()->emails->get_headers(), $this->payment->ID, $this->payment->payment_meta );

			Give()->emails->__set( 'headers', $headers );
		}

		/**
		 * Send donation receipt
		 * @since  1.9
		 * @access public
		 *
		 * @param $payment_id
		 */
		public function send_donation_receipt( $payment_id ) {
			$this->payment = new Give_Payment( $payment_id );

			// Setup email data.
			$this->setup_email_data();

			// Send email.
			$this->send_email_notification( array( 'payment_id' => $this->payment->ID ) );
		}

		/**
		 * Resend payment receipt by row action.
		 *
		 * @since  1.9
		 * @access public
		 *
		 * @param array $data
		 */
		public function resend_donation_receipt( $data ) {
			$purchase_id = absint( $data['purchase_id'] );

			if ( empty( $purchase_id ) ) {
				return;
			}

			// Get donation payment information.
			$this->payment = new Give_Payment( $purchase_id );

			if ( ! current_user_can( 'edit_give_payments', $this->payment->ID ) ) {
				wp_die( esc_html__( 'You do not have permission to edit payments.', 'give' ), esc_html__( 'Error', 'give' ), array( 'response' => 403 ) );
			}

			// Setup email data.
			$this->setup_email_data();

			// Send email.
			$this->send_email_notification( array( 'payment_id' => $this->payment->ID ) );

			wp_redirect( add_query_arg( array(
				'give-message' => 'email_sent',
				'give-action'  => false,
				'purchase_id'  => false,
			) ) );
			exit;
		}
	}

endif; // End class_exists check

return Give_Donation_Receipt_Email::get_instance();