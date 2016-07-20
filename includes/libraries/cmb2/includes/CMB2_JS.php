<?php
/**
 * Handles the dependencies and enqueueing of the CMB2 JS scripts
 *
 * @category  WordPress_Plugin
 * @package   CMB2
 * @author    WebDevStudios
 * @license   GPL-2.0+
 * @link      http://webdevstudios.com
 */
class CMB2_JS {

	/**
	 * The CMB2 JS handle
	 * @var   string
	 * @since 2.0.7
	 */
	protected static $handle = 'cmb2-scripts';

	/**
	 * The CMB2 JS variable name
	 * @var   string
	 * @since 2.0.7
	 */
	protected static $js_variable = 'cmb2_l10';

	/**
	 * Array of CMB2 JS dependencies
	 * @var   array
	 * @since 2.0.7
	 */
	protected static $dependencies = array( 'jquery' => 'jquery' );

	/**
	 * Add a dependency to the array of CMB2 JS dependencies
	 * @since 2.0.7
	 * @param array|string  $dependencies Array (or string) of dependencies to add
	 */
	public static function add_dependencies( $dependencies ) {
		foreach ( (array) $dependencies as $dependency ) {
			self::$dependencies[ $dependency ] = $dependency;
		}
	}

	/**
	 * Enqueue the CMB2 JS
	 * @since  2.0.7
	 */
	public static function enqueue() {
		// Filter required script dependencies
		$dependencies = apply_filters( 'cmb2_script_dependencies', self::$dependencies );

		// if colorpicker
		if ( ! is_admin() && isset( $dependencies['wp-color-picker'] ) ) {
			self::colorpicker_frontend();
		}

		// if file/file_list
		if ( isset( $dependencies['media-editor'] ) ) {
			wp_enqueue_media();
		}

		// if timepicker
		if ( isset( $dependencies['jquery-ui-datetimepicker'] ) ) {
			wp_register_script( 'jquery-ui-datetimepicker', cmb2_utils()->url( 'js/jquery-ui-timepicker-addon.min.js' ), array( 'jquery-ui-slider' ), CMB2_VERSION );
		}

		// Only use minified files if SCRIPT_DEBUG is off
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		$min   = $debug ? '' : '.min';

		// Register cmb JS
		wp_enqueue_script( self::$handle, cmb2_utils()->url( "js/cmb2{$min}.js" ), $dependencies, CMB2_VERSION, true );

		self::localize( $debug );
	}

	/**
	 * We need to register colorpicker on the front-end
	 * @since  2.0.7
	 */
	protected static function colorpicker_frontend() {
		wp_register_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), CMB2_VERSION );
		wp_register_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), CMB2_VERSION );
		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', array(
			'clear'         => esc_html( 'Clear', 'cmb2' ),
			'defaultString' => esc_html( 'Default', 'cmb2' ),
			'pick'          => esc_html( 'Select Color', 'cmb2' ),
			'current'       => esc_html( 'Current Color', 'cmb2' ),
		) );
	}

	/**
	 * Localize the php variables for CMB2 JS
	 * @since  2.0.7
	 */
	protected static function localize( $debug ) {
		$l10n = array(
			'ajax_nonce'       => wp_create_nonce( 'ajax_nonce' ),
			'ajaxurl'          => admin_url( '/admin-ajax.php' ),
			'script_debug'     => $debug,
			'up_arrow_class'   => 'dashicons dashicons-arrow-up-alt2',
			'down_arrow_class' => 'dashicons dashicons-arrow-down-alt2',
			'defaults'         => array(
				'color_picker' => false,
				'date_picker'  => array(
					'changeMonth'     => true,
					'changeYear'      => true,
					'dateFormat'      => _x( 'mm/dd/yy', 'Valid formatDate string for jquery-ui datepicker', 'cmb2' ),
					'dayNames'        => explode( ',', esc_html( 'Sunday, Monday, Tuesday, Wednesday, Thursday, Friday, Saturday', 'cmb2' ) ),
					'dayNamesMin'     => explode( ',', esc_html( 'Su, Mo, Tu, We, Th, Fr, Sa', 'cmb2' ) ),
					'dayNamesShort'   => explode( ',', esc_html( 'Sun, Mon, Tue, Wed, Thu, Fri, Sat', 'cmb2' ) ),
					'monthNames'      => explode( ',', esc_html( 'January, February, March, April, May, June, July, August, September, October, November, December', 'cmb2' ) ),
					'monthNamesShort' => explode( ',', esc_html( 'Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec', 'cmb2' ) ),
					'nextText'        => esc_html( 'Next', 'cmb2' ),
					'prevText'        => esc_html( 'Prev', 'cmb2' ),
					'currentText'     => esc_html( 'Today', 'cmb2' ),
					'closeText'       => esc_html( 'Done', 'cmb2' ),
					'clearText'       => esc_html( 'Clear', 'cmb2' ),
				),
				'time_picker'  => array(
					'timeOnlyTitle' => esc_html( 'Choose Time', 'cmb2' ),
					'timeText'      => esc_html( 'Time', 'cmb2' ),
					'hourText'      => esc_html( 'Hour', 'cmb2' ),
					'minuteText'    => esc_html( 'Minute', 'cmb2' ),
					'secondText'    => esc_html( 'Second', 'cmb2' ),
					'currentText'   => esc_html( 'Now', 'cmb2' ),
					'closeText'     => esc_html( 'Done', 'cmb2' ),
					'timeFormat'    => _x( 'hh:mm TT', 'Valid formatting string, as per http://trentrichardson.com/examples/timepicker/', 'cmb2' ),
					'controlType'   => 'select',
					'stepMinute'    => 5,
				),
			),
			'strings' => array(
				'upload_file'  => esc_html( 'Use this file', 'cmb2' ),
				'upload_files' => esc_html( 'Use these files', 'cmb2' ),
				'remove_image' => esc_html( 'Remove Image', 'cmb2' ),
				'remove_file'  => esc_html( 'Remove', 'cmb2' ),
				'file'         => esc_html( 'File:', 'cmb2' ),
				'download'     => esc_html( 'Download', 'cmb2' ),
				'check_toggle' => esc_html( 'Select / Deselect All', 'cmb2' ),
			),
		);

		wp_localize_script( self::$handle, self::$js_variable, apply_filters( 'cmb2_localized_data', $l10n ) );
	}

}
