<?php
/**
 * Install Function
 *
 * @package     Give
 * @subpackage  Functions/Install
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Install
 *
 * Runs on plugin install by setting up the post types, custom taxonomies, flushing rewrite rules to initiate the new 'donations' slug and also creates the plugin and populates the settings fields for those plugin pages. After successful install, the user is redirected to the Give Welcome screen.
 *
 * @since 1.0
 *
 * @param bool $network_wide
 *
 * @global     $wpdb
 * @return void
 */
function give_install( $network_wide = false ) {

	global $wpdb;

	if ( is_multisite() && $network_wide ) {

		foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {

			switch_to_blog( $blog_id );
			give_run_install();
			restore_current_blog();

		}

	} else {

		give_run_install();

	}

}

/**
 * Run the Give Install process.
 *
 * @since  1.5
 * @return void
 */
function give_run_install() {
	$give_options = give_get_settings();

	// Setup the Give Custom Post Types.
	give_setup_post_types();

	// Add Upgraded From Option.
	$current_version = get_option( 'give_version' );
	if ( $current_version ) {
		update_option( 'give_version_upgraded_from', $current_version );
	}

	// Setup some default options.
	$options = array();

	//Fresh Install? Setup Test Mode, Base Country (US), Test Gateway, Currency.
	if ( empty( $current_version ) ) {
		$options = array_merge( $options, give_get_default_settings() );
	}

	// Populate the default values.
	update_option( 'give_settings', array_merge( $give_options, $options ) );

	/**
	 * Run plugin upgrades.
	 *
	 * @since 1.8
	 */
	do_action( 'give_upgrades' );

	if ( GIVE_VERSION !== get_option( 'give_version' ) ) {
		update_option( 'give_version', GIVE_VERSION );
	}

	// Create Give roles.
	$roles = new Give_Roles();
	$roles->add_roles();
	$roles->add_caps();

	// Set api version, end point and refresh permalink.
	$api = new Give_API();
	$api->add_endpoint();
	update_option( 'give_default_api_version', 'v' . $api->get_version() );

	flush_rewrite_rules();

	// Create the donor databases.
	$donors_db = new Give_DB_Donors();
	$donors_db->create_table();
	$donor_meta = new Give_DB_Donor_Meta();
	$donor_meta->create_table();

	// Check for PHP Session support, and enable if available.
	$give_sessions = new Give_Session();
	$give_sessions->use_php_sessions();

	// Add a temporary option to note that Give pages have been created.
	Give_Cache::set( '_give_installed', $options, 30, true );

	if ( ! $current_version ) {

		require_once GIVE_PLUGIN_DIR . 'includes/admin/upgrades/upgrade-functions.php';

		// When new upgrade routines are added, mark them as complete on fresh install.
		$upgrade_routines = array(
			'upgrade_give_user_caps_cleanup',
			'upgrade_give_payment_customer_id',
			'upgrade_give_offline_status',
			'v18_upgrades_core_setting',
			'v18_upgrades_form_metadata',
			'v189_upgrades_levels_post_meta',
			'v1812_update_amount_values',
			'v1812_update_donor_purchase_values',
			'v1813_update_user_roles',
			'v1813_update_donor_user_roles',
			'v1817_update_donation_iranian_currency_code',
			'v20_upgrades_form_metadata',
			'v20_logs_upgrades',
			'v20_move_metadata_into_new_table',
			'v20_rename_donor_tables',
			'v20_upgrades_donor_name',
			'v20_upgrades_user_address'
		);

		foreach ( $upgrade_routines as $upgrade ) {
			give_set_upgrade_complete( $upgrade );
		}
	}

	// Bail if activating from network, or bulk.
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
	}

	// Add the transient to redirect.
	Give_Cache::set( '_give_activation_redirect', true, 30, true );
}

/**
 * Network Activated New Site Setup.
 *
 * When a new site is created when Give is network activated this function runs the appropriate install function to set up the site for Give.
 *
 * @since      1.3.5
 *
 * @param  int $blog_id The Blog ID created.
 * @param  int $user_id The User ID set as the admin.
 * @param  string $domain The URL.
 * @param  string $path Site Path.
 * @param  int $site_id The Site ID.
 * @param  array $meta Blog Meta.
 */
function give_on_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

	if ( is_plugin_active_for_network( GIVE_PLUGIN_BASENAME ) ) {

		switch_to_blog( $blog_id );
		give_install();
		restore_current_blog();

	}

}

