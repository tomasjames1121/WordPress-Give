<?php
/**
 * Offline Donation Instruction Email
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

if ( ! class_exists( 'Give_Offline_Donation_Instruction_Email' ) ) :

	/**
	 * Give_Offline_Donation_Instruction_Email
	 *
	 * @abstract
	 * @since       1.9
	 */
	class Give_Offline_Donation_Instruction_Email extends Give_Email_Notification {
		/* @var Give_Payment $payment */
		public $payment;

		/**
		 * Create a class instance.
		 *
		 * @param   mixed[] $objects
		 *
		 * @access  public
		 * @since   1.9
		 */
		public function __construct( $objects = array() ) {
			$this->id          = 'offline-donation-instruction';
			$this->label       = __( 'Offline Donation Instruction', 'give' );
			$this->description = __( 'Offline Donation Instruction will be sent to recipient(s) when offline donation received.', 'give' );

			$this->notification_status       = 'enabled';
			$this->recipient_group_name      = __( 'Donor', 'give' );
			$this->preview_email_tags_values = array(
				'payment_method' => esc_html__( 'Offline', 'give' ),
			);

			// Initialize empty payment.
			$this->payment = new Give_Payment( 0 );

			parent::__construct();

			add_action( 'give_insert_payment', array( $this, 'setup_email_notification' ) );
		}


		/**
		 * Get email message
		 *
		 * @since 1.9
		 * @return string
		 */
		public function get_email_message() {
			$post_offline_customization_option = get_post_meta(
				$this->payment->form_id,
				'_give_customize_offline_donations',
				true
			);

			//Customize email content depending on whether the single form has been customized
			$message = wp_strip_all_tags(
				give_get_option( "{$this->id}_email_message",
					$this->get_default_email_message()
				)
			);


			if ( give_is_setting_enabled( $post_offline_customization_option, 'enabled' ) ) {
				$message = get_post_meta( $this->payment->form_id, '_give_offline_donation_email', true );
			}

			/**
			 * Filter the email message.
			 *
			 * @since 1.9
			 */
			$message = apply_filters( "give_{$this->id}_get_email_message", $message, $this );

			return $message;
		}

		/**
		 * Get email message
		 *
		 * @since 1.9
		 * @return string
		 */
		public function get_email_subject() {
			$post_offline_customization_option = get_post_meta(
				$this->payment->form_id,
				'_give_customize_offline_donations',
				true
			);

			$subject = give_get_option(
				"{$this->id}_email_subject",
				$this->get_default_email_subject()
			);

			if ( give_is_setting_enabled( $post_offline_customization_option, 'enabled' ) ) {
				$subject = get_post_meta( $this->payment->form_id, '_give_offline_donation_subject', true );
			}

			/**
			 * Filter the email subject.
			 *
			 * @since 1.9
			 */
			$subject = apply_filters( "give_{$this->id}_get_email_subject", $subject, $this );

			return $subject;
		}

		/**
		 * Get attachments.
		 *
		 * @since 1.9
		 * @return array
		 */
		public function get_email_attachments() {
			/**
			 * Filter the attachments.
			 * Note: This filter will deprecate soon.
			 *
			 * @since 1.0
			 */
			$attachment = apply_filters(
				'give_offline_donation_attachments',
				array(),
				$this->payment->ID,
				$this->payment->payment_meta
			);

			/**
			 * Filter the email attachment.
			 *
			 * @since 1.9
			 */
			$attachment = apply_filters( "give_{$this->id}_get_email_attachment", $attachment, $this );

			return $attachment;
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
			return apply_filters( "give_{$this->id}_get_default_email_subject", esc_attr__( '{donation} - Offline Donation Instructions', 'give' ), $this );
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
			$message = give_get_default_offline_donation_email_content();

			/**
			 * Filter the email message
			 *
			 * @since 1.9
			 *
			 * @param string $message
			 */
			return apply_filters( "give_{$this->id}_get_default_email_message", $message, $this );
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
			 * @since 1.7
			 */
			$from_name = apply_filters( 'give_donation_from_name', Give()->emails->get_from_name(), $this->payment->ID, $this->payment->payment_meta );

			/**
			 * Filters the from email.
			 *
			 * @since 1.7
			 */
			$from_email = apply_filters( 'give_donation_from_address', Give()->emails->get_from_address(), $this->payment->ID, $this->payment->payment_meta );


			Give()->emails->__set( 'from_name', $from_name );
			Give()->emails->__set( 'from_email', $from_email );
			Give()->emails->__set( 'heading', __( 'Offline Donation Instructions', 'give' ) );
			Give()->emails->__set( 'headers', apply_filters( 'give_receipt_headers', Give()->emails->get_headers(), $this->payment->ID, $this->payment->payment_meta ) );

		}

		/**
		 * Setup email notification.
		 *
		 * @since  1.9
		 * @access public
		 *
		 * @param int $payment_id
		 */
		public function setup_email_notification( $payment_id ) {
			$this->payment = new Give_Payment( $payment_id );

			// Exit if not donation was not with offline donation.
			if ( 'offline' !== $this->payment->gateway ) {
				return;
			}

			// Set email data.
			$this->setup_email_data();

			// Send email.
			$this->send_email_notification( array( 'payment_id' => $this->payment->ID ) );
		}
	}

endif; // End class_exists check

return new Give_Offline_Donation_Instruction_Email();
