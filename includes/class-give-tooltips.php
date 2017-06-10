<?php

/**
 * Class for managing tooltips
 *
 * @package     Give
 * @subpackage  Classes/Give_Tooltips
 * @copyright   Copyright (c) 2017, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       2.0
 */
class Give_Tooltips {
	/**
	 * Set tooltip arguments.
	 *
	 * @since  2.0
	 * @access private
	 *
	 * @param $args
	 *
	 * @return array
	 */
	private function set_toottip_args( $args ) {
		return wp_parse_args(
			$args,
			array(
				// Tooltip tag.
				'tag'         => 'span',
				'tag_content' => '',

				// Set to link of anchor if tooltip tag is anchor.
				'link'        => '#',

				// Text for tooltip
				'label'       => '',

				// Value: top-right, top, top-left, right, left, bottom-right, bottom, bottom-left.
				'position'    => 'top',

				// Value: error, warning, info, success.
				'status'      => '',

				// Value: small, medium, large.
				'size'        => '',

				// Value: true/false.
				'show_always' => false,

				// Value: true/false
				'round_edges' => false,

				// Value: true/false
				'animate'     => true,

				// Attributes.
				'attributes'  => array(),
			)
		);
	}


	/**
	 * Render tooltip
	 *
	 * @since  2.0
	 * @access public
	 *
	 * @param $args
	 *
	 * @return string
	 */
	public function render( $args ) {
		$args           = $this->set_toottip_args( $args );

		$tooltip_pos = array(
			'top'          => 'hint--top',
			'top-right'    => 'hint--top-right',
			'top-left'     => 'hint--top-left',
			'right'        => 'hint--right',
			'left'         => 'hint--left',
			'bottom'       => 'hint--bottom',
			'bottom-right' => 'hint--bottom-right',
			'bottom-left'  => 'hint--bottom-left',
		);

		$tooltip_status = array(
			'error'   => 'hint--error',
			'warning' => 'hint--warning',
			'info'    => 'hint--info',
			'success' => 'hint--success',
		);

		$tooltip_size = array(
			'small'  => 'hint--small',
			'medium' => 'hint--medium',
			'large'  => 'hint--large',
		);

		// Set label.
		$args['attributes']['aria-label'] = $args['label'];

		// Set classes.
		$args['attributes']['class'] = $tooltip_pos[ $args['position'] ];
		$args['attributes']['class'] .= ! empty( $args['status'] ) ? " {$tooltip_status[ $args['status'] ]}" : '';
		$args['attributes']['class'] .= ! empty( $args['size'] ) ? " {$tooltip_size[ $args['size'] ]}" : '';
		$args['attributes']['class'] .= $args['show_always'] ? ' hint--always' : '';
		$args['attributes']['class'] .= $args['round_edges'] ? ' hint--rounded' : '';
		$args['attributes']['class'] .= $args['animate'] ? ' hint--bounce' : ' hint--no-animate';

		// Set link attribute in tooltip has anchor tag.
		if ( 'a' === $args['tag'] && ! empty( $args['link'] ) ) {
			$args['attributes']['href'] = esc_url( $args['link'] );
		}

		// Set attribute string
		$attributes_str = '';
		foreach ( $args['attributes'] as $name => $val ) {
			$attributes_str .= "{$name}=\"{$val}\" ";
		}
		$attributes_str = trim( $attributes_str );

		return sprintf( '<%1$s %2$s>%3$s</%1$s>', $args['tag'], $attributes_str, $args['tag_content'] );
	}


	/**
	 * Render tooltip with anchor tag
	 *
	 * @since  2.0
	 * @access public
	 *
	 * @param array $args
	 * @param bool  $echo
	 *
	 * @return string
	 */
	function render_link( $args, $echo = false ) {
		$args['tag']    = 'a';
		$tooltip_markup = $this->render( $args );

		if ( $echo ) {
			echo $tooltip_markup;
		}

		error_log( print_r( $tooltip_markup, true ) . "\n", 3, WP_CONTENT_DIR . '/debug_new.log' );


		return $tooltip_markup;
	}

	/**
	 * Render tooltip with span tag
	 *
	 * @since  2.0
	 * @access public
	 *
	 * @param array $args
	 * @param bool  $echo
	 *
	 * @return string
	 */
	function render_span( $args, $echo = false ) {
		$args['tag']    = 'span';
		$tooltip_markup = $this->render( $args );

		if ( $echo ) {
			echo $tooltip_markup;
		}


		return $tooltip_markup;
	}
}