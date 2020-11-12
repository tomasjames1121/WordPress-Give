<?php

namespace Give\DonorProfiles\Routes;

use WP_REST_Request;
use Give\API\RestRoute;
use Give\DonorProfiles\Repositories\Donations as DonationsRepository;

/**
 * @since 2.10.0
 */
class DonationHistoryRoute implements RestRoute {

	/** @var string */
	protected $endpoint = 'donor-profile/donation-history';

	/**
	 * @inheritDoc
	 */
	public function registerRoute() {
		register_rest_route(
			'give-api/v2',
			$this->endpoint,
			[
				[
					'methods'             => 'GET',
					'callback'            => [ $this, 'handleRequest' ],
					'permission_callback' => function() {
						return is_user_logged_in();
					},
				],
				'schema' => [ $this, 'getSchema' ],
			]
		);
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 *
	 * @since 2.10.0
	 */
	public function handleRequest( WP_REST_Request $request ) {
		return [
			'data' => $this->getData(),
		];
	}

	/**
	 * @return array
	 *
	 * @since 2.10.0
	 */
	public function getSchema() {
		return [
			// This tells the spec of JSON Schema we are using which is draft 4.
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			// The title property marks the identity of the resource.
			'title'      => 'donor-profile',
			'type'       => 'object',
			// In JSON Schema you can specify object properties in the properties attribute.
			'properties' => [
				// ...
			],
		];
	}

	/**
	 * @return array
	 *
	 * @since 2.10.0
	 */
	protected function getData() {

		$repository = new DonationsRepository();

		return [
			'donations'      => $repository->getDonations( 1 ),
			'donationsCount' => $repository->getDonationCount( 1 ),
			'revenue'        => $repository->getRevenue( 1 ),
		];

		// return [
		//     '2' => [
		//         'form' => [
		//             'title' => 'Save the Trees',
		//             'id' => '3',
		//         ],
		//         'payment' => [
		//             'amount' => '33.00',
		//             'currency' => 'USD',
		//             'fee' => '5.00',
		//             'total' => '38.00',
		//             'method' => 'paypal',
		//             'status' => 'pending',
		//             'date' => '04-01-2020 12:00:00'
		//         ],
		//         'donor' => [
		//             'name' => 'Ben Smith',
		//             'email' => 'bensmith@gmail.com',
		//             'address' => [
		//                 'street' => '875 26th St',
		//                 'city' => 'San Diego',
		//                 'state' => 'CA',
		//                 'zipcode' => '92081',
		//                 'country' => 'USA',
		//             ]
		//         ]
		//     ],
		//     '7' => [
		//         'form' => [
		//             'title' => 'Save the Whales',
		//             'id' => '5',
		//         ],
		//         'payment' => [
		//             'amount' => '30.00',
		//             'currency' => 'USD',
		//             'fee' => '5.00',
		//             'total' => '35.00',
		//             'method' => 'paypal',
		//             'status' => 'pending',
		//             'date' => '04-04-2020 12:00:00'
		//         ],
		//         'donor' => [
		//             'name' => 'Ben Smith',
		//             'email' => 'bensmith@gmail.com',
		//             'address' => [
		//                 'street' => '875 26th St',
		//                 'city' => 'San Diego',
		//                 'state' => 'CA',
		//                 'zipcode' => '92081',
		//                 'country' => 'USA',
		//             ]
		//         ]
		//     ],
		// ];
	}
}
