<?php

namespace Give\PaymentGateways\PayPalCommerce;

use Give\Helpers\ArrayDataSet;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use Give\ConnectClient\ConnectClient;
use Give\PaymentGateways\PayPalCommerce\Repositories\Webhooks;

/**
 * Class AjaxRequestHandler
 * @package Give\PaymentGateways\PaypalCommerce
 *
 * @sicne 2.8.0
 */
class AjaxRequestHandler {
	/**
	 * @var Webhooks
	 */
	private $webhooksRepository;

	/**
	 * @var MerchantDetail
	 */
	private $merchantDetails;

	/**
	 * @var PayPalClient
	 */
	private $paypalClient;

	/**
	 * @var ConnectClient
	 */
	private $connectClient;

	public function __construct(
		Webhooks $webhooksRepository,
		MerchantDetail $merchantDetails,
		PayPalClient $paypalClient,
		ConnectClient $connectClient
	) {
		$this->webhooksRepository = $webhooksRepository;
		$this->merchantDetails    = $merchantDetails;
		$this->paypalClient       = $paypalClient;
		$this->connectClient      = $connectClient;
	}

	/**
	 *  give_paypal_commerce_user_onboarded ajax action handler
	 *
	 * @since 2.8.0
	 */
	public function onBoardedUserAjaxRequestHandler() {
		$this->validateAdminRequest();

		$partnerLinkInfo = get_option( OptionId::$partnerInfoOptionKey, [ 'nonce' => '' ] );

		$payPalResponse = wp_remote_retrieve_body(
			wp_remote_post(
				$this->paypalClient->getEnvironment()->baseUrl() . '/v1/oauth2/token',
				[
					'headers' => [
						'Authorization' => sprintf(
							'Basic %1$s',
							base64_encode( give_clean( $_GET['sharedId'] ) )
						),
						'Content-Type'  => 'application/x-www-form-urlencoded',
					],
					'body'    => [
						'grant_type'    => 'authorization_code',
						'code'          => give_clean( $_GET['authCode'] ),
						'code_verifier' => $partnerLinkInfo['nonce'], // Seller nonce.
					],
				]
			)
		);

		if ( ! $payPalResponse ) {
			wp_send_json_error();
		}

		$payPalResponse = ArrayDataSet::camelCaseKeys( json_decode( $payPalResponse, true ) );

		update_option( OptionId::$accessTokenOptionKey, $payPalResponse );

		give( RefreshToken::class )->registerCronJobToRefreshToken( $payPalResponse['expiresIn'] );

		wp_send_json_success();
	}

	/**
	 * give_paypal_commerce_get_partner_url action handler
	 *
	 * @since 2.8.0
	 */
	public function onGetPartnerUrlAjaxRequestHandler() {
		$this->validateAdminRequest();

		$response = wp_remote_retrieve_body(
			wp_remote_post(
				sprintf(
					$this->connectClient->getApiUrl( 'paypal?mode=%1$s&request=partner-link' ),
					$this->paypalClient->mode
				),
				[
					'body' => [
						'return_url' => admin_url( 'edit.php?post_type=give_forms&page=give-settings&tab=gateways&section=paypal&group=paypal-commerce' ),
					],
				]
			)
		);

		if ( ! $response ) {
			wp_send_json_error();
		}

		$data = json_decode( $response, true );
		update_option( OptionId::$partnerInfoOptionKey, $data );

		wp_send_json_success( $data );
	}

	/**
	 * give_paypal_commerce_disconnect_account ajax request handler.
	 *
	 * @since 2.8.0
	 */
	public function removePayPalAccount() {
		$this->validateAdminRequest();

		// Remove the webhook from PayPal if there is one
		if ( $webhookId = $this->webhooksRepository->getWebhookId() ) {
			$this->webhooksRepository->deleteWebhook( $this->merchantDetails->accessToken, $webhookId );
			$this->webhooksRepository->deleteWebhookId();
		}

		$this->merchantDetails->delete();

		wp_send_json_success();
	}

	/**
	 * Create order.
	 *
	 * @todo: handle payment create error on frontend.
	 *
	 * @since 2.8.0
	 */
	public function createOrder() {
		$this->validateFrontendRequest();

		$postData = give_clean( $_POST );
		$formId   = absint( $postData['give-form-id'] );

		$request = new OrdersCreateRequest();
		$request->payPalPartnerAttributionId( PartnerDetails::$attributionId );
		$request->body = [
			'intent'              => 'CAPTURE',
			'purchase_units'      => [
				[
					'reference_id'        => get_post_field( 'post_name', $formId ),
					'description'         => '',
					'amount'              => [
						'value'         => give_maybe_sanitize_amount( $postData['give-amount'] ),
						'currency_code' => give_get_currency( $_POST['give-form-id'] ),
					],
					'payee'               => [
						'email_address' => $this->merchantDetails->merchantId,
						'merchant_id'   => $this->merchantDetails->merchantIdInPayPal,
					],
					'payer'               => [
						'given_name'    => $postData['give_first'],
						'surname'       => $postData['give_last'],
						'email_address' => $postData['give_email'],
					],
					'payment_instruction' => [
						'disbursement_mode' => 'INSTANT',
					],
				],
			],
			'application_context' => [
				'shipping_preference' => 'NO_SHIPPING',
				'user_action'         => 'PAY_NOW',
			],
		];

		try {
			$response = $this->paypalClient->getHttpClient()->execute( $request );

			wp_send_json_success(
				[
					'id' => $response->result->id,
				]
			);
		} catch ( \Exception $ex ) {
			wp_send_json_error(
				[
					'errorMsg' => $ex->getMessage(),
				]
			);
		}
	}

	/**
	 * Approve order.
	 *
	 * @todo: handle payment capture error on frontend.
	 *
	 * @since 2.8.0
	 */
	public function approveOrder() {
		$this->validateFrontendRequest();
		$orderId = give_clean( $_GET['order'] );

		$request = new OrdersCaptureRequest( $orderId );

		try {
			$response = $this->paypalClient->getHttpClient()->execute( $request );
			wp_send_json_success(
				[
					'order' => $response->result,
				]
			);
		} catch ( \Exception $ex ) {
			wp_send_json_error(
				[
					'errorMsg' => $ex->getMessage(),
				]
			);
		}
	}

	/**
	 * Validate admin ajax request.
	 *
	 * @since 2.8.0
	 */
	private function validateAdminRequest() {
		if ( ! current_user_can( 'manage_give_settings' ) ) {
			wp_send_json_error();
		}
	}

	/**
	 * Validate frontend ajax request.
	 *
	 * @since 2.8.0
	 */
	private function validateFrontendRequest() {
		$formId = absint( $_POST['give-form-id'] );

		if ( ! $formId || ! give_verify_donation_form_nonce( give_clean( $_POST['give-form-hash'] ), $formId ) ) {
			wp_send_json_error();
		}
	}
}
