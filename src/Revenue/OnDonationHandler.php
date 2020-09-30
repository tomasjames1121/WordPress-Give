<?php
namespace Give\Revenue;

use Give\Revenue\Repositories\Revenue;
use WP_Post;

/**
 * Class OnDonationHandler
 * @package Give\Revenue
 * @since 2.9.0
 *
 * use this class to insert revenue when new donation create.
 */
class OnDonationHandler {
	/**
	 * Handle new donation.
	 *
	 * @param  int  $donationId
	 * @param  WP_Post  $donation
	 * @param bool $isUpdated
	 *
	 * @since 2.9.0
	 *
	 */
	public function handle( $donationId, $donation, $isUpdated ) {
		// Exit if it is not a new donation.
		if ( ! $isUpdated ) {
			return;
		}

		/* @var Revenue $revenue */
		$revenue              = give( Revenue::class );
		$donationAmountInCent = give_donation_amount( $donationId ) * 100;
		$formId               = give_get_payment_form_id( $donationId );

		$revenueData = [
			'donation_id' => $donationId,
			'form_id'     => $formId,
			'amount'      => $donationAmountInCent,
		];

		$revenue->insert( $revenueData );
	}
}
