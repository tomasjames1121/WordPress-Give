<?php
namespace Give\Tracking;

use Give\Tracking\Contracts\TrackData;
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
	 * @sicne 2.10.0
	 * @var TrackData[]
	 */
	private $newTracks;

	/**
	 * Recoded tracks.
	 * @var array
	 */
	private $recordedTracks;

	/**
	 * Option name to record tracks.
	 * @var string
	 */
	const  TRACK_RECORDS_OPTION_NAME = 'give_telemetry_records';

	/**
	 * Cron job name.
	 * @var string
	 */
	const CRON_JOB_NAME = 'give_telemetry_send_requests';

	/**
	 * Track constructor.
	 */
	public function __construct() {
		$this->recordedTracks = get_option( self::TRACK_RECORDS_OPTION_NAME, [] );
	}

	/**
	 * Send tracks.
	 *
	 * @since 2.10.0
	 */
	public function send() {
		if ( empty( $this->recordedTracks ) || ! TrackHelper::isTrackingEnabled() ) {
			return;
		}

		$trackClient = new TrackClient();

		foreach ( $this->recordedTracks as $trackId => $trackData ) {
			$trackClient->post( $trackId, $trackData->get() );
		}
	}

	/**
	 * Schedule cron job to send request to telemetry server.
	 *
	 * @since 2.10.0
	 */
	public function scheduleCronJob() {
		if ( ! $this->newTracks ) {
			return;
		}

		update_option( self::TRACK_RECORDS_OPTION_NAME, array_merge( $this->recordedTracks, $this->newTracks ) );

		if ( ! wp_next_scheduled( self::CRON_JOB_NAME ) ) {
			wp_schedule_single_event( strtotime( 'tomorrow - 1 day', current_time( 'timestamp' ) ), self::CRON_JOB_NAME );
		}
	}

	/**
	 * Record track.
	 *
	 * @param string $trackId
	 * @param TrackData $trackData
	 *
	 * @since 2.10.0
	 */
	public function recordTrack( $trackId, TrackData $trackData ) {
		if ( array_key_exists( $trackId, $this->recordedTracks ) ) {
			return;
		}

		$this->newTracks[ $trackId ] = $trackData;
	}
}
