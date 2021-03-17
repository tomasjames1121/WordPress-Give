<?php

namespace Give\PaymentGateways\Stripe\Models;

/**
 * Class AccountDetail
 *
 * @package Give\PaymentGateways\Stripe\Models
 * @unreleased
 */
class AccountDetail {
	public $type;
	public $accountName;
	public $accountSlug;
	public $accountEmail;
	public $accountCountry;
	public $accountId;
	public $liveSecretKey;
	public $livePublishableKey;
	public $testSecretKey;
	public $testPublishableKey;

	/**
	 * Return AccountDetail model
	 *
	 * @unreleased
	 * @param $args
	 *
	 * @return AccountDetail
	 */
	public static function fromArray( $args ) {
		$accountDetail = new static();

		$accountDetail->type               = $args['type'];
		$accountDetail->accountName        = $args['account_name'];
		$accountDetail->accountSlug        = $args['account_slug'];
		$accountDetail->accountEmail       = $args['account_email'];
		$accountDetail->accountCountry     = $args['account_country'];
		$accountDetail->accountId          = $args['account_id'];
		$accountDetail->liveSecretKey      = $args['live_secret_key'];
		$accountDetail->livePublishableKey = $args['live_publishable_key'];
		$accountDetail->testSecretKey      = $args['test_secret_key'];
		$accountDetail->testPublishableKey = $args['test_publishable_key'];

		return $accountDetail;
	}
}
