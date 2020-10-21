<?php
namespace Give\ValueObjects;

/**
 * Class Money
 * @package Give\ValueObjects
 *
 * @since 2.9.0
 */
class Money {
	/**
	 * @var int|string
	 */
	private $amount;

	/**
	 * @var int
	 */
	private $minorAmount;

	/**
	 * @var string
	 */
	private $currency;

	/**
	 * @var array
	 */
	private $currencyConfig;

	/**
	 * Return Money class object.
	 *
	 * @since 2.9.0
	 *
	 * @param int|string $amount Amount value without currency formatting
	 * @param string $currency
	 *
	 * @return Money
	 */
	public static function of( $amount, $currency ) {
		$object = new static();

		$object->currencyConfig = give_get_currency_formatting_settings( $currency );
		$object->amount         = $amount;
		$object->currency       = $currency;

		return $object;
	}

	/**
	 * Return Money class object.
	 *
	 * @since 2.9.0
	 *
	 * @param int|string $amount
	 * @param string $currency
	 *
	 * @return Money
	 */
	public static function ofMinor( $amount, $currency ) {
		$object = new static();

		$object->currencyConfig = give_get_currency_formatting_settings( $currency );
		$object->minorAmount    = $amount;
		$object->currency       = $currency;

		return $object;
	}

	/**
	 * Get amount in smallest unit of currency.
	 *
	 * @sicne 2.9.0
	 *
	 * @return int
	 */
	public function getMinorAmount() {
		if ( $this->minorAmount ) {
			return $this->minorAmount;
		}

		$this->minorAmount = absint( $this->amount * ( 10 ** $this->currencyConfig['number_decimals'] ) );

		return $this->minorAmount;
	}

	/**
	 * Get amount in smallest unit of currency.
	 *
	 * @sicne 2.9.0
	 *
	 * @return string
	 */
	public function getAmount() {
		if ( $this->amount ) {
			return $this->amount;
		}

		$this->amount = (string) ( $this->amount / ( 10 ** $this->currencyConfig['number_decimals'] ) );

		return $this->amount;
	}
}
