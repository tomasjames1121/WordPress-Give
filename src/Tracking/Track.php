<?php
namespace Give\Tracking;

use Give\Tracking\Helpers\Track as TrackHelper;

/**
 * Class Track
 *
 * This class uses to recode tracks and send them to sever on "shutdown" action hook.
 *
 * @since 2.10.0
 * @package Give\Tracking
 */
class Track {
	/**
	 * Collection of track events.
	 *
	 * @ssicne 2.10.0
	 * @var array
	 */
	private $tracks;

	/**
	 * Send tracks.
	 *
	 * @since 2.10.0
	 */
	public function send() {
		if ( empty( $this->tracks ) || ! TrackHelper::isTrackingEnabled() ) {
			return;
		}

		$trackClient = new TrackClient();

		foreach ( $this->tracks as $trackId => $trackData ) {
			$trackClient->post( $trackId, $trackData );
		}
	}

	/**
	 * Record track.
	 *
	 * @param string $trackId
	 * @param array $trackData
	 *
	 * @since 2.10.0
	 */
	public function recordTrack( $trackId, $trackData ) {
		$this->tracks[ $trackId ] = $trackData;
	}
}
