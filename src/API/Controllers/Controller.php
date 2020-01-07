<?php
/**
 * Abstract API Controller class
 *
 * @package Give
 */

namespace Give\API\Controllers;

defined( 'ABSPATH' ) || exit;

/**
 * Common functionality for API Controllers. Override this class.
 */
abstract class Controller {

	/**
	 * Variables used to register controller
	 *
	 * @var string
	 */
    protected $namespace = '/give-api/v2';

	/**
	 * Initialize.
	 */
	public function __construct() {
        // Do nothing
    }

	public function register_routes() {
        // Override this method to define routes
        // See register_rest_route function: https://developer.wordpress.org/reference/functions/register_rest_route/
    }


    // Add unique functionality to support validation, permissions checks, schema

}
