<?php

namespace Give\DonorProfiles;

use Give\DonorProfiles\App as DonorProfile;

class Shortcode {

	protected $donorProfile;

	public function __construct() {
		$this->donorProfile = give( DonorProfile::class );
	}

	/**
	 * Registers Donor Profile Shortcode
	 *
	 * @since 2.10.0
	 **/
	public function addShortcode() {
		add_shortcode( 'give_donor_dashboard', [ $this, 'renderCallback' ] );
	}

	/**
	 * Load Donor Profile frontend assets
	 *
	 * @since 2.9.0
	 **/
	public function loadFrontendAssets() {
		global $post;
		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'give_donor_dashboard' ) ) {
			return $this->donorProfile->loadAssets();
		}
	}

	/**
	 * Returns Shortcode markup
	 *
	 * @since 2.10.0
	 **/
	public function renderCallback( $attributes ) {
		$attributes = shortcode_atts(
			[
				'accent_color' => '#68bb6c',
			],
			$attributes,
			'give_donor_dashboard'
		);
		return $this->donorProfile->getOutput( $attributes );
	}
}
