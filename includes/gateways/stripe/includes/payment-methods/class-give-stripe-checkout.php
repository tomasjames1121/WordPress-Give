<?php
/**
 * Give - Stripe Checkout
 *
 * @package    Give
 * @subpackage Stripe Core
 * @copyright  Copyright (c) 2019, GiveWP
 * @license    https://opensource.org/licenses/gpl-license GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Give\Helpers\Form\Utils as FormUtils;
use Give\Helpers\Gateways\Stripe;

/**
 * Check for Give_Stripe_Checkout existence.
 *
 * @since 2.5.5
 */
if ( ! class_exists( 'Give_Stripe_Checkout' ) ) {

	/**
	 * Class Give_Stripe_Checkout.
	 *
	 * @since 2.5.5
	 */
	class Give_Stripe_Checkout extends Give_Stripe_Gateway {

		/**
		 * Checkout Session of Stripe.
		 *
		 * @since  2.5.5
		 * @access public
		 *
		 * @var $stripe_checkout_session
		 */
		public $stripe_checkout_session;

		/**
		 * Give_Stripe_Checkout constructor.
		 *
		 * @since  2.5.5
		 * @access public
		 */
		public function __construct() {

			$this->id = 'stripe_checkout';

			parent::__construct();

			// Create object for Stripe Checkout Session for usage.
			$this->stripe_checkout_session = new Give_Stripe_Checkout_Session();

			// Remove CC fieldset.
			add_action( 'give_stripe_checkout_cc_form', [ $this, 'output_redirect_notice' ], 10, 2 );

			// Load the `redirect_to_checkout` function only when `redirect` is set as checkout type.
			if ( 'redirect' === give_stripe_get_checkout_type() ) {
				add_action( 'wp_footer', [ $this, 'redirect_to_checkout' ], 99999 );
				add_action( 'give_embed_footer', [ $this, 'redirect_to_checkout' ], 99999 );
			} else {
				add_action( 'give_stripe_checkout_cc_form', [ $this, 'showCheckoutModal' ], 10, 2 );
				//              add_action( 'wp_ajax_load_checkout_fields', [ $this, 'loadCheckoutFields' ] );
				//              add_action( 'wp_ajax_nopriv_load_checkout_fields', [ $this, 'loadCheckoutFields' ] );
				//                            add_action( 'give_donation_form_bottom', [ $this, 'showCheckoutModal' ], 10, 2 );
				//              remove_action( 'give_donation_form_after_cc_form', 'give_checkout_submit', 9999 );
				//              add_action( 'give_donation_form_after_cc_form', [ $this, 'checkoutSubmit' ], 9999, 2 );
			}

		}

		public function loadCheckoutFields() {
			$idPrefix = ! empty( $_POST['idPrefix'] ) ? give_clean( $_POST['idPrefix'] ) : '-1';

			wp_send_json_success(
				[
					'html' => Stripe::showCreditCardFields( $idPrefix ),
				]
			);
		}

		/**
		 * Render redirection notice.
		 *
		 * @param int   $formId Donation Form ID.
		 * @param array $args   List of arguments.
		 *
		 * @return bool
		 * @since 2.7.0
		 */
		public function output_redirect_notice( $formId, $args ) {
			if ( FormUtils::isLegacyForm( $formId ) ) {
				// For Legacy Form Template.
				return Stripe::canShowBillingAddress( $formId, $args );
			}

			// For Multi-step Sequoia Form Template.
			printf(
				'
					<fieldset class="no-fields">
						<div style="display: flex; justify-content: center; margin-top: 20px;">
						<svg width="173" height="73" viewBox="0 0 173 73" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
							<rect width="173" height="72.66" fill="url(#pattern0)"/>
							<defs>
								<pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
									<use xlink:href="#image0" transform="scale(0.00125 0.00297619)"/>
								</pattern>
								<image id="image0" width="800" height="336" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAyAAAAFQCAYAAABHzRqAAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABmJLR0QA/wD/AP+gvaeTAAAAB3RJTUUH4gIKBDEYUB2UFQAATPpJREFUeNrt3Xt4VNW5P/Dvu2aScL/KJTOJgIh3rYrXYu1NW6tWbWustupRhJmApTATQGt7esZzPFYEMlCOkAyK99MqP1uvtR61asU71GrFVhFQkpkEkFu4JjN7vb8/EixaxFwnM3t/P89Dn7bPZGb2u9Zee31n7702QERERERERERERERE5DbCEhARUb6YdP1HAzVdcIpaezJEjqiuDF7OqhAR5Rc/S0BERLkoFFpeoL2Dhxmj49TaM0RkrG3CEYAaiADAZlaJiIgBhIiIqF0mRdYfYiV9hkDGKjAWwFiB7QEFRHjCnoiIAYSIiKidxs9cFyhI+8aKYG/Y+LJFZhAgUJaHiIgBhIiIqP1hY2Nfv5P+kgBj1dqxInIGMhgFAcMGEREDCBERUfvFYupPbqs/3IgzFopxEDkDmaYjABiAl1IREREDCBERdcD4mesC/owZZyBnKDC2riF1ohH0BITrLBIREQMIERG13+QZG4Y7mfTJ+9y3cRoyOAjgpVRERMQAQkREHQkbkzf0yfTMHL/PfRtjHSd9FO/bICIiBhAiIuqQfe/baFkCd5yD9PGi8AG8b4OIiBhAiIioA/YugQvBOABn1DWkTjCCXlwCl4iIGECIiKhDronUDPL55HRROQWKUyA4GRkM5g3iRETEAEJERJ0/yBtzPSxmfPJ/8BQHERF1M8MSEBERERERAwgRERERETGAEBERERERMYAQEREREREDCBEREREREQMIERERERExgBAREREREQMIERERERERAwgRERERETGAEBERERERMYAQEREREREDCBERERERMYAQ0ecZP3NjX1aBiIiIqG38LAHRFwtdt7o/Mj2PFdVxAM4Q4FRk0lUAfsnqEBERETGAELU/bISWF2jv4GHG6Di19gwRGYs0jgRU9r5GAUBUWS0iIiIiBhCiNpkUWX+IlfQZAhmrwFgAJwlsERQQkc/9O1EwgBARERExgBB9vskzNgx3MumTRdAcNgSnW80MBqTNacKCAYSIiIiIAYSo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Z63Pl5G03y/mn8FEnDScwh3GOI4tSDc4mrH8JYU+z5Qpq4oyvQt7obHHQPVpL1FbBACOA/WJbysAONbJ+Hqmt+/IFDVxbXkiBpDupkAqURkMspWJqLt54aZvtFwDuveXEv5iQh3WssJWI/sTEeWLtizDS0TUlQxLQERE5AHKAEJEDCBERESULcIAQkQMIERERJQ9DCBExABCREREREQMIEREROQyyjMgRMQAQkRERNnCVbCIiAGEiIiIsokBhIgYQIiIiChr+YMBhIgYQIiIiChrh3wGECJiACEiIqLsUJ4BISIGECIiIsoW4ZPQiYgBhIiIiLKXQBhAiIgBhIiIiIiIGECIiIjIdXgGhIgYQIiIiChreA8IETGAEBERUfbyBwMIETGAEBERUZYIAwgRMYAQERFRFjGAEBEDCBEREWUtfzCAEBEDCBEREWXtkM8AQkQMIERERJQdqjwDQkQMIERERJQlwueAEBEDCBERERERMYAQERGR+/AMCBExgBAREVHW8EnoRMQAQkRERFmNIEREDCBERETEAEJEDCBERETEAEJExABCRERE7SF8EjoRMYAQERFRtiifhE5EDCBERESUxQTCAEJEDCBERESUJcJLsIiIAYSIiIiIiBhAiIiIyHX4JHQiYgAhIiKirOUP3gNCRAwgRERElC3K54AQEQMIERERZTeDEBExgBAREREDCBExgBAREREDCBERAwgRERG1g4DPASEiBhAiIiLKElXDAEJEDCBERERERMQAQkRERG7DBxESEQMIERERZY3yHhAiYgAhIiKibOEZECJiACEiIqKs5Q8GECJiACEiIqIsScLq6ywDEeUCP0tARETkKjsAvCXACoWuMFqwbFF82BqWhYgYQIiIiKijMgDeV9UVYswKAywb1rf4zVhMLEtDRAwgRERE1EFSB+gKAMtU5CVsx18SicAu1oWIGECIiIioo2FjG6DvAFgGxUvi871WNWf4BtaFiBhAiIiIqKNhIw3o2wK8pNAVVn0rFseHvwsIV64iIgYQIiIi6ihdo4qXxJgVCqwoatr5xoIFYxpZFyJiACEiIqIOar5vQ4AVqliRgX3pjnjpZtaFiBhAiIiIqKM+tQSuY/wv3j5n+FqWhYiIAYSIiKijuAQuEREDCBERUVf59BK4va2zIh4v3c26EBExgBAREXU0bHxqCdymjHn1zgXFG1kXIiIGECIiok7jc/TedIHvDt63QURERERERERERERERERErff/ATy4f5RFM28cAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDE4LTAyLTEwVDA0OjQ5OjI0KzAwOjAwCjelzQAAACV0RVh0ZGF0ZTptb2RpZnkAMjAxOC0wMi0xMFQwNDo0OToyNCswMDowMHtqHXEAAAAASUVORK5CYII="/>
							</defs>
						</svg>
						</div>
						<p style="text-align: center;"><b>%1$s</b></p>
						<p style="text-align: center;">
							<b>%2$s</b> %3$s
						</p>
					</fieldset>
					',
				esc_html__( 'Make your donations quickly and securely with Stripe', 'give' ),
				esc_html__( 'How it works:', 'give' ),
				esc_html__( 'A Stripe window will open after you click the Donate Now button where you can securely make your donation. You will then be brought back to this page to view your receipt.', 'give' ),
			);

			return Stripe::canShowBillingAddress( $formId, $args );
		}

		/**
		 * This function will be used for donation processing.
		 *
		 * @param array $donation_data List of donation data.
		 *
		 * @since  2.5.5
		 * @access public
		 *
		 * @return void
		 */
		public function process_payment( $donation_data ) {
			//echo "<pre>"; print_r($donation_data); echo "</pre>"; die();
			// Bailout, if the current gateway and the posted gateway mismatched.
			if ( $this->id !== $donation_data['post_data']['give-gateway'] ) {
				return;
			}

			// Make sure we don't have any left over errors present.
			give_clear_errors();

			// Any errors?
			$errors = give_get_errors();

			// No errors, proceed.
			if ( ! $errors ) {

				$form_id          = ! empty( $donation_data['post_data']['give-form-id'] ) ? intval( $donation_data['post_data']['give-form-id'] ) : 0;
				$price_id         = ! empty( $donation_data['post_data']['give-price-id'] ) ? $donation_data['post_data']['give-price-id'] : 0;
				$donor_email      = ! empty( $donation_data['post_data']['give_email'] ) ? $donation_data['post_data']['give_email'] : 0;
				$payment_method   = ! empty( $donation_data['post_data']['give_stripe_payment_method'] ) ? $donation_data['post_data']['give_stripe_payment_method'] : 0;
				$donation_summary = give_payment_gateway_donation_summary( $donation_data, false );

				// Get an existing Stripe customer or create a new Stripe Customer and attach the source to customer.
				$give_stripe_customer = new Give_Stripe_Customer( $donor_email, $payment_method );
				$stripe_customer_id   = $give_stripe_customer->get_id();
				$payment_method       = ! empty( $give_stripe_customer->attached_payment_method ) ?
					$give_stripe_customer->attached_payment_method->id :
					$payment_method;

				// We have a Stripe customer, charge them.
				if ( $stripe_customer_id ) {

					// Setup the payment details.
					$payment_data = [
						'price'           => $donation_data['price'],
						'give_form_title' => $donation_data['post_data']['give-form-title'],
						'give_form_id'    => $form_id,
						'give_price_id'   => $price_id,
						'date'            => $donation_data['date'],
						'user_email'      => $donation_data['user_email'],
						'purchase_key'    => $donation_data['purchase_key'],
						'currency'        => give_get_currency( $form_id ),
						'user_info'       => $donation_data['user_info'],
						'status'          => 'pending',
						'gateway'         => $this->id,
					];

					// Record the pending payment in Give.
					$donation_id = give_insert_payment( $payment_data );

					// Return error, if donation id doesn't exists.
					if ( ! $donation_id ) {
						give_record_gateway_error(
							__( 'Donation creating error', 'give' ),
							sprintf(
								/* translators: %s Donation Data */
								__( 'Unable to create a pending donation. Details: %s', 'give' ),
								wp_json_encode( $donation_data )
							)
						);
						give_set_error( 'stripe_error', __( 'The Stripe Gateway returned an error while creating a pending donation.', 'give' ) );
						give_send_back_to_checkout( '?payment-mode=' . give_clean( $_GET['payment-mode'] ) );
						return;
					}

					// Assign required data to array of donation data for future reference.
					$donation_data['donation_id'] = $donation_id;
					$donation_data['description'] = $donation_summary;
					$donation_data['customer_id'] = $stripe_customer_id;
					$donation_data['source_id']   = $payment_method;

					// Save Stripe Customer ID to Donation note, Donor and Donation for future reference.
					give_insert_payment_note( $donation_id, 'Stripe Customer ID: ' . $stripe_customer_id );
					$this->save_stripe_customer_id( $stripe_customer_id, $donation_id );
					give_update_meta( $donation_id, '_give_stripe_customer_id', $stripe_customer_id );

					if ( 'modal' === give_stripe_get_checkout_type() ) {
						$this->processModalCheckout( $donation_id, $donation_data );
						//                      $this->process_legacy_checkout( $donation_id, $donation_data );
					} elseif ( 'redirect' === give_stripe_get_checkout_type() ) {
						$this->process_checkout( $donation_id, $donation_data );
					} else {
						give_record_gateway_error(
							__( 'Invalid Checkout Error', 'give' ),
							sprintf(
								/* translators: %s Donation Data */
								__( 'Invalid Checkout type passed to process the donation. Details: %s', 'give' ),
								wp_json_encode( $donation_data )
							)
						);
						give_set_error( 'stripe_error', __( 'The Stripe Gateway returned an error while processing the donation.', 'give' ) );
						give_send_back_to_checkout( '?payment-mode=' . give_clean( $_GET['payment-mode'] ) );
						return;
					}

					// Don't execute code further.
					give_die();
				}
			}

		}

		public function processModalCheckout( $donation_id, $donation_data ) {
			$form_id = ! empty( $donation_data['post_data']['give-form-id'] ) ? intval( $donation_data['post_data']['give-form-id'] ) : 0;

			/**
			 * This filter hook is used to update the payment intent arguments.
			 *
			 * @since 2.5.0
			 */
			$intent_args = apply_filters(
				'give_stripe_create_intent_args',
				[
					'amount'               => $this->format_amount( $donation_data['price'] ),
					'currency'             => give_get_currency( $form_id ),
					'payment_method_types' => [ 'card' ],
					'statement_descriptor' => give_stripe_get_statement_descriptor(),
					'description'          => give_payment_gateway_donation_summary( $donation_data ),
					'metadata'             => $this->prepare_metadata( $donation_id, $donation_data ),
					'customer'             => $donation_data['customer_id'],
					'payment_method'       => $donation_data['source_id'],
					'confirm'              => true,
					'return_url'           => give_get_success_page_uri(),
				]
			);

			// Send Stripe Receipt emails when enabled.
			if ( give_is_setting_enabled( give_get_option( 'stripe_receipt_emails' ) ) ) {
				$intent_args['receipt_email'] = $donation_data['user_email'];
			}

			$intent = $this->payment_intent->create( $intent_args );

			// Save Payment Intent Client Secret to donation note and DB.
			give_insert_payment_note( $donation_id, 'Stripe Payment Intent Client Secret: ' . $intent->client_secret );
			give_update_meta( $donation_id, '_give_stripe_payment_intent_client_secret', $intent->client_secret );

			// Set Payment Intent ID as transaction ID for the donation.
			give_set_payment_transaction_id( $donation_id, $intent->id );
			give_insert_payment_note( $donation_id, 'Stripe Charge/Payment Intent ID: ' . $intent->id );

			// Process additional steps for SCA or 3D secure.
			give_stripe_process_additional_authentication( $donation_id, $intent );

			if ( ! empty( $intent->status ) && 'succeeded' === $intent->status ) {
				// Process to success page, only if intent is successful.
				give_send_to_success_page();
			} else {
				// Show error message instead of confirmation page.
				give_send_back_to_checkout( '?payment-mode=' . give_clean( $_GET['payment-mode'] ) );
			}
		}

		/**
		 * This function is used to process donations via legacy Stripe Checkout which will be deprecated soon.
		 *
		 * @param int   $donation_id   Donation ID.
		 * @param array $donation_data List of submitted data for donation processing.
		 *
		 * @since  2.5.5
		 * @access public
		 *
		 * @return void
		 */
		public function process_legacy_checkout( $donation_id, $donation_data ) {

			$stripe_customer_id = ! empty( $donation_data['customer_id'] ) ? $donation_data['customer_id'] : '';

			// Process charge w/ support for preapproval.
			$charge = $this->process_charge( $donation_data, $stripe_customer_id );

			// Verify the Stripe payment.
			$this->verify_payment( $donation_id, $stripe_customer_id, $charge );

		}

		/**
		 * Process One Time Charge.
		 *
		 * @param array  $donation_data      List of donation data.
		 * @param string $stripe_customer_id Customer ID.
		 *
		 * @return bool|\Stripe\Charge
		 */
		public function process_charge( $donation_data, $stripe_customer_id ) {

			$form_id     = ! empty( $donation_data['post_data']['give-form-id'] ) ? intval( $donation_data['post_data']['give-form-id'] ) : 0;
			$donation_id = ! empty( $donation_data['donation_id'] ) ? intval( $donation_data['donation_id'] ) : 0;
			$description = ! empty( $donation_data['description'] ) ? $donation_data['description'] : false;

			// Format the donation amount as required by Stripe.
			$amount = $this->format_amount( $donation_data['price'] );

			// Prepare charge arguments.
			$charge_args = [
				'amount'               => $amount,
				'customer'             => $stripe_customer_id,
				'currency'             => give_get_currency( $form_id ),
				'description'          => html_entity_decode( $description, ENT_COMPAT, 'UTF-8' ),
				'statement_descriptor' => give_stripe_get_statement_descriptor( $donation_data ),
				'metadata'             => $this->prepare_metadata( $donation_id, $donation_data ),
			];

			// Process the charge.
			$charge = $this->create_charge( $donation_id, $charge_args );

			// Return charge if set.
			if ( isset( $charge ) ) {
				return $charge;
			} else {
				return false;
			}
		}

		/**
		 * This function is used to process donations via Stripe Checkout 2.0.
		 *
		 * @param int   $donation_id Donation ID.
		 * @param array $data        List of submitted data for donation processing.
		 *
		 * @since  2.5.5
		 * @access public
		 *
		 * @return void
		 */
		public function process_checkout( $donation_id, $data ) {

			// Define essential variables.
			$form_id          = ! empty( $data['post_data']['give-form-id'] ) ? intval( $data['post_data']['give-form-id'] ) : 0;
			$form_name        = ! empty( $data['post_data']['give-form-title'] ) ? $data['post_data']['give-form-title'] : false;
			$donation_summary = ! empty( $data['description'] ) ? $data['description'] : '';
			$donation_id      = ! empty( $data['donation_id'] ) ? intval( $data['donation_id'] ) : 0;
			$redirect_to_url  = ! empty( $data['post_data']['give-current-url'] ) ? $data['post_data']['give-current-url'] : site_url();

			// Format the donation amount as required by Stripe.
			$amount = give_stripe_format_amount( $data['price'] );

			// Fetch whether the billing address collection is enabled in admin settings or not.
			$is_billing_enabled = give_is_setting_enabled( give_get_option( 'stripe_collect_billing' ) );

			$session_args = [
				'customer'                   => $data['customer_id'],
				'client_reference_id'        => $data['purchase_key'],
				'payment_method_types'       => [ 'card' ],
				'billing_address_collection' => $is_billing_enabled ? 'required' : 'auto',
				'mode'                       => 'payment',
				'line_items'                 => [
					[
						'name'        => $form_name,
						'description' => $data['description'],
						'amount'      => $amount,
						'currency'    => give_get_currency( $form_id ),
						'quantity'    => 1,
					],
				],
				'payment_intent_data'        => [
					'capture_method'       => 'automatic',
					'description'          => $donation_summary,
					'metadata'             => $this->prepare_metadata( $donation_id ),
					'statement_descriptor' => give_stripe_get_statement_descriptor(),
				],
				'submit_type'                => 'donate',
				'success_url'                => give_get_success_page_uri(),
				'cancel_url'                 => give_get_failed_transaction_uri(),
				'locale'                     => give_stripe_get_preferred_locale(),
			];

			// If featured image exists, then add it to checkout session.
			if ( ! empty( get_the_post_thumbnail( $form_id ) ) ) {
				$session_args['line_items'][0]['images'] = [ get_the_post_thumbnail_url( $form_id ) ];
			}

			// Create Checkout Session.
			$session    = $this->stripe_checkout_session->create( $session_args );
			$session_id = ! empty( $session->id ) ? $session->id : false;

			// Set Checkout Session ID as Transaction ID.
			if ( ! empty( $session_id ) ) {
				give_insert_payment_note( $donation_id, 'Stripe Checkout Session ID: ' . $session_id );
				give_set_payment_transaction_id( $donation_id, $session_id );
			}

			// Save donation summary to donation.
			give_update_meta( $donation_id, '_give_stripe_donation_summary', $donation_summary );

			// Redirect to show loading area to trigger redirectToCheckout client side.
			wp_safe_redirect(
				add_query_arg(
					[
						'action'  => 'checkout_processing',
						'session' => $session_id,
						'id'      => $form_id,
					],
					$redirect_to_url
				)
			);

			// Don't execute code further.
			give_die();
		}

		/**
		 * Redirect to Checkout.
		 *
		 * @since  2.5.5
		 * @access public
		 *
		 * @return void
		 */
		public function redirect_to_checkout() {

			$get_data          = give_clean( $_GET );
			$form_id           = ! empty( $get_data['id'] ) ? absint( $get_data['id'] ) : false;
			$publishable_key   = give_stripe_get_publishable_key( $form_id );
			$session_id        = ! empty( $get_data['session'] ) ? $get_data['session'] : false;
			$action            = ! empty( $get_data['action'] ) ? $get_data['action'] : false;
			$default_account   = give_stripe_get_default_account( $form_id );
			$stripe_account_id = give_stripe_get_connected_account_id( $form_id );

			// Bailout, if action is not checkout processing.
			if ( 'checkout_processing' !== $action ) {
				return;
			}

			// Bailout, if session id doesn't exists.
			if ( ! $session_id ) {
				return;
			}
			?>
			<div id="give-stripe-checkout-processing"></div>
			<script>
				// Show Processing Donation Overlay.
				const processingHtml = document.querySelector( '#give-stripe-checkout-processing');

				processingHtml.setAttribute( 'class', 'stripe-checkout-process' );
				processingHtml.style.background = '#FFFFFF';
				processingHtml.style.opacity = '0.9';
				processingHtml.style.position = 'fixed';
				processingHtml.style.top = '0';
				processingHtml.style.left = '0';
				processingHtml.style.bottom = '0';
				processingHtml.style.right = '0';
				processingHtml.style.zIndex = '2147483646';
				processingHtml.innerHTML = '<div class="give-stripe-checkout-processing-container" style="position: absolute;top: 50%;left: 50%;width: 300px; margin-left: -150px; text-align:center;"><div style="display:inline-block;"><span class="give-loading-animation" style="color: #333;height:26px;width:26px;font-size:26px; margin:0; "></span><span style="color:#000; font-size: 26px; margin:0 0 0 10px;">' + give_stripe_vars.checkout_processing_text + '</span></div></div>';

				window.addEventListener('load', function() {
					let stripe = {};

					stripe = Stripe( '<?php echo $publishable_key; ?>' );

					<?php if ( ! empty( $stripe_account_id ) ) { ?>
					stripe = Stripe( '<?php echo $publishable_key; ?>', {
						'stripeAccount': '<?php echo $stripe_account_id; ?>'
					} );
					<?php } ?>

					// Redirect donor to Checkout page.
					stripe.redirectToCheckout({
						// Make the id field from the Checkout Session creation API response
						// available to this file, so you can provide it as parameter here
						// instead of the {{CHECKOUT_SESSION_ID}} placeholder.
						sessionId: '<?php echo $session_id; ?>'
					}).then( ( result ) => {
						console.log(result);
						// If `redirectToCheckout` fails due to a browser or network
						// error, display the localized error message to your customer
						// using `result.error.message`.
					});
				})
			</script>
			<?php
		}

		/**
		 * Stripe Checkout Modal HTML.
		 *
		 * @param int   $formId Donation Form ID.
		 * @param array $args   List of arguments.
		 *
		 * @since  2.7.3
		 * @access public
		 *
		 * @return void
		 */
		public function showCheckoutModal( $formId, $args ) {
			$idPrefix           = ! empty( $args['id_prefix'] ) ? $args['id_prefix'] : "{$formId}-1";
			$backgroundImageUrl = give_get_option( 'stripe_checkout_background_image', '' );
			$backgroundItem     = 'background-color: #000000;';

			// Load Background Image, if exists.
			if ( ! empty( $backgroundImageUrl ) ) {
				$backgroundImageUrl = esc_url_raw( $backgroundImageUrl );
				$backgroundItem     = "background-image: url('{$backgroundImageUrl}'); background-size: cover;";
			}
			?>
			<div id="give-stripe-checkout-modal-<?php echo $idPrefix; ?>" class="give-stripe-checkout-modal">
				<div class="give-stripe-checkout-modal-content">
					<div class="give-stripe-checkout-modal-container">
						<div class="give-stripe-checkout-modal-header" style="<?php echo $backgroundItem; ?>">
							<button class="give-stripe-checkout-modal-close">
								<svg
									width="20px"
									height="20px"
									viewBox="0 0 20 20"
									version="1.1"
									xmlns="http://www.w3.org/2000/svg"
									xmlns:xlink="http://www.w3.org/1999/xlink"
								>
									<defs>
										<path
											d="M10,8.8766862 L13.6440403,5.2326459 C13.9542348,4.92245137 14.4571596,4.92245137 14.7673541,5.2326459 C15.0775486,5.54284044 15.0775486,6.04576516 14.7673541,6.3559597 L11.1238333,9.99948051 L14.7673541,13.6430016 C15.0775486,13.9531961 15.0775486,14.4561209 14.7673541,14.7663154 C14.4571596,15.0765099 13.9542348,15.0765099 13.6440403,14.7663154 L10,11.1222751 L6.3559597,14.7663154 C6.04576516,15.0765099 5.54284044,15.0765099 5.2326459,14.7663154 C4.92245137,14.4561209 4.92245137,13.9531961 5.2326459,13.6430016 L8.87616671,9.99948051 L5.2326459,6.3559597 C4.92245137,6.04576516 4.92245137,5.54284044 5.2326459,5.2326459 C5.54284044,4.92245137 6.04576516,4.92245137 6.3559597,5.2326459 L10,8.8766862 Z"
											id="path-1"
										></path>
									</defs>
									<g
										id="Payment-recipes"
										stroke="none"
										stroke-width="1"
										fill="none"
										fill-rule="evenodd"
									>
										<g
											id="Elements-Popup"
											transform="translate(-816.000000, -97.000000)"
										>
											<g id="close-btn" transform="translate(816.000000, 97.000000)">
												<circle
													id="Oval"
													fill-opacity="0.3"
													fill="#AEAEAE"
													cx="10"
													cy="10"
													r="10"
												></circle>
												<mask id="mask-2" fill="white">
													<use xlink:href="#path-1"></use>
												</mask>
												<use
													id="Mask"
													fill-opacity="0.5"
													fill="#FFFFFF"
													opacity="0.5"
													xlink:href="#path-1"
												></use>
											</g>
										</g>
									</g>
								</svg>
							</button>
							<h3><?php echo give_get_option( 'stripe_checkout_name' ); ?></h3>
							<div class="give-stripe-checkout-donation-amount">
								<?php echo give_get_form_price( $formId ); ?>
							</div>
							<div class="give-stripe-checkout-donor-email"></div>
							<div class="give-stripe-checkout-form-title">
								<?php echo get_the_title( $formId ); ?>
							</div>
						</div>
						<div class="give-stripe-checkout-modal-body">
							<?php
							/**
							 * This action hook will be trigger in Stripe Checkout Modal before CC fields.
							 *
							 * @since 2.7.3
							 */
							do_action( 'give_stripe_checkout_modal_before_cc_fields', $formId, $args );

							// Load Credit Card Fields for Stripe Checkout.
							echo Stripe::showCreditCardFields( $idPrefix );

							/**
							 * This action hook will be trigger in Stripe Checkout Modal after CC fields.
							 *
							 * @since 2.7.3
							 */
							do_action( 'give_stripe_checkout_modal_after_cc_fields', $formId, $args );
							?>
							<input type="hidden" name="give_validate_stripe_payment_fields" value="0"/>
						</div>
						<div class="give-stripe-checkout-modal-footer">
							<div class="card-errors"></div>
							<?php
							$display_label_field = give_get_meta( $formId, '_give_checkout_label', true );
							$display_label_field = apply_filters( 'give_donation_form_submit_button_text', $display_label_field, $formId, $args );
							$display_label       = ( ! empty( $display_label_field ) ? $display_label_field : esc_html__( 'Donate Now', 'give' ) );
							ob_start();
							?>
							<div class="give-submit-button-wrap give-clearfix">
								<?php
								echo sprintf(
									'<input type="submit" class="%1$s" id="%2$s" value="%3$s" data-before-validation-label="%3$s" name="%4$s" disabled/>',
									FormUtils::isLegacyForm() ? 'give-btn give-stripe-checkout-modal-donate-button' : 'give-btn give-stripe-checkout-modal-sequoia-donate-button',
									"give-stripe-checkout-modal-donate-button-{$idPrefix}",
									$display_label,
									'give_stripe_modal_donate'
								);
								?>
								<span class="give-loading-animation"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Simplify Stripe Checkout Submit.
		 *
		 * @param int   $formId Donation Form ID.
		 * @param array $args   List of arguments.
		 *
		 * @since  2.7.1
		 * @access public
		 *
		 * @return void
		 */
		public function checkoutSubmit( $formId, $args ) {
			$idPrefix = ! empty( $args['id_prefix'] ) ? $args['id_prefix'] : "{$formId}-1";
			?>
			<fieldset id="give_purchase_submit" class="give-stripe-checkout-submit">
				<input
					id="give-stripe-checkout-modal-btn-<?php echo $idPrefix; ?>"
					class="give-stripe-checkout-modal-btn"
					type="submit"
					name="give-stripe-checkout-submit"
					value="<?php esc_html_e( 'Donate Now', 'give' ); ?>"
				/>
			</fieldset>
			<?php
		}
	}
}

new Give_Stripe_Checkout();
