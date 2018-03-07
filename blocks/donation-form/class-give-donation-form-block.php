<?php
/**
 * Give Donation Form Block Class
 *
 * @package     Give
 * @subpackage  Classes/Blocks
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       2.0.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Give_Donation_Form_Block Class.
 *
 * This class handles donation forms block.
 *
 * @since 2.0.2
 */
class Give_Donation_Form_Block {
	/**
	 * Instance.
	 *
	 * @since
	 * @access private
	 * @var Give_Donation_Form_Block
	 */
	static private $instance;

	/**
	 * Singleton pattern.
	 *
	 * @since
	 * @access private
	 */
	private function __construct() {
	}


	/**
	 * Get instance.
	 *
	 * @since
	 * @access public
	 * @return Give_Donation_Form_Block
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			self::$instance = new static();

			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * Set up the Give Donation Form Block class.
	 *
	 * @since  2.0.2
	 * @access private
	 */
	private function init() {

		// Setup hooks.
		add_action( 'rest_api_init', array( $this, 'register_rest_api' ) );

		// Register block.
		register_block_type( 'give/donation-form', array(
			'render_callback' => array( $this, 'render_donation_form' ),
			'attributes'      => array(
				'id'                  => array(
					'type' => 'number',
				),
				'displayStyle'        => array(
					'type' => 'string',
				),
				'continueButtonTitle' => array(
					'type' => 'string',
				),
				'showTitle'           => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'showGoal'            => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'showContent'         => array(
					'type'    => 'string',
					'default' => 'none',
				),
			),
		) );
	}

	/**
	 * Block render callback
	 *
	 * @param array $attributes Block parameters.
	 *
	 * @access public
	 * @return string;
	 */
	public function render_donation_form( $attributes ) {
		// Bailout.
		if ( empty( $attributes['id'] ) ) {
			return '';
		}

		$parameters = array();

		$parameters['id']                    = $attributes['id'];
		$parameters['show_title']            = $attributes['showTitle'];
		$parameters['show_goal']             = $attributes['showGoal'];
		$parameters['show_content']          = $attributes['showContent'];
		$parameters['display_style']         = $attributes['displayStyle'];
		$parameters['continue_button_title'] = trim( $attributes['continueButtonTitle'] );

		return give_form_shortcode( $parameters );
	}

	/**
	 * Register rest route to fetch form data
	 * @TODO   : This is a temporary solution. Next step would be to find a solution that is limited to the editor.
	 * @access public
	 * @return void
	 */
	public function register_rest_api() {
		register_rest_route( 'give-api/v1', '/form/(?P<id>\d+)', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'get_forms_data' ),
		) );
	}

	/**
	 * Rest fetch form data callback
	 *
	 * @param WP_REST_Request $request
	 *
	 * @access public
	 * @return array|mixed|object
	 */
	public function get_forms_data( $request ) {
		$parameters = $request->get_params();

		// Bailout
		if ( ! isset( $parameters['id'] ) || empty( $parameters['id'] ) ) {
			return array( 'error' => 'no_parameter_given' );
		}

		if ( ! ( $html = give_form_shortcode( $parameters ) ) ) {
			// @todo: add notice here for form which do not has publish status.
			$html = '';
		}

		// Response data array
		$response = array(
			'html' => $html,
		);

		return $response;
	}
}
