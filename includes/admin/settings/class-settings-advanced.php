<?php
/**
 * Give Settings Page/Tab
 *
 * @package     Give
 * @subpackage  Classes/Give_Settings_Advanced
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Give_Settings_Advanced' ) ) :

	/**
	 * Give_Settings_Advanced.
	 *
	 * @sine 1.8
	 */
	class Give_Settings_Advanced extends Give_Settings_Page {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id    = 'advanced';
			$this->label = esc_html__( 'Advanced', 'give' );

			$this->default_tab = 'advanced-options';

			parent::__construct();
		}

		/**
		 * Get settings array.
		 *
		 * @since  1.8
		 * @return array
		 */
		public function get_settings() {
			$settings = array();

			$current_section = give_get_current_setting_section();

			switch ( $current_section ) {
				case 'advanced-options':
					$settings = array(
						array(
							'id'   => 'give_title_data_control_2',
							'type' => 'title'
						),
						array(
							'name' => esc_html__( 'Remove All Data on Uninstall?', 'give' ),
							'desc' => esc_html__( 'When the plugin is deleted, completely remove all Give data.', 'give' ),
							'id'   => 'uninstall_on_delete',
							'type' => 'checkbox'
						),
						array(
							/* translators: %s: the_content */
							'name' => sprintf( __( 'Disable %s filter', 'give' ), '<code>the_content</code>' ),
							/* translators: 1: https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content 2: the_content */
							'desc' => sprintf( __( 'If you are seeing extra social buttons, related posts, or other unwanted elements appearing within your forms then you can disable WordPress\' content filter. <a href="%1$s" target="_blank">Learn more</a> about %2$s filter.', 'give' ), esc_url( 'https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content' ), '<code>the_content</code>' ),
							'id'   => 'disable_the_content_filter',
							'type' => 'checkbox'
						),
						array(
							'name' => esc_html__( 'Load Scripts in Footer?', 'give' ),
							'desc' => esc_html__( 'Check this box if you would like Give to load all frontend JavaScript files in the footer.', 'give' ),
							'id'   => 'scripts_footer',
							'type' => 'checkbox'
						),
						array(
							'id'   => 'give_title_data_control_2',
							'type' => 'sectionend'
						)
					);
					break;
			}


			/**
			 * Filter the advanced settings.
			 * Backward compatibility: Please do not use this filter. This filter is deprecated in 1.8
			 */
			$settings = apply_filters( 'give_settings_advanced', $settings );

			/**
			 * Filter the settings.
			 *
			 * @since  1.8
			 * @param  array $settings
			 */
			$settings = apply_filters( 'give_get_settings_' . $this->id, $settings );

			// Output.
			return $settings;
		}

		/**
		 * Get sections.
		 *
		 * @since 1.8
		 * @return array
		 */
		public function get_sections() {
			$sections = array(
				'advanced-options' => esc_html__( 'Advanced Options', 'give' )
			);

			return apply_filters( 'give_get_sections_' . $this->id, $sections );
		}
	}

endif;

return new Give_Settings_Advanced();
