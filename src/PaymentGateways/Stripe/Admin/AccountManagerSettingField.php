<?php

namespace Give\PaymentGateways\Stripe\Admin;

use Give\PaymentGateways\Stripe\Repositories\AccountDetail;
use Give_Admin_Settings;
use function _e;
use function add_query_arg;
use function do_action;
use function esc_html;
use function esc_html__;
use function esc_html_e;
use function esc_url_raw;
use function get_site_url;
use function give;
use function esc_attr;
use function give_has_upgrade_completed;
use function give_stripe_connection_type_name;
use function give_stripe_is_premium_active;

/**
 * Class AccountManagerSettingField
 *
 * @package Give\PaymentGateways\Stripe\Admin
 * @unreleased
 */
class AccountManagerSettingField {

	/**
	 * @var AccountDetail
	 */
	private $accountDetailRepository;

	/**
	 * @var array
	 */
	private $stripeAccounts;

	/**
	 * @var string
	 */
	private $defaultStripeAccountId;

	/**
	 * AccountManagerSettingField constructor.
	 *
	 * @unreleased
	 *
	 * @param AccountDetail $accountDetailRepository
	 */
	public function __construct( AccountDetail $accountDetailRepository ) {
		$this->accountDetailRepository = $accountDetailRepository;
	}

	/**
	 * @unreleased
	 */
	private function setUpProperties() {
		$this->stripeAccounts         = $this->accountDetailRepository->getAllStripeAccounts();
		$this->defaultStripeAccountId = $this->accountDetailRepository->getDefaultStripeAccountId();
	}

	/**
	 * Render Stripe account manager setting field.
	 *
	 * @unreleased
	 *
	 * @param array $field
	 */
	public function handle( $field ) {
		$this->setUpProperties();
		$classes = ! empty( $field['wrapper_class'] ) ? esc_attr( $field['wrapper_class'] ) : ''
		?>
		<div class="<?php echo $classes; ?>">

			<div id="give-stripe-account-manager-errors"></div>
			<?php $this->getIntroductionSectionMarkup(); ?>
			<div class="give-stripe-account-manager-container">
				<div class="main-heading">
					<h2 class="give-stripe-setting-heading"><?php esc_html_e( 'Connected Accounts', 'give' ); ?></h2>
				</div>
				<?php
				$this->getStripeAccountListSectionMarkup();
				$this->getAddNewStripeAccountSectionMarkup();
				?>

			</div>
			<?php $this->getDefaultStripeAccountNotice(); ?>
		</div>
		<?php
	}

	/**
	 * @unreleased
	 */
	private function getIntroductionSectionMarkup() {
		// Show introduction content only on global setting edit screen.
		if ( ! $this->isGlobalSettingPage() ) {
			return;
		}
		?>
		<div id="give-stripe-account-manager-description">
			<h2><?php esc_html_e( 'Manage your Stripe Accounts', 'give' ); ?></h2>

			<?php if ( give_stripe_is_premium_active() ) : ?>
				<div class="give-stripe-pro-badge">
					<div class="give-tooltip" data-tooltip="
					<?php
					esc_html_e(
						'You are using the Pro version of the GiveWP add-on which includes additional payment methods, zero additional fees, and premium support.',
						'give'
					);
					?>
						">
						<span class="dashicons dashicons-yes"></span>
						<?php esc_html_e( 'Pro Version Active', 'give' ); ?>
					</div>
				</div>
			<?php endif; ?>

			<p class="give-stripe-subheading-description">
				<?php
				esc_html_e(
					'Connect to the Stripe payment gateway using this section. Multiple Stripe accounts can be connected simultaneously. All donation forms will use the "Default Account" unless configured otherwise. To specify a different Stripe account for a form, configure the settings within the "Stripe Account" tab on the individual form edit screen.',
					'give'
				);
				?>
			</p>
			<?php
			if ( $this->canShowFreeStripeVersionNotice() ) {
				$this->getFreeStripeVersionNoticeMarkup();
			}
			?>
			<hr style="margin: 25px 0; display: block" />
		</div>
		<?php
	}