add_action( 'wpmu_new_blog', 'give_on_create_blog', 10, 6 );


/**
 * Drop Give's custom tables when a mu site is deleted.
 *
 * @since  1.4.3
 *
 * @param  array $tables The tables to drop.
 * @param  int $blog_id The Blog ID being deleted.
 *
 * @return array          The tables to drop.
 */
function give_wpmu_drop_tables( $tables, $blog_id ) {

	switch_to_blog( $blog_id );
	$donors_db     = new Give_DB_Donors();
	$donor_meta_db = new Give_DB_Donor_Meta();

	if ( $donors_db->installed() ) {
		$tables[] = $donors_db->table_name;
		$tables[] = $donor_meta_db->table_name;
	}
	restore_current_blog();

	return $tables;

}

add_filter( 'wpmu_drop_tables', 'give_wpmu_drop_tables', 10, 2 );

/**
 * Post-installation
 *
 * Runs just after plugin installation and exposes the give_after_install hook.
 *
 * @since 1.0
 * @return void
 */
function give_after_install() {

	if ( ! is_admin() ) {
		return;
	}

	$give_options     = Give_Cache::get( '_give_installed', true );
	$give_table_check = get_option( '_give_table_check', false );

	if ( false === $give_table_check || current_time( 'timestamp' ) > $give_table_check ) {

		if ( ! @Give()->donor_meta->installed() ) {

			// Create the donor meta database.
			// (this ensures it creates it on multisite instances where it is network activated).
			@Give()->donor_meta->create_table();

		}

		if ( ! @Give()->donors->installed() ) {
			// Create the donor database.
			// (this ensures it creates it on multisite instances where it is network activated).
			@Give()->donors->create_table();

			/**
			 * Fires after plugin installation.
			 *
			 * @since 1.0
			 *
			 * @param array $give_options Give plugin options.
			 */
			do_action( 'give_after_install', $give_options );
		}

		update_option( '_give_table_check', ( current_time( 'timestamp' ) + WEEK_IN_SECONDS ) );

	}

	// Delete the transient
	if ( false !== $give_options ) {
		Give_Cache::delete( Give_Cache::get_key( '_give_installed' ) );
	}


}

add_action( 'admin_init', 'give_after_install' );


/**
 * Install user roles on sub-sites of a network
 *
 * Roles do not get created when Give is network activation so we need to create them during admin_init
 *
 * @since 1.0
 * @return void
 */
function give_install_roles_on_network() {

	global $wp_roles;

	if ( ! is_object( $wp_roles ) ) {
		return;
	}

	if ( ! array_key_exists( 'give_manager', $wp_roles->roles ) ) {

		// Create Give plugin roles
		$roles = new Give_Roles();
		$roles->add_roles();
		$roles->add_caps();

	}

}

add_action( 'admin_init', 'give_install_roles_on_network' );

/**
 * Default core setting values.
 *
 * @since 1.8
 * @return array
 */
function give_get_default_settings() {

	$options = array(
		// General.
		'base_country'                                => 'US',
		'test_mode'                                   => 'enabled',
		'currency'                                    => 'USD',
		'currency_position'                           => 'before',
		'session_lifetime'                            => '604800',
		'email_access'                                => 'enabled',
		'thousands_separator'                         => ',',
		'decimal_separator'                           => '.',
		'number_decimals'                             => 2,

		// Display options.
		'css'                                         => 'enabled',
		'floatlabels'                                 => 'disabled',
		'welcome'                                     => 'enabled',
		'forms_singular'                              => 'enabled',
		'forms_archives'                              => 'enabled',
		'forms_excerpt'                               => 'enabled',
		'form_featured_img'                           => 'enabled',
		'form_sidebar'                                => 'enabled',
		'categories'                                  => 'disabled',
		'tags'                                        => 'disabled',
		'terms'                                       => 'disabled',
		'admin_notices'                               => 'enabled',
		'cache'                                       => 'enabled',
		'uninstall_on_delete'                         => 'disabled',
		'the_content_filter'                          => 'enabled',
		'scripts_footer'                              => 'disabled',
		'agree_to_terms_label'                        => __( 'Agree to Terms?', 'give' ),
		'agreement_text'                              => give_get_default_agreement_text(),

		// Paypal IPN verification.
		'paypal_verification'                         => 'enabled',

		// Default is manual gateway.
		'gateways'                                    => array( 'manual' => 1, 'offline' => 1 ),
		'default_gateway'                             => 'manual',

		// Offline gateway setup.
		'global_offline_donation_content'             => give_get_default_offline_donation_content(),
		'global_offline_donation_email'               => give_get_default_offline_donation_content(),

		// Billing address.
		'give_offline_donation_enable_billing_fields' => 'disabled',

		// Default donation notification email.
		'donation_notification'                       => give_get_default_donation_notification_email(),

		// Default email receipt message.
		'donation_receipt'                            => give_get_default_donation_receipt_email(),

		'donor_default_user_role'                     => 'give_donor',

	);

	return $options;
}

