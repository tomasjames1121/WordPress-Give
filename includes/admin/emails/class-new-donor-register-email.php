<?php
/**
 * New Donor Register Email
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

if ( ! class_exists( 'Give_New_Donor_Register_Email' ) ) :

	/**
	 * Give_New_Donor_Register_Email
	 *
	 * @abstract
	 * @since       1.9
	 */
	class Give_New_Donor_Register_Email extends Give_Email_Notification {

		/**
		 * Create a class instance.
		 *
		 * @param   mixed[] $objects
		 *
		 * @access  public
		 * @since   1.9
		 */
		public function __construct( $objects = array() ) {
			$this->id          = 'new-donor-register';
			$this->label       = __( 'New Donor Register', 'give' );
			$this->description = __( 'New Donor Register Notification will be sent to recipient(s) when new donor registered.', 'give' );

			$this->has_recipient_field = true;
			$this->notification_status = 'enabled';
			$this->has_preview_header  = true;
			$this->email_tag_context   = 'donor';

			parent::__construct();

			// Setup action hook.
			add_action(
				"give_{$this->get_id()}_email_notification",
				array( $this, 'setup_email_notification' ),
				10,
				2
			);

			add_filter( "give_email_preview_{$this->get_id()}_header", array( $this, 'email_preview_header' ) );
		}

		/**
		 * Get default email subject.
		 *
		 * @since  1.9
		 * @access public
		 * @return string
		 */
		function get_default_email_subject() {
			/**
			 * Filter the default email subject
			 *
			 * @since 1.9
			 */
			return apply_filters(
				"give_{$this->id}_get_default_email_subject",
				sprintf(
				/* translators: %s: site name */
					esc_attr__( 'New user registration on your site %s:', 'give' ),
					get_bloginfo( 'name' )
				),
				$this
			);
		}

		/**
		 * Get default email message.
		 *
		 * @since  1.9
		 * @access public
		 *
		 * @return string
		 */
		function get_default_email_message() {
			$message = esc_attr__( 'New user registration on your site {sitename}:', 'give' ) . "\r\n\r\n";
			$message .= esc_attr__( 'Username: {username}', 'give' ) . "\r\n\r\n";
			$message .= esc_attr__( 'E-mail: {user_email}', 'give' ) . "\r\n";

			/**
			 * Filter the default email message
			 *
			 * @since 1.9
			 */
			return apply_filters( "give_{$this->id}_get_default_email_message", $message, $this );
		}


		/**
		 * Send new donor register notifications.
		 *
		 * @since  1.9
		 * @access public
		 *
		 * @param int   $user_id   User ID.
		 * @param array $user_data User Information.
		 *
		 * @return string
		 */
		public function setup_email_notification( $user_id, $user_data ) {
			$this->recipient_email = $user_data['user_email'];
			$this->send_email_notification( array( 'user_id' => $user_id ) );
		}


		/**
		 * email preview header.
		 *
		 * @since  1.9
		 * @access public
		 *
		 * @param string $email_preview_header
		 *
		 * @return bool
		 */
		public function email_preview_header( $email_preview_header ) {
			//Payment receipt switcher
			$user_id = give_check_variable( give_clean( $_GET ), 'isset', 0, 'user_id' );

			//Get payments.
			$donors  = new Give_API();
			$donors  = give_check_variable( $donors->get_customers(), 'empty', array(), 'donors' );
			$options = array();

			// Default option.
			$options[0] = esc_html__( 'No donor(s) found.', 'give' );

			//Provide nice human readable options.
			if ( $donors ) {
				$options[0] = esc_html__( '- Select a donor -', 'give' );
				foreach ( $donors as $donor ) {
					// Exclude customers for which wp user not exist.
					if ( ! $donor['info']['user_id'] ) {
						continue;
					}
					$options[ $donor['info']['user_id'] ] = esc_html( '#' . $donor['info']['customer_id'] . ' - ' . $donor['info']['email'] );
				}
			}

			//Start constructing HTML output.
			$email_preview_header = '<div style="margin:0;padding:10px 0;width:100%;background-color:#FFF;border-bottom:1px solid #eee; text-align:center;">';

			//Inline JS function for switching donations.
			$request_url = $_SERVER['REQUEST_URI'];

			// Remove payment id query param if set from request url.
			if ( $user_id ) {
				$request_url_data = wp_parse_url( $_SERVER['REQUEST_URI'] );
				$query            = $request_url_data['query'];
				$query            = str_replace( "&user_id={$user_id}", '', $query );

				$request_url = home_url( '/?' . str_replace( '', '', $query ) );
			}


			$email_preview_header .= '<script>
				 function change_preview(){
				  var transactions = document.getElementById("give_preview_email_user_id");
			        var selected_trans = transactions.options[transactions.selectedIndex];
				        if (selected_trans){
				            var url_string = "' . $request_url . '&user_id=" + selected_trans.value;
				                window.location = url_string;
				        }
				    }
			    </script>';

			$email_preview_header .= '<label for="give_preview_email_user_id" style="font-size:12px;color:#333;margin:0 4px 0 0;">' . esc_html__( 'Preview email with a donor:', 'give' ) . '</label>';

			//The select field with 100 latest transactions
			$email_preview_header .= Give()->html->select( array(
				'name'             => 'preview_email_user_id',
				'selected'         => $user_id,
				'id'               => 'give_preview_email_user_id',
				'class'            => 'give-preview-email-donor-id',
				'options'          => $options,
				'chosen'           => false,
				'select_atts'      => 'onchange="change_preview()"',
				'show_option_all'  => false,
				'show_option_none' => false,
			) );

			//Closing tag
			$email_preview_header .= '</div>';

			echo $email_preview_header;
		}
	}

endif; // End class_exists check

return new Give_New_Donor_Register_Email();
