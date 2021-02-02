<?php

namespace Give\Log\ValueObjects;

/**
 * Class LogCategory
 * @package Give\Log\ValueObjects
 *
 * @since 2.9.7
 *
 * @method static CORE()
 * @method static WARNING()
 * @method static NOTICE()
 */
class LogCategory extends Enum {
	const CORE      = 'Core';
	const PAYMENT   = 'Payment';
	const MIGRATION = 'Migration';

	/**
	 * @inheritDoc
	 */
	public static function getDefault() {
		return LogCategory::CORE;
	}
}