/**
 * Default terms and conditions.
 */
function give_get_default_agreement_text() {

	$org_name = get_bloginfo( 'name' );

	$agreement = sprintf(
		'<p>Acceptance of any contribution, gift or grant is at the discretion of the %1$s. The  %1$s will not accept any gift unless it can be used or expended consistently with the purpose and mission of the  %1$s.</p>
				<p>No irrevocable gift, whether outright or life-income in character, will be accepted if under any reasonable set of circumstances the gift would jeopardize the donor’s financial security.</p>
				<p>The %1$s will refrain from providing advice about the tax or other treatment of gifts and will encourage donors to seek guidance from their own professional advisers to assist them in the process of making their donation.</p>
				<p>The %1$s will accept donations of cash or publicly traded securities. Gifts of in-kind services will be accepted at the discretion of the %1$s.</p>
				<p>Certain other gifts, real property, personal property, in-kind gifts, non-liquid securities, and contributions whose sources are not transparent or whose use is restricted in some manner, must be reviewed prior to acceptance due to the special obligations raised or liabilities they may pose for %1$s.</p>
				<p>The %1$s will provide acknowledgments to donors meeting tax requirements for property received by the charity as a gift. However, except for gifts of cash and publicly traded securities, no value shall be ascribed to any receipt or other form of substantiation of a gift received by %1$s.</p>
				<p>The %1$s will respect the intent of the donor relating to gifts for restricted purposes and those relating to the desire to remain anonymous. With respect to anonymous gifts, the %1$s will restrict information about the donor to only those staff members with a need to know.</p>
				<p>The %1$s will not compensate, whether through commissions, finders\' fees, or other means, any third party for directing a gift or a donor to the %1$s.</p>',
		$org_name
	);

	return apply_filters( 'give_get_default_agreement_text', $agreement, $org_name );
}


/**
 * This function will install give related page which is not created already.
 *
 * @since 1.8.11
 *
 * @return void
 */
function give_create_pages(){

	// Bailout if pages already created.
	if( get_option( 'give_install_pages_created') ) {
		return false;
	}

	$options = array();

	// Checks if the Success Page option exists AND that the page exists.
	if ( ! get_post( give_get_option( 'success_page' ) ) ) {

		// Donation Confirmation (Success) Page
		$success = wp_insert_post(
			array(
				'post_title'     => esc_html__( 'Donation Confirmation', 'give' ),
				'post_content'   => '[give_receipt]',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'page',
				'comment_status' => 'closed'
			)
		);

		// Store our page IDs
		$options['success_page'] = $success;
	}

	// Checks if the Failure Page option exists AND that the page exists.
	if ( ! get_post( give_get_option( 'failure_page' ) ) ) {

		// Failed Donation Page
		$failed = wp_insert_post(
			array(
				'post_title'     => esc_html__( 'Donation Failed', 'give' ),
				'post_content'   => esc_html__( 'We\'re sorry, your donation failed to process. Please try again or contact site support.', 'give' ),
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'page',
				'comment_status' => 'closed'
			)
		);

		$options['failure_page'] = $failed;
	}

	// Checks if the History Page option exists AND that the page exists.
	if ( ! get_post( give_get_option( 'history_page' ) ) ) {
		// Donation History Page
		$history = wp_insert_post(
			array(
				'post_title'     => esc_html__( 'Donation History', 'give' ),
				'post_content'   => '[donation_history]',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'page',
				'comment_status' => 'closed'
			)
		);

		$options['history_page'] = $history;
	}

	if( ! empty( $options ) ) {
		update_option( 'give_settings', array_merge( give_get_settings(), $options ) );
	}

	add_option( 'give_install_pages_created', 1, '', 'no' );
}
add_action( 'admin_init', 'give_create_pages', -1 );
