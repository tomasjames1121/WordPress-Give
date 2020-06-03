<?php
namespace Give\Helpers\Gateways;

/**
 * Class Stripe
 *
 * @package Give\Helpers\Gateways
 */
class Stripe {

	/**
	 * Check whether the Account is configured or not.
	 *
	 * @since  2.7.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function isAccountConfigured() {
		$publishableKey = give_stripe_get_publishable_key();
		$secretKey      = give_stripe_get_secret_key();

		return ! empty( $publishableKey ) || ! empty( $secretKey );
	}

	/**
	 * This function is used to add Stripe account details to donation if donation process with any stripe payment method.
	 *
	 * @param int $donationId
	 * @param int $formId
	 * @param string $paymentMethod
	 *
	 * @since 2.7.0
	 */
	public static function addAccountDetail( $donationId, $formId, $paymentMethod ) {
		// Bailout, if the donation is not processed with any of the supported payment method of Stripe.
		if ( ! in_array( $paymentMethod, give_stripe_supported_payment_methods(), true ) ) {
			return;
		}

		$accountId     = give_stripe_get_default_account_slug( $formId );
		$accountDetail = give_stripe_get_default_account( $formId );
		$accountName   = 'connect' === $accountDetail['type'] ? $accountDetail['account_name'] : give_stripe_convert_slug_to_title( $accountId );

		$stripeAccountNote = 'connect' === $accountDetail['type'] ?
			sprintf(
				'%1$s "%2$s" %3$s',
				esc_html__( 'Donation accepted with Stripe account', 'give' ),
				"{$accountName} ({$accountId})",
				esc_html__( 'using Stripe Connect.', 'give' )
			) :
			sprintf(
				'%1$s "%2$s" %3$s',
				esc_html__( 'Donation accepted with Stripe account', 'give' ),
				$accountName,
				esc_html__( 'using Manual API Keys.', 'give' )
			);

		give_update_meta( $donationId, '_give_stripe_account_slug', $accountId );

		// Log data to donation notes.
		give_insert_payment_note( $donationId, $stripeAccountNote );
	}
}
