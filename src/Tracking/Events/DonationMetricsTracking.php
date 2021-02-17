<?php

namespace Give\Tracking\Events;

use Give\Tracking\Contracts\TrackEvent;
use Give\Tracking\TrackRegisterer;
use Give\Tracking\TrackingData\DonationMetricsData;
use Give\Tracking\Enum\EventType;

/**
 * Class DonationMetricsTracking
 * @package Give\Tracking\Events
 *
 * @since 2.10.0
 */
class DonationMetricsTracking extends TrackEvent {

	/**
	 * @var string
	 */
	protected $trackId;

	/**
	 * @var string
	 */
	protected $dataClassName = DonationMetricsData::class;

	/**
	 * GivePluginSettingsTracking constructor.
	 *
	 * @since 2.10.0
	 *
	 * @param  TrackRegisterer  $track
	 *
	 */
	public function __construct( TrackRegisterer $track ) {
		$this->trackId = new EventType( 'donation-metrics' );
		parent::__construct( $track );
	}
}
