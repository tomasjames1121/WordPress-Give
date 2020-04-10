<?php

/**
 * Handle basic setup of form template
 *
 * @package Give
 * @since   2.7.0
 */

namespace Give\Form;

use Give\Form\Template\Options;
use function Give\Helpers\Form\Utils\createFailedPageURL;

defined( 'ABSPATH' ) || exit;

/**
 * Template class.
 *
 * @since 2.7.0
 */
abstract class Template {
	/**
	 * Flag to check whether or not open success page in iframe.
	 *
	 * @var bool $openSuccessPageInIframe If set to false then success page will open in window instead of iframe.
	 */
	public $openSuccessPageInIframe = true;

	/**
	 * Flag to check whether or not open failed page in iframe.
	 *
	 * @var bool $openFailedPageInIframe If set to false then failed page will open in window instead of iframe.
	 */
	public $openFailedPageInIframe = true;

	/**
	 * Donation level display style
	 *
	 * @var string $donationLevelsDisplayStyle
	 */
	public $donationLevelsDisplayStyle = 'buttons';

	/**
	 * Donation form display style.
	 *
	 * @var string $donationFormDisplayStyle
	 */
	public $donationFormDisplayStyle = 'onpage';

	/**
	 * Flag to check whether or not enable float label feature.
	 *
	 * @var string $enableFloatLabels
	 */
	public $enableFloatLabels = 'disabled';

	/**
	 * Continue to donation form button label.
	 *
	 * @var string $continueToDonationFormLabel
	 */
	public $continueToDonationFormLabel = '';

	/**
	 * Donation now button title.
	 *
	 * @var string $donateNowButtonLabel
	 */
	public $donateNowButtonLabel = '';

	/**
	 * Flag to check whether or not show donation form introduction text.
	 *
	 * @var string $showDonationIntroductionContent
	 */
	public $showDonationIntroductionContent = 'disabled';

	/**
	 * Donation introduction content position.
	 *
	 * @var string $donationIntroductionContentPosition
	 */
	public $donationIntroductionContentPosition = '';

	/**
	 * Donation introduction content.
	 *
	 * @var string $donationIntroductionContent
	 */
	public $donationIntroductionContent = '';

	/**
	 * template vs file array
	 *
	 * @since 2.7.0
	 * @var array
	 */
	public $templates = [
		'form'                => GIVE_PLUGIN_DIR . 'src/Views/Form/defaultFormTemplate.php',
		'receipt'             => GIVE_PLUGIN_DIR . 'src/Views/Form/defaultFormReceiptTemplate.php',
		'donation-processing' => GIVE_PLUGIN_DIR . 'src/Views/Form/defaultFormDonationProcessing.php',
	];

	/**
	 * Template constructor.
	 */
	public function __construct() {
		$this->donateNowButtonLabel        = __( 'Donate Now', 'give' );
		$this->continueToDonationFormLabel = __( 'Donate Now', 'give' );
	}

	/**
	 * return form template ID.
	 *
	 * @return string
	 * @since 2.7.0
	 */
	abstract public function getID();

	/**
	 * Get form template name.
	 *
	 * @return string
	 * @since 2.7.0
	 */
	abstract public function getName();

	/**
	 * Get form template image.
	 *
	 * @return string
	 * @since 2.7.0
	 */
	abstract public function getImage();

	/**
	 * Get options config
	 *
	 * @return array
	 * @since 2.7.0
	 */
	abstract public function getOptionsConfig();


	/**
	 * Template template manager get template according to view.
	 * Note: Do not forget to call this function before close bracket in overridden getTemplate method
	 *
	 * @param string $template
	 *
	 * @return string
	 * @since 2.7.0
	 */
	public function getView( $template ) {
		return $this->templates[ $template ];
	}


	/**
	 * Get form template options
	 *
	 * @return Options
	 */
	public function getOptions() {
		return Options::fromArray( $this->getOptionsConfig() );
	}

	/**
	 * Get failed/cancelled donation message.
	 *
	 * @return string
	 * @since 2.7.0
	 */
	public function getFailedDonationMessage() {
		return esc_html__( 'We\'re sorry, your donation failed to process. Please try again or contact site support.', 'give' );
	}


	/**
	 * Get failed donation page URL.
	 *
	 * @param int $formId
	 *
	 * @return mixed
	 * @since 2.7.0
	 */
	public function getFailedPageURL( $formId ) {
		return createFailedPageURL( Give()->routeForm->getURL( get_post_field( 'post_name', $formId ) ) );
	}
}
