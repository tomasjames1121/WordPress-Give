<?php
/**
 * The [give_donation_grid] Shortcode Generator class
 *
 * @package     Give/Admin/Shortcodes
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       2.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Give_Shortcode_Donation_Form_Goal
 */
class Give_Shortcode_Donation_Grid extends Give_Shortcode_Generator {

	/**
	 * Class constructor
	 */
	public function __construct() {

		$this->shortcode['title'] = esc_html__( 'Donation Form Grid', 'give' );
		$this->shortcode['label'] = esc_html__( 'Donation Form Grid', 'give' );

		parent::__construct( 'give_form_grid' );
	}

	/**
	 * Define the shortcode attribute fields
	 *
	 * @return array
	 */
	public function define_fields() {

		return array(
			array(
				'type' => 'container',
				'html' => sprintf( '<p class="strong margin-top">%s</p>', esc_html__( 'Optional settings', 'give' ) ),
			),
			array(
				'type'        => 'textbox',
				'name'        => 'ids',
				'label'       => esc_attr__( 'Form IDs:', 'give' ),
				'tooltip'     => esc_attr__( 'Enter a comma-separated list of form IDs. If empty, all published forms are displayed.', 'give' ),
				'placeholder' => esc_html__( 'All Forms', 'give' )
			),
			array(
				'type'        => 'textbox',
				'name'        => 'exclude',
				'label'       => esc_attr__( 'Excluded Form IDs:', 'give' ),
				'tooltip'     => esc_attr__( 'Enter a comma-separated list of form IDs to exclude those from the grid.', 'give' ),
				'placeholder' => esc_html__( 'Excluded Forms', 'give' )
			),
			array(
				'type'    => 'listbox',
				'name'    => 'orderby',
				'label'   => esc_attr__( 'Order By:', 'give' ),
				'tooltip' => esc_attr__( 'Different parameter to set the order for the forms display in the form grid.', 'give' ),
				'options' => array(
					'date'             => esc_html__( 'Date Created', 'give' ),
					'name'             => esc_html__( 'Form Name', 'give' ),
					'amount_donated'   => esc_html__( 'Amount Donated', 'give' ),
					'number_donations' => esc_html__( 'Number of Donations', 'give' ),
					'menu_order'       => esc_html__( 'Menu Order', 'give' ),
					'post__in'         => esc_html__( 'Provided Form IDs', 'give' ),
				),
			),
			array(
				'type'    => 'listbox',
				'name'    => 'order',
				'label'   => esc_attr__( 'Order:', 'give' ),
				'tooltip' => esc_attr__( 'Display forms based on order.', 'give' ),
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'give' ),
					'DESC' => esc_html__( 'Descending', 'give' ),
				),
			),
			array(
				'type'        => 'listbox',
				'name'        => 'columns',
				'label'       => esc_attr__( 'Columns:', 'give' ),
				'tooltip'     => esc_attr__( 'Sets the number of forms per row.', 'give' ),
				'options'     => array(
					'1' => esc_html__( '1', 'give' ),
					'2' => esc_html__( '2', 'give' ),
					'3' => esc_html__( '3', 'give' ),
					'4' => esc_html__( '4', 'give' ),
				),
				'placeholder' => esc_html__( 'Best Fit', 'give' )
			),
			array(
				'type'    => 'listbox',
				'name'    => 'show_goal',
				'label'   => esc_attr__( 'Show Goal:', 'give' ),
				'tooltip' => __( 'Do you want to display the goal\'s progress bar?', 'give' ),
				'options' => array(
					'true'  => esc_html__( 'Show', 'give' ),
					'false' => esc_html__( 'Hide', 'give' ),
				),
			),
			array(
				'type'    => 'listbox',
				'name'    => 'show_excerpt',
				'label'   => esc_attr__( 'Show Excerpt:', 'give' ),
				'tooltip' => esc_attr__( 'Do you want to display the excerpt?', 'give' ),
				'options' => array(
					'true'  => esc_html__( 'Show', 'give' ),
					'false' => esc_html__( 'Hide', 'give' ),
				),
			),
			array(
				'type'    => 'listbox',
				'name'    => 'show_featured_image',
				'label'   => esc_attr__( 'Show Featured Image:', 'give' ),
				'tooltip' => esc_attr__( 'Do you want to display the featured image?', 'give' ),
				'options' => array(
					'true'  => esc_html__( 'Show', 'give' ),
					'false' => esc_html__( 'Hide', 'give' ),
				),
			),
			array(
				'type'    => 'listbox',
				'name'    => 'display_style',
				'label'   => esc_attr__( 'Display Style:', 'give' ),
				'tooltip' => esc_attr__( 'Show form as modal window or redirect to a new page?', 'give' ),
				'options' => array(
					'redirect'     => esc_html__( 'Redirect', 'give' ),
					'modal_reveal' => esc_html__( 'Modal', 'give' ),
				),
			),
			array(
				'type'    => 'textbox',
				'name'    => 'forms_per_page',
				'label'   => esc_attr__( 'Forms Per Page:', 'give' ),
				'tooltip' => esc_attr__( 'Sets the number of forms to display per page.', 'give' ),
				'value'   => 12,
			),
		);
	}
}

new Give_Shortcode_Donation_Grid();
