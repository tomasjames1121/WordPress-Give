<?php
/**
 * Give Meta Box Functions
 *
 * @author      WordImpress
 * @category    Core
 * @version     1.8
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Check if field callback exist or not.
 *
 * @param $field
 * @return bool|string
 */
function give_is_field_callback_exist( $field ) {
	return ( give_get_field_callback( $field ) ? true : false );
}

/**
 * Get field callback.
 *
 * @param $field
 * @return bool|string
 */
function give_get_field_callback( $field ){
	$func_name_prefix = 'give';
	$func_name = '';

	// Set callback function on basis of cmb2 field name.
	switch( $field['type'] ) {
		case 'radio_inline':
			$func_name              = "{$func_name_prefix}_radio";
			$field['wrapper_class'] = 'give-inline-radio-fields';
			//$field['name']          = $field['id'];
			break;

		case 'text':
		case 'text-medium':
		case 'text-small' :
		case 'text_small' :
			$field['type'] = 'text';
			$func_name = "{$func_name_prefix}_text_input";
			break;


		case 'textarea' :
			$func_name = "{$func_name_prefix}_textarea_input";
			break;

		case 'colorpicker' :
			$func_name      = "{$func_name_prefix}_{$field['type']}";
			$field['type'] = 'text';
			$field['class'] = 'give-colorpicker';
			break;

		case 'group' :
			$func_name = "{$func_name_prefix}_repeater_fields";
			break;

		default:
			$func_name = "{$func_name_prefix}_{$field['type']}";
	}

	$func_name = apply_filters( 'give_setting_callback', $func_name, $field );

	// Check if render callback exist or not.
	if ( !  function_exists( "$func_name" ) || empty( $func_name ) ){
		return false;
	}

	return apply_filters( 'give_setting_callback', $func_name, $field );
}

/**
 * This function add backward compatibility to render cmb2 type field type
 *
 * @param  array $field Field argument array.
 * @return bool
 */
function give_render_field( $field ) {
	$func_name = give_get_field_callback( $field );

	// Check if render callback exist or not.
	if ( ! $func_name ){
		return false;
	}

	// Set field params on basis of cmb2 field name.
	switch( $field['type'] ) {
		case 'radio_inline':
			$field['wrapper_class'] = 'give-inline-radio-fields';
			break;

		case 'text':
		case 'text-medium':
		case 'text-small' :
		case 'text_small' :
			$field['type'] = 'text';
			break;

		case 'colorpicker' :
			$field['type'] = 'text';
			$field['class'] = 'give-colorpicker';
	}

	// Add support to define field description by desc & description param.
	$field['description'] = ( ! empty( $field['description'] )
		? $field['description']
		: ( ! empty( $field['desc'] ) ? $field['desc'] : '' ) );

	// Call render function.
	$func_name( $field );

	return true;
}

/**
 * Output a text input box.
 *
 * @param array $field
 */
function give_text_input( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
	$data_type              = empty( $field['data_type'] ) ? '' : $field['data_type'];

	switch ( $data_type ) {
		case 'price' :
			$field['class'] .= ' give_input_price';
			$field['value']  = give_format_localized_price( $field['value'] );
			break;
		case 'decimal' :
			$field['class'] .= ' give_input_decimal';
			$field['value']  = give_format_localized_decimal( $field['value'] );
			break;
		case 'stock' :
			$field['class'] .= ' give_input_stock';
			$field['value']  = give_stock_amount( $field['value'] );
			break;
		case 'url' :
			$field['class'] .= ' give_input_url';
			$field['value']  = esc_url( $field['value'] );
			break;

		default :
			break;
	}

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ){
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['name'] ) . '</label><input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}
	echo '</p>';
}

/**
 * Output a hidden input box.
 *
 * @param array $field
 */
function give_hidden_input( $field ) {
	global $thepostid, $post;

	$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['class'] = isset( $field['class'] ) ? $field['class'] : '';

	echo '<input type="hidden" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) .  '" /> ';
}

/**
 * Output a textarea input box.
 *
 * @param array $field
 */
function give_textarea_input( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ){
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['name'] ) . '</label><textarea class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '"  name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="10" cols="20" ' . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $field['value'] ) . '</textarea> ';

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}
	echo '</p>';
}

