<?php /** @var Give\Framework\FieldsAPI\File $field */ ?>
<input
	type="file"
	name="<?php echo $field->getName(); ?><?php echo $field->getAllowMultiple() ? '[]' : ''; ?>"
	id="give-<?php echo $field->getName(); ?>"
	<?php echo $field->getAllowMultiple() ? 'multiple' : ''; ?>
	<?php echo $field->isRequired() ? 'required' : ''; ?>
	<?php echo $field->isReadOnly() ? 'readonly' : ''; ?>
>
