<?php
namespace Give\FormAPI;

use Give\FormAPI\Form\File;
use InvalidArgumentException;
use Give\FormAPI\Form\Colorpicker;
use Give\FormAPI\Form\Media;
use Give\FormAPI\Form\Radio;
use Give\FormAPI\Form\Text;
use Give\FormAPI\Form\Textarea;
use Give\FormAPI\Form\Wysiwyg;

class Field {
	/**
	 * Field vs class name mapping array
	 *
	 * @since 2.7.0
	 * @var array
	 */
	private $fieldClasses = [
		'text'        => Text::class,
		'textarea'    => Textarea::class,
		'file'        => File::class,
		'media'       => Media::class,
		'radio'       => Radio::class,
		'wysiwyg'     => Wysiwyg::class,
		'colorpicker' => Colorpicker::class,
	];

	/**
	 * Get field object.
	 *
	 * @since 2.7.0
	 * @param array $array
	 *
	 * @return Form\Field
	 */
	public static function fromArray( $array ) {
		$field = new static();
		$field->validate( $array );

		/**
		 * Filter the field classes
		 *
		 * @since 2.7.0
		 * @param Form\Field[]
		 */
		$field->fieldClasses = apply_filters( 'give_form_api_field_classes', $field->fieldClasses );

		/* @var Form\Field $fieldClass */
		$fieldClass = $field->fieldClasses[ $field->getFieldType( $array['type'] ) ];

		return $fieldClass::fromArray( $array );
	}

	/**
	 * Get field class name.
	 * Note: field name create with {fieldType_modifier} logic. Use underscore in field type only if you want to add a modifier. For example: text_small, radio_inline etc.
	 *
	 * @since 2.7.0
	 * @param $type
	 *
	 * @return string
	 */
	private function getFieldType( $type ) {
		if ( false !== strpos( $type, '_' ) ) {
			$type = current( explode( '_', $type, 2 ) );
		}

		return $type;
	}

	/**
	 * Validate field arguments
	 *
	 * @since 2.7.0
	 *
	 * @param array $array
	 *
	 * @throws InvalidArgumentException
	 */
	private function validate( $array ) {
		$required = array( 'id', 'name', 'type' );
		$array    = array_filter( $array ); // Remove empty values.

		if ( array_diff( $required, array_keys( $array ) ) ) {
			throw new InvalidArgumentException( __( 'To create a TextField object, please provide valid id, name and type.', 'give' ) );
		}
	}
}
