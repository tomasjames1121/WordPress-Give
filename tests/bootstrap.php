<?php
/**
 * Give Unit Tests Bootstrap
 *
 * @since 1.3.2
 */
class Give_Unit_Tests_Bootstrap {

	/** @var \Give_Unit_Tests_Bootstrap instance */
	protected static $instance = null;

	/** @var string directory where wordpress-tests-lib	is installed */
	public $wp_tests_dir;

	/** @var string testing directory */
	public $tests_dir;

	/** @var string plugin directory */
	public $plugin_dir;

	/**
	 * Setup the unit testing environment
	 *
	 * @since 1.3.2
	 */
	public function __construct() {

		ini_set( 'display_errors', 'on' );
		error_reporting( E_ALL );
		
		$this->tests_dir 	= dirname( __FILE__ );
		$this->plugin_dir	= dirname( $this->tests_dir );
		$this->wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : '/tmp/wordpress-tests-lib';

		// load test function so tests_add_filter() is available
		require_once( $this->wp_tests_dir . '/includes/functions.php' );

		// load Give
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_give' ) );

		// install Give
		tests_add_filter( 'setup_theme', array( $this, 'install_give' ) );

		// load the WP testing environment
		require_once( $this->wp_tests_dir . '/includes/bootstrap.php' );

		// load Give testing framework
		$this->includes();
	}

	/**
	 * Load Give
	 *
	 * @since 1.3.2
	 */
	public function load_give() {
		require_once( $this->plugin_dir . '/give.php' );
	}

	/**
	 * Install Give after the test environment and Give have been loaded.
	 *
	 * @since 1.3.2
	 */
	public function install_give() {

		// clean existing install first
		define( 'WP_UNINSTALL_PLUGIN', true );
		include( $this->plugin_dir . '/uninstall.php' );

		echo "Installing Give..." . PHP_EOL;

		give_install();

		// reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374
		$GLOBALS['wp_roles']->reinit();
		$current_user = new WP_User(1);
		$current_user->set_role('administrator');
		wp_update_user( array( 'ID' => 1, 'first_name' => 'Admin', 'last_name' => 'User' ) );
		add_filter( 'give_log_email_errors', '__return_false' );
		
	}

	/**
	 * Load Give-specific test cases
	 *
	 * @since 1.3.2
	 */
	public function includes() {

		// test cases
		require_once( $this->tests_dir . '/framework/class-give-unit-test-case.php' );

		//Helpers
		require_once( $this->tests_dir . '/framework/helpers/shims.php' );
		require_once( $this->tests_dir . '/framework/helpers/class-helper-form.php' );
		require_once( $this->tests_dir . '/framework/helpers/class-helper-payment.php' );
	}

	/**
	 * Get the single class instance.
	 *
	 * @since 1.3.2
	 * @return Give_Unit_Tests_Bootstrap
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Give_Unit_Tests_Bootstrap::instance();