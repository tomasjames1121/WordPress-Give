<?php

/**
 * Class Tests_Give
 */
class Tests_Give extends Give_Unit_Test_Case {
	protected $object;

	public function setUp() {
		parent::setUp();
		$this->object = Give();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * @covers Give
	 */
	public function test_give_instance() {
		$this->assertClassHasStaticAttribute( 'instance', 'Give' );
	}

	/**
	 * @covers Give::setup_constants
	 */
	public function test_constants() {
		// Plugin Folder URL
		$path = str_replace( 'tests/unit-tests/', '', plugin_dir_url( __FILE__ ) );
		$this->assertSame( GIVE_PLUGIN_URL, $path );

		// Plugin Folder Path
		$path = str_replace( 'tests/unit-tests/', '', plugin_dir_path( __FILE__ ) );
		$this->assertSame( GIVE_PLUGIN_DIR, $path );

		// Plugin Root File
		$path = str_replace( 'tests/unit-tests/', '', plugin_dir_path( __FILE__ ) );
		$this->assertSame( GIVE_PLUGIN_FILE, $path . 'give.php' );
	}

	/**
	 * @covers Give::includes
	 */
	public function test_includes() {

		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/post-types.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/scripts.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/ajax-functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/actions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-roles.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-template-loader.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-donate-form.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-db.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-db-customer-meta.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-db-customers.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-customer.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-stats.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-session.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-html-elements.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-logging.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/class-give-license-handler.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/country-functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/template-functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/misc-functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/forms/functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/forms/template.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/forms/widget.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/shortcodes.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/formatting.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/error-tracking.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/process-purchase.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/login-register.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/user-functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/payments/functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/payments/actions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/payments/class-payment-stats.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/payments/class-payments-query.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/gateways/functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/gateways/actions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/gateways/paypal-standard.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/gateways/offline-donations.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/gateways/manual.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/emails/class-give-emails.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/emails/class-give-email-tags.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/emails/functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/emails/template.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/emails/actions.php' );

		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/admin-footer.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/welcome.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/admin-pages.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/class-admin-notices.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/system-info.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/add-ons.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/dashboard-widgets.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/payments/actions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/payments/payments-history.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/customers/customers.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/customers/customer-functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/customers/customer-actions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/forms/metabox.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/forms/dashboard-columns.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/reporting/export/export-functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/reporting/reports.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/reporting/pdf-reports.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/reporting/class-give-graph.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/reporting/graphing.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/reporting/tools/tools-actions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/shortcodes/abstract-shortcode-generator.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/shortcodes/class-shortcode-button.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/shortcodes/shortcode-give-donation-history.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/shortcodes/shortcode-give-form.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/shortcodes/shortcode-give-goal.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/shortcodes/shortcode-give-login.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/shortcodes/shortcode-give-profile-editor.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/shortcodes/shortcode-give-receipt.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/shortcodes/shortcode-give-register.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/upgrades/upgrade-functions.php' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'includes/admin/upgrades/upgrades.php' );


		/** Check Assets Exist */
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/css/chosen.css' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/css/chosen.min.css' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/css/chosen-sprite.png' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/css/give-admin.css' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/css/give-admin.css.map' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/css/give-admin.min.css' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/css/jquery-ui-fresh.css' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/css/jquery-ui-fresh.min.css' );

		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/admin/addons.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/admin/dashboard.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/admin/donors.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/admin/forms.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/admin/give-admin.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/admin/logs.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/admin/payment-history.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/admin/reports.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/admin/settings.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/admin/welcome.scss' );

		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/frontend/_mixins.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/frontend/_variables.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/frontend/fonts.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/frontend/forms.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/frontend/give-frontend.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/frontend/layouts.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/frontend/progress-bar.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/frontend/receipt.scss' );

		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/plugins/_settings.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/plugins/float-labels.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/plugins/magnific-popup.scss' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/scss/plugins/qtip.scss' );

		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/fonts/icomoon.eot' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/fonts/icomoon.svg' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/fonts/icomoon.ttf' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/fonts/icomoon.woff' );

		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/admin/admin-forms.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/admin/admin-forms.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/admin/admin-scripts.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/admin/admin-scripts.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/admin/admin-widgets.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/admin/admin-widgets.min.js' );

		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/frontend/give.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/frontend/give.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/frontend/give.all.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/frontend/give-ajax.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/frontend/give-ajax.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/frontend/give-checkout-global.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/frontend/give-checkout-global.min.js' );

		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/chosen.jquery.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/chosen.jquery.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/float-labels.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/float-labels.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.magnific-popup.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.magnific-popup.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.blockUI.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.blockUI.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.payment.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.payment.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.flot.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.flot.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.flot.orderBars.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.flot.orderBars.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.flot.time.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.flot.time.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.qtip.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.qtip.min.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.payment.js' );
		$this->assertFileExists( GIVE_PLUGIN_DIR . 'assets/js/plugins/jquery.payment.min.js' );

	}
}
