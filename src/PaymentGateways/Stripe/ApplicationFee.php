<?php

namespace Give\PaymentGateways\Stripe;

use Give\PaymentGateways\Stripe\Repository\AccountDetail;
use Give_License;

/**
 * Class ApplicationFee
 * @package Give\PaymentGateways\Stripe
 *
 * @see https://github.com/impress-org/givewp/issues/5555#issuecomment-759596226
 *
 * @unreleased
 */
class ApplicationFee {
	/**
	 * Slug of the Stripe add-on on GiveWP.com
	 */
	const PluginSlug = 'give-stripe';

	/**
	 * Name of the Stripe add-on on GiveWP.com
	 */
	const PluginName = 'Give - Stripe Gateway';

	/**
	 * Returns true or false based on whether the Stripe fee should be applied or not
	 *
	 * @unreleased
	 *
	 * @param $formId
	 *
	 * @return bool
	 */
	public static function canAddFee( $formId ) {
		$gate = new static();

		/* @var $accountDetailRepository AccountDetail */
		$accountDetailRepository = give( AccountDetail::class );
		$stripeAccountDetail     = $accountDetailRepository->getDonationFormStripeAccountId( $formId );
		$country                 = $stripeAccountDetail->accountCountry;

		return $gate->isCountrySupportApplicationFee( $country )
			|| $gate->isStripeProAddonActive()
			|| $gate->isStripeProAddonInstalled( get_plugins() )
			|| ! $gate->hasLicense();
	}

	/**
	 * Returns true or false based on whether the Stripe Pro add-on is activated
	 *
	 * @unreleased
	 *
	 * @return bool
	 */
	public function isStripeProAddonActive() {
		return defined( 'GIVE_STRIPE_VERSION' );
	}

	/**
	 * Returns true or false based on whether the plugin is installed (but not necessarily active)
	 *
	 * @param array $plugins Array of arrays of plugin data, keyed by plugin file name. See get_plugin_data().
	 *
	 * @return bool
	 */
	public function isStripeProAddonInstalled( array $plugins ) {
		return (bool) array_filter(
			$plugins,
			static function ( $plugin ) {
				return static::PluginName === $plugin['Name'];
			}
		);
	}

	/**
	 * Returns true or false based on whether a license has been provided for the Stripe add-on
	 *
	 * @unreleased
	 *
	 * @return bool
	 */
	public function hasLicense() {
		return (bool) Give_License::get_license_by_plugin_dirname( static::PluginSlug );
	}

	/**
	 * Return whether or not country support application fee.
	 *
	 * @unreleased
	 *
	 * @param $country
	 *
	 * @return bool
	 */
	public function isCountrySupportApplicationFee( $country ) {
		return 'BR' !== $country;
	}
}
