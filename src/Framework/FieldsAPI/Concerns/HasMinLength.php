<?php

namespace Give\Framework\FieldsAPI\Concerns;

use Give\Framework\Validation\Rules\Min;

/**
 * @unreleased update to new validation system
 * @since 2.14.0
 */
trait HasMinLength
{
    /**
     * Set the value’s minimum length.
     *
     * @unreleased update to new validation system
     * @since 2.14.0
     */
    public function minLength(int $minLength): self
    {
        if ( $this->hasRule('min') ) {
            /** @var Min $rule */
            $rule = $this->getRule('min');
            $rule->size($minLength);
        }

        $this->rules("min:$minLength");

        return $this;
    }

    /**
     * Get the value’s minimum length.
     *
     * @unreleased update to use the new validation system
     * @since 2.14.0
     *
     * @return int|null
     */
    public function getMinLength()
    {
        $rule = $this->getRule('min');

        return $rule instanceof Min ? $rule->getSize() : null;
    }
}
