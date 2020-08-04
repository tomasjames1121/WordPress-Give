<?php

namespace Give\Onboarding\Routes;

use WP_REST_Request;
use Give\API\RestRoute;
use Give\Onboarding\Helpers\Currency;
use Give\Onboarding\SettingsRepository;

class SettingsRoute implements RestRoute {

	protected $endpoint = 'onboarding/settings/(?P<setting>\w+)';

	/**
	 * @var SettingsRepository
	 */
	protected $settingsRepository;

	public function __construct( SettingsRepository $settingsRepository ) {
		$this->settingsRepository = $settingsRepository;
	}

	public function handleRequest( WP_REST_Request $request ) {

		$setting = $request->get_param( 'setting' );
		$value   = json_decode( $request->get_param( 'value' ) );

		$this->settingsRepository->set( $setting, $value );
		$this->settingsRepository->save();

		return [
			'data' => [
				'setting' => $setting,
				'value'   => $value,
			],
		];
	}

	public function validateSetting( $setting ) {
		return in_array(
			$setting,
			[
				'user_type',
				'cause_type',
				'base_country',
				'base_state',
				'addons',
				'features',
			]
		);
	}

	/**
	 * @inheritDoc
	 */
	public function registerRoute() {
		register_rest_route(
			'give-api/v2',
			$this->endpoint,
			[
				[
					'methods'             => 'POST',
					'callback'            => [ $this, 'handleRequest' ],
					'permission_callback' => function() {
						return current_user_can( 'manage_options' );
					},
					'args'                => [
						'setting' => [
							'type'              => 'string',
							'required'          => true,
							'validate_callback' => [ $this, 'validateSetting' ],
							'sanitize_callback' => 'sanitize_text_field',
						],
						'value'   => [
							'type'              => 'string',
							'required'          => false,
							// 'validate_callback' => [ $this, 'validateValue' ],
							'sanitize_callback' => 'sanitize_text_field',
						],
					],
				],
				'schema' => [ $this, 'getSchema' ],
			]
		);
	}

	public function getSchema() {
		return [
			// This tells the spec of JSON Schema we are using which is draft 4.
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			// The title property marks the identity of the resource.
			'title'      => 'onboarding',
			'type'       => 'object',
			// In JSON Schema you can specify object properties in the properties attribute.
			'properties' => [
				'setting' => [
					'description' => esc_html__( 'The reference name for the setting being updated.', 'give' ),
					'type'        => 'string',
				],
				'value'   => [
					'description' => esc_html__( 'The value of the setting being updated.', 'give' ),
					'type'        => 'string',
				],
			],
		];
	}
}
