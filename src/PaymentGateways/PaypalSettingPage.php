<?php
namespace  Give\PaymentGateways;

use Give\PaymentGateways\PayPalCheckout\PayPalCheckout;
use Give\PaymentGateways\PayPalCheckout\AdminSettingFields;
use Give\PaymentGateways\PayPalStandard\PayPalStandard;
use function give_get_current_setting_section as getCurrentSettingSection;

/**
 * Class PaypalSettingSection
 * @package Give\PaymentGateways
 *
 * @sicne 2.8.0
 */
class PaypalSettingPage implements SettingPage {
	/**
	 * @var PayPalCheckout
	 */
	private $paypalCheckout;

	/**
	 * @var PayPalStandard
	 */
	private $paypalStandard;

	/**
	 * Register properties
	 *
	 * @param  PayPalCheckout  $paypalCheckout
	 * @param  PayPalStandard  $paypalStandard
	 *
	 * @since 2.8.0
	 */
	public function __construct( PayPalCheckout $paypalCheckout, PayPalStandard $paypalStandard ) {
		$this->paypalCheckout = $paypalCheckout;
		$this->paypalStandard = $paypalStandard;
	}

	/**
	 * @inheritDoc
	 */
	public function boot() {
		add_action( 'give_get_groups_paypal', [ $this, 'getGroups' ] );
		add_filter( 'give_get_settings_gateways', [ $this, 'registerPaypalSettings' ] );
		add_filter( 'give_get_sections_gateways', [ $this, 'registerPaypalSettingSection' ] );

		// Load custom setting fields.
		$adminSettingFields = new AdminSettingFields();
		$adminSettingFields->boot();

	}

	/**
	 * @inheritDoc
	 */
	public function getId() {
		return 'paypal';
	}

	/**
	 * @inheritDoc
	 */
	public function getName() {
		return esc_html__( 'PayPal', 'give' );
	}

	/**
	 * @inheritDoc
	 */
	public function getSettings() {
		$settings[ $this->paypalCheckout->getId() ] = $this->paypalCheckout->getOptions();
		$settings[ $this->paypalStandard->getId() ] = $this->paypalStandard->getOptions();

		return $settings;
	}

	/**
	 * Get groups.
	 *
	 * @since 2.8.0
	 *
	 * @return array
	 */
	public function getGroups() {
		return [
			$this->paypalCheckout->getId() => $this->paypalCheckout->getName(),
			$this->paypalStandard->getId() => $this->paypalStandard->getName(),
		];
	}

	/**
	 * Register settings.
	 *
	 * @param array $settings
	 *
	 * @since 2.8.0
	 *
	 * @return array
	 */
	public function registerPaypalSettings( $settings ) {
		$currentSection = getCurrentSettingSection();

		return $currentSection === $this->getId() ?
			$this->getSettings() :
			$settings;
	}

	/**
	 * Register setting section.
	 *
	 * @param array $sections
	 *
	 * @since 2.8.0
	 *
	 * @return array
	 */
	public function registerPaypalSettingSection( $sections ) {
		$sections[ $this->getId() ] = $this->getName();

		return $sections;
	}
}
