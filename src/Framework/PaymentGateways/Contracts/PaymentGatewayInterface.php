<?php
namespace Give\Framework\PaymentGateways\Contracts;

use Give\PaymentGateways\DataTransferObjects\FormData;

/**
 * @unreleased
 */
interface PaymentGatewayInterface {
	/**
	 * Return a unique identifier for the gateway
	 *
	 * @unreleased
	 *
	 * @return string
	 */
	public static function id();

	/**
	 * Return a unique identifier for the gateway
	 *
	 * @unreleased
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * Returns a human-readable name for the gateway
	 *
	 * @unreleased
	 *
	 * @return string - Translated text
	 */
	public function getName();

	/**
	 * Returns a human-readable label for use when a donor selects a payment method to use
	 *
	 * @unreleased
	 *
	 * @return string - Translated text
	 */
	public function getPaymentMethodLabel();

	/**
	 * Returns a subscription module
	 *
	 * @unreleased
	 *
	 * @return string
	 */
	public function getSubscriptionModule();

	/**
	 * After creating the initial payment, we can continue with the gateway processing for a one-time request
	 *
	 * @unreleased
	 *
	 * @param  int  $donationId
	 * @param  FormData  $formData
	 *
	 * @return void
	 */
	public function handleOneTimeRequest( $donationId, $formData );

	/**
	 * After creating the initial payment, we can continue with the gateway processing for a subscription request
	 *
	 * @unreleased
	 *
	 * @param  int  $donationId
	 * @param  FormData  $formData
	 *
	 * @return void
	 */
	public function handleSubscriptionRequest( $donationId, $formData );
}
