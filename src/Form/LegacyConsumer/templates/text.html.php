<input
	type="text"
	class="give-input required"
	name="<?php echo $field->getName(); ?>"
	id="give-<?php echo $field->getName(); ?>"
	placeholder="<?php echo $field->getLabel(); ?>"
	<?php if ( $field->isRequired() ) : ?>
	required
	<?php endif; ?>
	@attributes
	/>
