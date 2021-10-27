<?php

namespace Give\PaymentGateways\DataTransferObjects;

use Give\ValueObjects\Address;
use Give\ValueObjects\CardInfo;
use Give\ValueObjects\DonorInfo;

/**
 * Class FormData
 * @unreleased
 */
class FormData {
	/**
	 * @var float
	 */
	public $price;
	/**
	 * @var string
	 */
	public $formTitle;
	/**
	 * @var string
	 */
	public $date;
	/**
	 * @var string
	 */
	public $purchaseKey;
	/**
	 * @var string
	 */
	public $currency;
	/**
	 * @var array
	 */
	public $userInfo;
	/**
	 * @var string
	 */
	public $paymentGateway;
	/**
	 * @var string
	 */
	public $gatewayNonce;
	/**
	 * @var array
	 */
	public $postData;
	/**
	 * @var CardInfo
	 */
	public $cardInfo;
	/**
	 * @var string
	 */
	public $honeypot;
	/**
	 * @var int
	 */
	public $formId;
	/**
	 * @var string
	 */
	public $priceId;
	/**
	 * @var string
	 */
	public $formIdPrefix;
	/**
	 * @var string
	 */
	public $currentUrl;
	/**
	 * @var string
	 */
	public $formMinimum;
	/**
	 * @var string
	 */
	public $formMaximum;
	/**
	 * @var string
	 */
	public $formHash;
	/**
	 * @var string
	 */
	public $loggedInOnly;
	/**
	 * @var string
	 */
	public $amount;
	/**
	 * @var string
	 */
	public $userId;
	/**
	 * @var Address
	 */
	public $billingAddress;
	/**
	 * @var DonorInfo
	 */
	public $donorInfo;

	/**
	 * Convert data from request into DTO
	 *
	 * @unreleased
	 *
	 * @return self
	 */
	public static function fromRequest( array $request ) {
		$self = new static();

		$self->price          = $request['price'];
		$self->date           = $request['date'];
		$self->purchaseKey    = $request['purchase_key'];
		$self->currency       = give_get_currency( $request['post_data']['give-form-id'], $request );
		$self->userInfo       = $request['user_info'];
		$self->postData       = $request['post_data'];
		$self->honeypot       = $request['post_data']['give-honeypot'];
		$self->formTitle      = $request['post_data']['give-form-title'];
		$self->formId         = (int) $request['post_data']['give-form-id'];
		$self->priceId        = isset( $request['post_data']['give-price-id'] ) ? $request['post_data']['give-price-id'] : '';
		$self->formIdPrefix   = $request['post_data']['give-form-id-prefix'];
		$self->currentUrl     = $request['post_data']['give-current-url'];
		$self->formMinimum    = $request['post_data']['give-form-minimum'];
		$self->formMaximum    = $request['post_data']['give-form-maximum'];
		$self->formHash       = $request['post_data']['give-form-hash'];
		$self->loggedInOnly   = $request['post_data']['give-logged-in-only'];
		$self->amount         = $request['post_data']['give-amount'];
		$self->paymentGateway = $request['post_data']['give-gateway'];
		$self->gatewayNonce   = $request['gateway_nonce'];
		$self->donorInfo      = DonorInfo::fromArray( [
			'wpUserId'  => $request['user_info']['id'],
			'firstName' => $request['user_info']['first_name'],
			'lastName'  => $request['user_info']['last_name'],
			'email'     => $request['user_info']['email'],
			'honorific' => ! empty( $request['user_info']['title'] ) ? $request['user_info']['title'] : '',
			'address'   => $request['user_info']['address']
		] );
		$self->cardInfo       = CardInfo::fromArray( [
			'name'     => $request['card_info']['card_name'],
			'cvc'      => $request['card_info']['card_cvc'],
			'expMonth' => $request['card_info']['card_exp_month'],
			'expYear'  => $request['card_info']['card_exp_year'],
			'number'   => $request['card_info']['card_number'],
		] );

		$self->billingAddress = Address::fromArray( $billingAddress );

		return $self;
	}

	/**
	 * This is used to expose data for use with give_insert_payment
	 *
	 * @return array
	 */
	public function toPaymentArray() {
		return [
			'price'           => $this->price,
			'give_form_title' => $this->formTitle,
			'give_form_id'    => $this->formId,
			'give_price_id'   => $this->priceId,
			'date'            => $this->date,
			'user_email'      => $this->donorInfo->email,
			'purchase_key'    => $this->purchaseKey,
			'currency'        => $this->currency,
			'user_info'       => $this->userInfo,
			'status'          => 'pending',
		];
	}
}
