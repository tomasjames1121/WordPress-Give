<?php

/**
 * Template Loader
 *
 * @class          WC_Template
 * @version        2.2.0
 * @package        WooCommerce/Classes
 * @category       Class
 * @author         WooThemes
 */
class Give_Template_Loader {

	/**
	 * Hook in methods
	 */
	public static function init() {

		add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );


		/**
		 * Load Template Functions
		 *
		 * @see: template-functions.php
		 */

		/**
		 * Content Wrappers
		 *
		 * @see give_output_content_wrapper()
		 * @see give_output_content_wrapper_end()
		 */
		add_action( 'give_before_main_content', 'give_output_content_wrapper', 10 );
		add_action( 'give_after_main_content', 'give_output_content_wrapper_end', 10 );

		/**
		 * Before Single Forms Summary Div
		 *
		 * @see give_show_product_images()
		 */
		add_action( 'give_before_single_form_summary', 'give_show_form_images', 10 );

		/**
		 * Single Forms Summary Box
		 *
		 * @see give_template_single_title()
		 */
		add_action( 'give_single_form_summary', 'give_template_single_title', 5 );
		add_action( 'give_single_form_summary', 'give_get_donation_form', 10 );



	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. Give looks for theme
	 * overrides in /theme/give/ by default
	 *
	 * For beginners, it also looks for a give.php template first. If the user adds
	 * this to the theme (containing a give() inside) this will be used for all
	 * woocommerce templates.
	 *
	 * @param mixed $template
	 *
	 * @return string
	 */
	public static function template_loader( $template ) {
		$find = array( 'give.php' );
		$file = '';

		if ( is_single() && get_post_type() == 'give_forms' ) {

			$file   = 'single-give-form.php';
			$find[] = $file;
			$find[] = apply_filters( 'give_template_path', 'give/' ) . $file;

		}

		if ( $file ) {
			$template = locate_template( array_unique( $find ) );
			if ( ! $template ) {
				$template = GIVE_PLUGIN_DIR . '/templates/' . $file;
			}
		}

		return $template;
	}


}

Give_Template_Loader::init();
