<?php

namespace Give\DonorProfiles\Routes;

use WP_REST_Request;
use WP_REST_Response;
use Give\API\RestRoute;
use Give\DonorProfiles\Profile as Profile;
use Give\DonorProfiles\Helpers\SanitizeProfileData as SanitizeHelper;

/**
 * @since 2.11.0
 */
class LogoutRoute implements RestRoute {

	/** @var string */
	protected $endpoint = 'donor-profile/logout';

	/**
	 * @inheritDoc
	 */
	public function registerRoute() {
		register_rest_route(
			'give-api/v2',
			$this->endpoint,
			[
				[
					'methods'  => 'POST',
					'callback' => [ $this, 'handleRequest' ],
				],
			]
		);
	}

	/**
	 * Handles profile update, and returns updated profile array
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 *
	 * @since 2.11.0
	 */
	public function handleRequest( WP_REST_Request $request ) {

		// Prevent occurring of any custom action on wp_logout.
		remove_all_actions( 'wp_logout' );

		/**
		 * Fires before processing user logout.
		 *
		 * @since 1.0
		 */
		do_action( 'give_before_user_logout' );

		// Logout user.
		wp_logout();

		/**
		 * Fires after processing user logout.
		 *
		 * @since 1.0
		 */
		do_action( 'give_after_user_logout' );

		return new WP_REST_Response(
			[
				'status'        => 200,
				'response'      => 'logout_successful',
				'body_response' => [
					'message' => 'User was logged out succesfully.',
				],
			]
		);

	}
}