/**
 * Output a wysiwyg.
 *
 * @param array $field
 */
function give_wysiwyg( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ){
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<div class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['name'] ) . '</label>';

	wp_editor(
		$field['value'],
		$field['id'],
		array(
			'textarea_name' => esc_attr( $field['id'] ),
			'editor_class'  => esc_attr( $field['class'] ),
			'textarea_rows' => '10',
			'editor_css'    => esc_attr( $field['style'] ),
		)
	);

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}
	echo '</div>';
}

/**
 * Output a checkbox input box.
 *
 * @param array $field
 */
function give_checkbox( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'checkbox';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['cbvalue']       = isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes';
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ){
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['name'] ) . '</label><input type="checkbox" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['cbvalue'] ) . '" ' . checked( $field['value'], $field['cbvalue'], false ) . '  ' . implode( ' ', $custom_attributes ) . '/> ';

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</p>';
}

/**
 * Output a select input box.
 *
 * @param array $field
 */
function give_select( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ){
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['name'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" ' . implode( ' ', $custom_attributes ) . '>';

	foreach ( $field['options'] as $key => $value ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
	}

	echo '</select> ';

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}
	echo '</p>';
}

/**
 * Output a radio input box.
 *
 * @param array $field
 */
function give_radio( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class']         = isset( $field['class'] ) ? $field['class'] : '';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];

	echo '<fieldset class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><legend>' . wp_kses_post( $field['name'] ) . '</legend><ul class="give-radios">';

	foreach ( $field['options'] as $key => $value ) {

		echo '<li><label><input
				name="' . esc_attr( $field['id'] ) . '"
				value="' . esc_attr( $key ) . '"
				type="radio"
				class="' . esc_attr( $field['class'] ) . '"
				style="' . esc_attr( $field['style'] ) . '"
				' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
				/> ' . esc_html( $value ) . '</label>
		</li>';
	}
	echo '</ul>';

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</fieldset>';
}

/**
 * Output a colorpicker.
 *
 * @param array $field
 */
function give_colorpicker( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ){
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['name'] ) . '</label><input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

	if ( ! empty( $field['description'] ) ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}
	echo '</p>';
}

/**
 * Output a colorpicker.
 *
 * @param array $field
 */
function give_default_gateway( $field ) {
	global $thepostid, $post;

	// get all active payment gateways.
	$gateways = give_get_enabled_payment_gateways();

	// Set field option value.
	foreach ( $gateways as $key => $option ) {
		$field['options'][ $key ] = $option['admin_label'];
	}

	//Add a field to the Give Form admin single post view of this field
	if ( is_object( $post ) &&  'give_forms' === $post->post_type ) {
		$field['options'] = array_merge( array( 'global' => esc_html__( 'Global Default', 'give' ) ), $field['options'] );
	}

	// Render select field.
	give_select( $field );
}

/**
 * Output a colorpicker.
 *
 * @param array $fields
 */
//function give_repeater_fields( $fields ) {
//	?>
<!--	<div class="give-repeater-fields-wrap">-->
<!--		<div class="give-repeater-block">-->
<!--			--><?php //foreach ( $fields['fields'] as $field ) : ?>
<!--				--><?php //if ( ! give_is_field_callback_exist( $field ) ) continue; ?>
<!---->
<!--				<div class="give-field-wrap">-->
<!--					--><?php //give_render_field( $field ); ?>
<!--					<div class="give-field-row-actions">-->
<!--						<a class="give-plus" href="#" data-event="add-row" title="--><?php //echo esc_html__( 'Add row', 'give' ); ?><!--">+</a>-->
<!--						<a class="give-minus" href="#" data-event="remove-row" title="--><?php //echo esc_html__( 'Add row', 'give' ); ?><!--">-</a>-->
<!--					</div>-->
<!--				</div>-->
<!--			--><?php //endforeach; ?>
<!--		</div>-->
<!---->
<!--		<div class="give-repeater-block-actions">-->
<!--			<a class="acf-button button button-primary" data-event="add-row">--><?php //echo esc_html__( 'Add Row', 'give' ); ?><!--</a>-->
<!--		</div>-->
<!--	</div>-->
<!--	--><?php
//}
