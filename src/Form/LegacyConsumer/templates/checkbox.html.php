<?php /** @var Give\Framework\FieldsAPI\Checkbox $field */ ?>
<label class="give-label">
	<input
		type="checkbox"
		name="<?php echo $field->getName(); ?>"
		<?php echo $field->isRequired() ? 'required' : ''; ?>
		<?php echo $field->isChecked() ? 'checked' : ''; ?>
		<?php echo $field->isReadOnly() ? 'readonly' : ''; ?>
		<?php
		if ( $conditions = $field->getVisibilityConditions() ) {
			$conditions = esc_attr( json_encode( $conditions ) );
			echo "data-field-visibility-conditions=\"$conditions\"";
		}
		?>
	>
	<?php echo $field->getLabel(); ?>
</label>