	/**
	 * @unreleased
	 */
	private function getStripeAccountListSectionMarkup() {
		$this->getStripeAccountOnBoardingModalMarkup();
		if ( ! $this->stripeAccounts ) :
			$this->getNoStripeAccountMarkup();

			return;
		endif;
		?>

		<div class="give-stripe-account-manager-list">
			<?php
			foreach ( $this->stripeAccounts as $slug => $details ) {
				$this->getStripeAccountMarkup( $slug, $details );
			}
			?>
		</div>
		<?php
	}

	/**
	 * @unreleased
	 */
	private function getAddNewStripeAccountSectionMarkup() {
		if ( $this->canShowCompatibilityNotice() ) {
			$this->getCompatibilityNoticeMarkup();

			return;
		}
		?>
		<div class="give-stripe-account-manager-add-section<?php echo give_stripe_is_premium_active() ? '  give-settings-premium-active' : ''; ?>">

			<div class="stripe-logo-with-circle">
				<svg width="21" height="31" viewBox="0 0 21 31" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M8.41683 9.55871C8.41683 8.29941 9.4501 7.81507 11.1614 7.81507C13.6155 7.81507 16.7153 8.55773 19.1693 9.8816V2.29355C16.4892 1.22799 13.8415 0.808228 11.1614 0.808228C4.60666 0.808228 0.247559 4.23093 0.247559 9.94618C0.247559 18.8581 12.5176 17.4374 12.5176 21.2798C12.5176 22.7652 11.226 23.2495 9.4178 23.2495C6.73777 23.2495 3.31507 22.1517 0.602744 20.6663V28.3513C3.60568 29.6428 6.6409 30.1918 9.4178 30.1918C16.134 30.1918 20.7515 26.8659 20.7515 21.0861C20.7192 11.4638 8.41683 13.1751 8.41683 9.55871Z" fill="#6772E5" />
				</svg>
			</div>
			<h3 class="give-stripe-heading"><?php esc_html_e( 'Add a New Stripe Account', 'give' ); ?></h3>

			<div class="give-setting-tab-body-gateways">

				<?php

				// Output Stripe Connect Button.
				echo $this->getStripeConnectButtonMarkup();

				// Check if premium is active.
				if ( give_stripe_is_premium_active() ) {
					/**
					 * This action hook will be used to load Manual API fields for premium addon.
					 *
					 * @param array $this- >stripeAccounts  All Stripe accounts.
					 *
					 * @since 2.7.0
					 *
					 */
					do_action( 'give_stripe_premium_manual_api_fields', $this->stripeAccounts );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * @unreleased
	 *
	 * @param string $stripeAccountId
	 * @param array  $stripeAccount
	 */
	private function getStripeAccountMarkup( $stripeAccountId, $stripeAccount ) {

		$account_name       = $stripeAccount['account_name'];
		$account_email      = $stripeAccount['account_email'];
		$stripe_account_id  = $stripeAccount['account_id'];
		$disconnect_message = esc_html__( 'Are you sure you want to disconnect this Stripe account?', 'give' );
		$disconnect_url     = add_query_arg(
			[
				'post_type'                   => 'give_forms',
				'page'                        => 'give-settings',
				'tab'                         => 'gateways',
				'section'                     => 'stripe-settings',
				'give_action'                 => ( 'connect' === $stripeAccount['type'] )
					? 'disconnect_connected_stripe_account'
					: 'disconnect_manual_stripe_account',
				'give_stripe_disconnect_slug' => $stripeAccountId,
			],
			wp_nonce_url( admin_url( 'edit.php' ), 'give_disconnect_connected_stripe_account_' . $stripeAccountId )
		);

		$class = $stripeAccountId === $this->defaultStripeAccountId ? 'give-stripe-boxshadow-option-wrap__selected' : '';
		?>
		<div
			id="give-stripe-<?php echo $stripeAccountId; ?>"
			class="give-stripe-account-manager-list-item give-stripe-boxshadow-option-wrap <?php echo $class; ?>"
		>
			<?php if ( $stripeAccountId === $this->defaultStripeAccountId ) : ?>
				<div class="give-stripe-account-default-checkmark">
					<svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M32.375 16.1875C32.375 25.1276 25.1276 32.375 16.1875 32.375C7.24737 32.375 0 25.1276 0 16.1875C0 7.24737 7.24737 0 16.1875 0C25.1276 0 32.375 7.24737 32.375 16.1875ZM14.3151 24.7586L26.3252 12.7486C26.733 12.3407 26.733 11.6795 26.3252 11.2717L24.8483 9.79474C24.4404 9.38686 23.7792 9.38686 23.3713 9.79474L13.5766 19.5894L9.00371 15.0165C8.59589 14.6086 7.93462 14.6086 7.52673 15.0165L6.04982 16.4934C5.642 16.9012 5.642 17.5625 6.04982 17.9703L12.8381 24.7586C13.246 25.1665 13.9072 25.1665 14.3151 24.7586Z" fill="#69B868" />
					</svg>
				</div>
			<?php endif; ?>

			<div class="give-stripe-account-fieldset give-stripe-account-name">
				<span class="give-stripe-label"><?php _e( 'Account name:', 'give' ); ?></span>
				<span class="give-stripe-connect-data-field">
						<?php echo esc_html( $account_name ); ?>
					</span>
			</div>

			<?php if ( ! empty( $account_email ) ) : ?>
				<div class="give-stripe-account-fieldset give-stripe-account-email">
					<span class="give-stripe-label"><?php _e( 'Account email:', 'give' ); ?></span>
					<div class="give-stripe-connect-data-field">
						<?php echo esc_html( $account_email ); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $stripe_account_id ) ) : ?>
				<div class="give-stripe-account-fieldset give-stripe-account-id">
					<span class="give-stripe-label"><?php _e( 'Account ID:', 'give' ); ?></span>
					<div class="give-stripe-connect-data-field">
						<?php echo esc_html( $stripe_account_id ); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="give-stripe-account-fieldset give-stripe-connection-method">
					<span class="give-stripe-label">
						<?php esc_html_e( 'Connection Method:', 'give' ); ?>
					</span>
				<div class="give-stripe-connect-data-field">
					<?php echo give_stripe_connection_type_name( $stripeAccount['type'] ); ?>
				</div>
			</div>

			<?php if ( 'connect' !== $stripeAccount['type'] ) : ?>
				<div class="give-stripe-account-fieldset give-stripe-account-edit">
					<a class="give-stripe-account-edit-name" href="#">
						<?php esc_html_e( 'Edit', 'give' ); ?>
					</a>
					<a
						class="give-stripe-account-update-name give-hidden"
						href="#"
						data-account="<?php echo $stripeAccountId; ?>"
					><?php esc_html_e( 'Update', 'give' ); ?></a>
					<a class="give-stripe-account-cancel-name give-hidden" href="#">
						<?php esc_html_e( 'Cancel', 'give' ); ?>
					</a>
				</div>
			<?php endif; ?>

			<div class="give-stripe-account-fieldset">
				<span class="give-stripe-label"><?php esc_html_e( 'Connection Status:', 'give' ); ?></span>
				<div class="give-stripe-account-actions">
					<span class="give-stripe-account-connected give-stripe-connect-data-field">
						<?php esc_html_e( 'Connected', 'give' ); ?>
					</span>
					<?php if ( $stripeAccountId !== $this->defaultStripeAccountId ) : ?>
						<span class="give-stripe-account-disconnect">
							<a
								class="give-stripe-disconnect-account-btn"
								href="<?php echo $disconnect_url; ?>"
								data-disconnect-message="<?php echo $disconnect_message; ?>"
								data-account="<?php echo $stripeAccountId; ?>"
							><span class="dashicons dashicons-editor-unlink"></span><?php esc_html_e( 'Remove', 'give' ); ?></a>
						</span>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $stripeAccountId === $this->defaultStripeAccountId ) : ?>
				<div class="give-stripe-account-badge">
					<?php esc_html_e( 'Default Account', 'give' ); ?>
				</div>
			<?php else : ?>
				<div class="give-stripe-account-default">
					<a
						data-account="<?php echo $stripeAccountId; ?>"
						class="give-stripe-account-set-default"
						href="#"
					><?php esc_html_e( 'Set as Default', 'give' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * @unreleased
	 */
	private function getStripeAccountOnBoardingModalMarkup() {
		$site_url            = get_site_url();
		$modal_title         = sprintf(
			'<strong>%1$s</strong>',
			esc_html__(
				'You are connected! Now this is important: Please configure your Stripe webhook to finalize the setup.',
				'give'
			)
		);
		$modal_first_detail  = sprintf(
			'%1$s %2$s',
			esc_html__(
				'In order for Stripe to function properly, you must add a new Stripe webhook endpoint. To do this please visit the <a href=\'https://dashboard.stripe.com/webhooks\' target=\'_blank\'>Webhooks Section of your Stripe Dashboard</a> and click the <strong>Add endpoint</strong> button and paste the following URL:',
				'give'
			),
			"<strong>{$site_url}?give-listener=stripe</strong>"
		);
		$modal_second_detail = esc_html__(
			'Stripe webhooks are required so GiveWP can communicate properly with the payment gateway to confirm payment completion, renewals, and more.',
			'give'
		);
		$can_display         = ! empty( $_GET['stripe_account'] ) ? '0' : '1';
		?>
		<div
			id="give-stripe-connected"
			class="stripe-btn-disabled give-hidden"
			data-status="connected"
			data-title="<?php echo $modal_title; ?>"
			data-first-detail="<?php echo $modal_first_detail; ?>"
			data-second-detail="<?php echo $modal_second_detail; ?>"
			data-display="<?php echo $can_display; ?>"
			data-redirect-url="<?php echo esc_url_raw( admin_url( 'edit.php?post_type=give_forms&page=give-settings&tab=gateways&section=stripe-settings' ) ); ?>"
		>
		</div>
		<?php
	}

	/**
	 * @unreleased
	 */
	private function getCompatibilityNoticeMarkup() {
		?>
		<div class="give-stripe-account-manager-add-section">
			<?php
			Give()->notices->print_admin_notices(
				[
					'description' => sprintf(
						'%1$s <a href="%2$s">%3$s</a> %4$s',
						esc_html__(
							'Give 2.7.0 introduces the ability to connect a single site to multiple Stripe accounts. To use this feature, you need to complete database updates. ',
							'give'
						),
						esc_url( admin_url( 'edit.php?post_type=give_forms&page=give-updates' ) ),
						esc_html__( 'Click here', 'give' ),
						esc_html__( 'to complete your pending database updates.', 'give' )
					),
					'dismissible' => false,
				]
			);
			?>
		</div>
		<?php
	}

	/**
	 * @unreleased
	 */
	private function getFreeStripeVersionNoticeMarkup() {
		?>
		<p class="give-stripe-subheading-description">
			<?php
			printf(
				__(
					'NOTE: You are using the free Stripe payment gateway integration. This includes an additional 2%% fee for processing one-time donations. This fee is removed by activating the premium <a href="%1$s" target="_blank">Stripe add-on</a> and never applies to subscription donations made through the <a href="%2$s" target="_blank">Recurring Donations add-on</a>. <a href="%3$s" target="_blank">Learn More ></a>',
					'give'
				),
				esc_url( 'http://docs.givewp.com/settings-stripe-addon' ),
				esc_url( 'http://docs.givewp.com/settings-stripe-recurring' ),
				esc_url( 'http://docs.givewp.com/settings-stripe-free' )
			);
			?>
		</p>
		<?php
	}

	/**
	 * @unreleased
	 */
	public function getNoStripeAccountMarkup() {
		?>
		<div class="no-stripe-account-connected">
			<div class="no-stripe-account-connected-inner">
				<span class="dashicons dashicons-info"></span>
				<h3 class="give-stripe-settings-heading"><?php esc_html_e( 'No Stripe Accounts Connected', 'give' ); ?></h3>
				<p id="give-stripe-connect-invite"><?php esc_html_e( 'Connect an account to get started!', 'give' ); ?></p>
				<?php echo $this->getStripeConnectButtonMarkup(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * @unreleased
	 * @return string
	 */
	public function getStripeConnectButtonMarkup() {
		// Prepare Stripe Connect URL.
		$link = add_query_arg(
			[
				'stripe_action'         => 'connect',
				'mode'                  => give_is_test_mode() ? 'test' : 'live',
				'return_url'            => ! $this->isGlobalSettingPage() ?
					rawurlencode(
						sprintf(
							admin_url( 'post.php?post=%d&action=edit&give_tab=stripe_manage_accounts_option' ),
							get_the_ID()
						)
					) :
					rawurlencode( admin_url( 'edit.php?post_type=give_forms&page=give-settings&tab=gateways&section=stripe-settings' ) ),
				'website_url'           => get_bloginfo( 'url' ),
				'give_stripe_connected' => '0',
			],
			esc_url_raw( 'https://connect.givewp.com/stripe/connect.php' )
		);

		$stripeSvgIcon = '<svg width="15" height="21" viewBox="0 0 15 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M6.05469 6.55469C6.05469 5.69531 6.75781 5.34375 7.92969 5.34375C9.64844 5.34375 11.7969 5.85156 13.4766 6.78906V1.55469C11.6406 0.8125 9.80469 0.5 7.92969 0.5C3.4375 0.5 0.429688 2.88281 0.429688 6.82812C0.429688 13 8.86719 11.9844 8.86719 14.6406C8.86719 15.6953 7.96875 16.0078 6.75781 16.0078C4.88281 16.0078 2.5 15.2656 0.664062 14.25V19.25C2.5 20.0703 4.57031 20.5 6.75781 20.5391C11.3672 20.5391 14.5703 18.5469 14.5703 14.5234C14.5703 7.88281 6.05469 9.05469 6.05469 6.55469Z" fill="white"/>
</svg>
';

		return sprintf(
			'<a href="%1$s" class="give-stripe-connect" title="%2$s"><span class="stripe-logo">%3$s</span><span>%2$s</span></a>',
			esc_url( $link ),
			esc_html__( 'Connect with Stripe', 'give' ),
			$stripeSvgIcon
		);
	}

	/**
	 * @unreleased
	 */
	private function getDefaultStripeAccountNotice() {
		?>
		<div class="give-stripe-default-account-notice">
			<span class="dashicons dashicons-info"></span>
			<div class="give-stripe-default-account-notice__inner">
				<p class="give-stripe-default-account-notice__bold"><strong><?php esc_html_e( 'All payments go to the default account.', 'give' ); ?></strong></p>
				<p>
					<?php
						echo $this->isGlobalSettingPage() ?
							esc_html__( 'You can set this globally (for all donation forms) here or override the setting per donation form.', 'give' ) :
							esc_html__( 'All payments go to the default account. You can set this globally (for all donation forms) or per donation form.', 'give' );
					?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * @unreleased
	 * @return bool
	 */
	private function canShowFreeStripeVersionNotice() {
		return ! give_stripe_is_premium_active();
	}

	/**
	 * @return bool
	 */
	private function canShowCompatibilityNotice() {
		return ! give_has_upgrade_completed( 'v270_store_stripe_account_for_donation' );
	}

	/**
	 * @unreleased
	 * @return bool
	 */
	private function isGlobalSettingPage() {
		return Give_Admin_Settings::is_setting_page( 'gateways', 'stripe-settings' );
	}
}
