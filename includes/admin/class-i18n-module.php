<?php

/**
 * Class Give_i18n
 */
class Give_i18n {

	/**
	 * Your translation site's URL.
	 *
	 * @var string
	 */
	private $glotpress_url;

	/**
	 * Hook where you want to show the promo box.
	 *
	 * @var string
	 */
	private $hook;

	/**
	 * Will contain the site's locale.
	 *
	 * @access private
	 * @var string
	 */
	private $locale;

	/**
	 * Will contain the locale's name, obtained from your translation site.
	 *
	 * @access private
	 * @var string
	 */
	private $locale_name;

	/**
	 * Will contain the percentage translated for the plugin translation project in the locale.
	 *
	 * @access private
	 * @var int
	 */
	private $percent_translated;


	/**
	 * Indicates whether there's a translation available at all.
	 *
	 * @access private
	 * @var bool
	 */
	private $translation_exists;

	/**
	 * Indicates whether the translation's loaded.
	 *
	 * @access private
	 * @var bool
	 */
	private $translation_loaded;

	/**
	 * Class constructor.
	 *
	 * @param array $args Contains the settings for the class.
	 */
	public function __construct( $args ) {
		if ( ! is_admin() ) {
			return;
		}

		//This plugin is en_US native.
		$this->locale = get_locale();
		if ( 'en_US' === $this->locale ) {
			return;
		}

		$this->init( $args );

		if ( ! $this->hide_promo() ) {
			add_action( $this->hook, array( $this, 'promo' ) );
		}
	}

	/**
	 * This is where you decide where to display the messages and where you set the plugin specific variables.
	 *
	 * @access private
	 *
	 * @param array $args
	 */
	private function init( $args ) {
		foreach ( $args as $key => $arg ) {
			$this->$key = $arg;
		}

	}

	/**
	 * Check whether the promo should be hidden or not.
	 *
	 * @access private
	 *
	 * @return bool
	 */
	private function hide_promo() {
		$hide_promo = get_transient( 'give_i18n_give_promo_hide' );
		if ( ! $hide_promo ) {
			if ( filter_input( INPUT_GET, 'remove_i18n_promo', FILTER_VALIDATE_INT ) === 1 ) {
				// No expiration time, so this would normally not expire, but it wouldn't be copied to other sites etc.
				set_transient( 'give_i18n_give_promo_hide', true );
				$hide_promo = true;
			}
		}

		return $hide_promo;
	}

	/**
	 * Generates a promo message.
	 *
	 * @access private
	 *
	 * @return bool|string $message
	 */
	private function promo_message() {
		$message = false;

		if ( $this->translation_exists && $this->translation_loaded && $this->percent_translated < 90 ) {
			$message = __( 'As you can see, there is a translation of this plugin in %1$s. This translation is currently %3$d%% complete. We need your help to make it complete and to fix any errors. Please register at %4$s to help complete the translation to %1$s!', 'give' );
		} else if ( ! $this->translation_loaded && $this->translation_exists ) {
			$message = __( 'You\'re using WordPress in %1$s. While %2$s has been translated to %1$s for %3$d%%, it\'s not been shipped with the plugin yet. You can help! Register at %4$s to help complete the translation to %1$s!', 'give' );
		} else if ( ! $this->translation_exists ) {
			$message = __( 'You\'re using WordPress in a language we don\'t support yet. We\'d love for %2$s to be translated in that language too, but unfortunately, it isn\'t right now. You can change that! Register at %4$s to help translate it!', 'give' );
		}

		$registration_link = sprintf( '<a href="%1$s">%2$s</a>', 'https://wordpress.org/support/register.php', esc_html__( 'WordPress.org', 'give' ) );
		$message           = sprintf( $message, esc_html( $this->locale_name ), esc_html( 'Give' ), $this->percent_translated, $registration_link );

		return $message;
	}

	/**
	 * Outputs a promo box
	 */
	public function promo() {

		$this->translation_details();
		$message = $this->promo_message();

		if ( $message ) { ?>

			<style>
				/* Banner specific styles */

			</style>
			<div id="give-i18n-notice" class="give-addon-alert updated" style="">

				<a href="https://wordpress.org/support/register.php" class="alignleft" style="margin:0" target="_blank"><span class="dashicons dashicons-translation" style="font-size: 110px; text-decoration: none;"></span></a>

				<div style="margin: 0 0 0 132px;">
					<a href="<?php echo esc_url( add_query_arg( array( 'remove_i18n_promo' => '1' ) ) ); ?>" style="color:#333;text-decoration:none;font-weight:bold;font-size:16px;padding:1px 4px;" class="alignright"><span class="dashicons dashicons-dismiss"></span></a>

					<h2 style="margin: 10px 0;"><?php _e( 'Help Translate Give', 'give' ); ?></h2>

					<p><?php echo $message; ?></p>
					<p>
						<a href="https://wordpress.org/support/register.php" target="_blank"><?php _e( 'Register now &raquo;', 'give' ); ?></a>
					</p>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Try to find the transient for the translation set or retrieve them.
	 *
	 * @access private
	 *
	 * @return object|null
	 */
	private function find_or_initialize_translation_details() {
		$set = get_transient( 'give_i18n_give_' . $this->locale );

		if ( ! $set ) {
			$set = $this->retrieve_translation_details();
			set_transient( 'give_i18n_give_' . $this->locale, $set, DAY_IN_SECONDS );
		}

		return $set;
	}

	/**
	 * Try to get translation details from cache, otherwise retrieve them, then parse them.
	 *
	 * @access private
	 */
	private function translation_details() {
		$set = $this->find_or_initialize_translation_details();

		$this->translation_exists = ! is_null( $set );
		$this->translation_loaded = is_textdomain_loaded( 'give' );

		$this->parse_translation_set( $set );
	}

	/**
	 * Retrieve the translation details from Give Translate.
	 *
	 * @access private
	 *
	 * @return object|null
	 */
	private function retrieve_translation_details() {
		$api_url = trailingslashit( $this->glotpress_url ) . 'api/projects/give';

		$resp = wp_remote_get( $api_url );
		$body = wp_remote_retrieve_body( $resp );
		unset( $resp );

		if ( $body ) {
			$body = json_decode( $body );
			foreach ( $body->translation_sets as $set ) {
				if ( ! property_exists( $set, 'wp_locale' ) ) {
					continue;
				}

				if ( $this->locale == $set->wp_locale ) {
					return $set;
				}
			}
		}

		return null;
	}

	/**
	 * Set the needed private variables based on the results from Give Translate.
	 *
	 * @param object $set The translation set
	 *
	 * @access private
	 */
	private function parse_translation_set( $set ) {
		if ( $this->translation_exists && is_object( $set ) ) {
			$this->locale_name        = $set->name;
			$this->percent_translated = $set->percent_translated;
		} else {
			$this->locale_name        = '';
			$this->percent_translated = '';
		}
	}
}

$give_i18n = new Give_i18n(
	array(
		'hook'          => 'admin_notices',
		'glotpress_url' => 'https://translate.wordpress.org/projects/wp-plugins/give',
	)
);