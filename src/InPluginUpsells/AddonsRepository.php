<?php

namespace Give\InPluginUpsells;

/**
 * @unreleased
 */
class AddonsRepository {
	/**
	 * @var string
	 */
	private $endpoint = 'https://givewp.com/downloads/upsells/addons.json';

	/**
	 * @var string
	 */
	private $transient = 'give-in-plugin-upsells';

	/**
	 * @return array
	 */
	private function fetchAddons() {
		$request = wp_remote_get( $this->endpoint, [
			'headers' => [
				'Content-Type' => 'application/json'
			]
		] );

		if ( is_wp_error( $request ) ) {
			return [];
		}

		$body = wp_remote_retrieve_body( $request );

		if ( empty( $body ) ) {
			return [];
		}

		return json_decode( $body, true );
	}

	/**
	 * @return array
	 */
	public function getAddons() {
		$cache = get_transient( $this->transient );

		if ( false === $cache ) {
			$addons = $this->fetchAddons();

			set_transient(
				$this->transient,
				serialize( $addons ),
				DAY_IN_SECONDS
			);

			return $addons;
		}

		return unserialize( $cache );
	}
}
