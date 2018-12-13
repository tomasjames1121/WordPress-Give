<?php
/**
 * Currency Functions
 *
 * @package     Give
 * @subpackage  Functions
 * @copyright   Copyright (c) 2017, GiveWP
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.8.17
 */

/**
 * Get the set currency
 *
 * @since 1.0
 * @since 1.8.15 Upgrade function to handle dynamic currency
 *
 * @param int          $donation_or_form_id Donation or Form ID
 * @param array|object $args                Additional data
 *
 * @return string The currency code
 */
function give_get_currency( $donation_or_form_id = null, $args = array() ) {

	// Get currency from donation
	if ( is_numeric( $donation_or_form_id ) && 'give_payment' === get_post_type( $donation_or_form_id ) ) {
		$currency = give_get_meta( $donation_or_form_id, '_give_payment_currency', true );

		if ( empty( $currency ) ) {
			$currency = give_get_option( 'currency', 'USD' );
		}
	} else {
		$currency = give_get_option( 'currency', 'USD' );
	}

	/**
	 * Filter the currency on basis of donation, form id, or additional data.
	 *
	 * @since 1.0
	 */
	return apply_filters( 'give_currency', $currency, $donation_or_form_id, $args );
}

/**
 * Get the set currency position
 *
 * @since 1.3.6
 *
 * @return string The currency code
 */
function give_get_currency_position() {

	$currency_pos = give_get_option( 'currency_position', 'before' );

	return apply_filters( 'give_currency_position', $currency_pos );
}

/**
 * Get Currencies List
 *
 * @since 1.8.17
 *
 * @return array $currencies A list of the available currencies
 */
function give_get_currencies_list() {
	$currencies = array(
		'USD' => array(
			'admin_label' => sprintf( __('US Dollars (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'EUR' => array(
			'admin_label' => sprintf( __('Euros (%1$s)', 'give'), '&euro;'),
			'symbol'      => '&euro;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'GBP' => array(
			'admin_label' => sprintf( __('Pounds Sterling (%1$s)', 'give'), '&pound;'),
			'symbol'      => '&pound;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'AUD' => array(
			'admin_label' => sprintf( __('Australian Dollars (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'BRL' => array(
			'admin_label' => sprintf( __('Brazilian Real (%1$s)', 'give'), '&#82;&#36;'),
			'symbol'      => '&#82;&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'CAD' => array(
			'admin_label' => sprintf( __('Canadian Dollars (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'CZK' => array(
			'admin_label' => sprintf( __('Czech Koruna (%1$s)', 'give'), '&#75;&#269;'),
			'symbol'      => '&#75;&#269;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'DKK' => array(
			'admin_label' => sprintf( __('Danish Krone (%1$s)', 'give'), '&nbsp;kr.&nbsp;'),
			'symbol'      => '&nbsp;kr.&nbsp;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'HKD' => array(
			'admin_label' => sprintf( __('Hong Kong Dollar (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'HUF' => array(
			'admin_label' => sprintf( __('Hungarian Forint (%1$s)', 'give'), '&#70;&#116;'),
			'symbol'      => '&#70;&#116;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'ILS' => array(
			'admin_label' => sprintf( __('Israeli Shekel (%1$s)', 'give'), '&#8362;'),
			'symbol'      => '&#8362;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'JPY' => array(
			'admin_label' => sprintf( __('Japanese Yen (%1$s)', 'give'), '&yen;'),
			'symbol'      => '&yen;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'MYR' => array(
			'admin_label' => sprintf( __('Malaysian Ringgits (%1$s)', 'give'), '&#82;&#77;'),
			'symbol'      => '&#82;&#77;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'MXN' => array(
			'admin_label' => sprintf( __('Mexican Peso (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'MAD' => array(
			'admin_label' => sprintf( __('Moroccan Dirham (%1$s)', 'give'), '&#x2e;&#x62f;&#x2e;&#x645;'),
			'symbol'      => '&#x2e;&#x62f;&#x2e;&#x645;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'NZD' => array(
			'admin_label' => sprintf( __('New Zealand Dollar (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'NOK' => array(
			'admin_label' => sprintf( __('Norwegian Krone (%1$s)', 'give'), '&#107;&#114;.'),
			'symbol'      => '&#107;&#114;.',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'PHP' => array(
			'admin_label' => sprintf( __('Philippine Pesos (%1$s)', 'give'), '&#8369;'),
			'symbol'      => '&#8369;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'PLN' => array(
			'admin_label' => sprintf( __('Polish Zloty (%1$s)', 'give'), '&#122;&#322;'),
			'symbol'      => '&#122;&#322;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'SGD' => array(
			'admin_label' => sprintf( __('Singapore Dollar (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'KRW' => array(
			'admin_label' => sprintf( __('South Korean Won (%1$s)', 'give'), '&#8361;'),
			'symbol'      => '&#8361;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'ZAR' => array(
			'admin_label' => sprintf( __('South African Rand (%1$s)', 'give'), '&#82;'),
			'symbol'      => '&#82;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ' ',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'SEK' => array(
			'admin_label' => sprintf( __('Swedish Krona (%1$s)', 'give'), '&nbsp;kr.&nbsp;'),
			'symbol'      => '&nbsp;kr.&nbsp;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'CHF' => array(
			'admin_label' => sprintf( __('Swiss Franc (%1$s)', 'give'), '&#70;&#114;'),
			'symbol'      => '&#70;&#114;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ' ',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'TWD' => array(
			'admin_label' => sprintf( __('Taiwan New Dollars (%1$s)', 'give'), '&#78;&#84;&#36;'),
			'symbol'      => '&#78;&#84;&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'THB' => array(
			'admin_label' => sprintf( __('Thai Baht (%1$s)', 'give'), '&#3647;'),
			'symbol'      => '&#3647;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'INR' => array(
			'admin_label' => sprintf( __('Indian Rupee (%1$s)', 'give'), '&#8377;'),
			'symbol'      => '&#8377;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'TRY' => array(
			'admin_label' => sprintf( __('Turkish Lira (%1$s)', 'give'), '&#8378;'),
			'symbol'      => '&#8378;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'IRR' => array(
			'admin_label' => sprintf( __('Iranian Rial (%1$s)', 'give'), '&#xfdfc;'),
			'symbol'      => '&#xfdfc;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'RUB' => array(
			'admin_label' => sprintf( __('Russian Rubles (%1$s)', 'give'), '&#8381;'),
			'symbol'      => '&#8381;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'AED' => array(
			'admin_label' => sprintf( __('United Arab Emirates dirham (%1$s)', 'give'), '&#x62f;.&#x625;'),
			'symbol'      => '&#x62f;.&#x625;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'AMD' => array(
			'admin_label' => sprintf( __('Armenian dram (%1$s)', 'give'), 'AMD'),
			'symbol'      => 'AMD', // Add backward compatibility. Using AMD in place of &#1423;
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'ANG' => array(
			'admin_label' => sprintf( __('Netherlands Antillean guilder (%1$s)', 'give'), '&#402;'),
			'symbol'      => '&#402;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'ARS' => array(
			'admin_label' => sprintf( __('Argentine peso (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'AWG' => array(
			'admin_label' => sprintf( __( 'Aruban florin (%1$s)', 'give' ), '&#402;' ),
			'symbol'      => '&#402;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'BAM' => array(
			'admin_label' => sprintf( __( 'Bosnia and Herzegovina convertible mark (%1$s)', 'give' ), '&#75;&#77;' ),
			'symbol'      => '&#75;&#77;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'BDT' => array(
			'admin_label' => sprintf( __( 'Bangladeshi taka (%1$s)', 'give' ), '&#2547;' ),
			'symbol'      => '&#2547;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'BHD' => array(
			'admin_label' => sprintf( __( 'Bahraini dinar (%1$s)', 'give' ), '.&#x62f;.&#x628;' ),
			'symbol'      => '.&#x62f;.&#x628;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 3,
			),
		),
		'BMD' => array(
			'admin_label' => sprintf( __( 'Bermudian dollar (%1$s)', 'give' ), '&#66;&#68;&#36;' ),
			'symbol'      => '&#66;&#68;&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'BND' => array(
			'admin_label' => sprintf( __( 'Brunei dollar (%1$s)', 'give' ), '&#66;&#36;' ),
			'symbol'      => '&#66;&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'BOB' => array(
			'admin_label' => sprintf( __( 'Bolivian boliviano (%1$s)', 'give' ), '&#66;&#115;&#46;' ),
			'symbol'      => '&#66;&#115;&#46;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'BSD' => array(
			'admin_label' => sprintf( __( 'Bahamian dollar (%1$s)', 'give' ), '&#66;&#36;' ),
			'symbol'      => '&#66;&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'BWP' => array(
			'admin_label' => sprintf( __( 'Botswana pula (%1$s)', 'give' ), '&#80;' ),
			'symbol'      => '&#80;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'BZD' => array(
			'admin_label' => sprintf( __( 'Belizean dollar (%1$s)', 'give' ), '&#66;&#90;&#36;' ),
			'symbol'      => '&#66;&#90;&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'CLP' => array(
			'admin_label' => sprintf( __( 'Chilean peso (%1$s)', 'give' ), '&#36;' ),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => '',
				'number_decimals'     => 0,
			),
		),
		'CNY' => array(
			'admin_label' => sprintf( __( 'Chinese yuan (%1$s)', 'give' ), '&yen;' ),
			'symbol'      => '&yen;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'COP' => array(
			'admin_label' => sprintf( __( 'Colombian peso (%1$s)', 'give' ), '&#36;' ),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'CRC' => array(
			'admin_label' => sprintf( __( 'Costa Rican colón (%1$s)', 'give' ), '&#8353;' ),
			'symbol'      => '&#8353;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'CUC' => array(
			'admin_label' => sprintf( __( 'Cuban convertible peso (%1$s)', 'give' ), '&#8369;' ),
			'symbol'      => '&#8369;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'CUP' => array(
			'admin_label' => sprintf( __( 'Cuban convertible peso (%1$s)', 'give' ), '&#8369;' ),
			'symbol'      => '&#8369;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'DOP' => array(
			'admin_label' => sprintf( __( 'Dominican peso (%1$s)', 'give' ), '&#82;&#68;&#36;' ),
			'symbol'      => '&#82;&#68;&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'EGP' => array(
			'admin_label' => sprintf( __( 'Egyptian pound (%1$s)', 'give' ), '&#69;&pound;' ),
			'symbol'      => '&#69;&pound;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'GIP' => array(
			'admin_label' => sprintf( __( 'Gibraltar pound (%1$s)', 'give' ), '&pound;' ),
			'symbol'      => '&pound;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'GTQ' => array(
			'admin_label' => sprintf( __( 'Guatemalan quetzal (%1$s)', 'give' ), '&#81;' ),
			'symbol'      => '&#81;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'HNL' => array(
			'admin_label' => sprintf( __( 'Honduran lempira (%1$s)', 'give' ), '&#76;' ),
			'symbol'      => '&#76;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'HRK' => array(
			'admin_label' => sprintf( __( 'Croatian kuna (%1$s)', 'give' ), '&#107;&#110;' ),
			'symbol'      => '&#107;&#110;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'IDR' => array(
			'admin_label' => sprintf( __( 'Indonesian rupiah (%1$s)', 'give' ), '&#82;&#112;' ),
			'symbol'      => '&#82;&#112;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'ISK' => array(
			'admin_label' => sprintf( __( 'Icelandic króna (%1$s)', 'give' ), '&#107;&#114;' ),
			'symbol'      => '&#107;&#114;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => '.',
				'decimal_separator'   => '',
				'number_decimals'     => 0,
			),
		),
		'JMD' => array(
			'admin_label' => sprintf( __( 'Jamaican dollar (%1$s)', 'give' ), '&#106;&#36;' ),
			'symbol'      => '&#106;&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'JOD' => array(
			'admin_label' => sprintf( __( 'Jordanian dinar (%1$s)', 'give' ), '&#x62f;.&#x627;' ),
			'symbol'      => '&#x62f;.&#x627;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'KES' => array(
			'admin_label' => sprintf( __( 'Kenyan shilling (%1$s)', 'give' ), '&#75;&#83;&#104;' ),
			'symbol'      => '&#75;&#83;&#104;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'KWD' => array(
			'admin_label' => sprintf( __( 'Kuwaiti dinar (%1$s)', 'give' ), '&#x62f;.&#x643;' ),
			'symbol'      => '&#x62f;.&#x643;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'KYD' => array(
			'admin_label' => sprintf( __( 'Cayman Islands dollar (%1$s)', 'give' ), '&#75;&#89;&#36;' ),
			'symbol'      => '&#75;&#89;&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'MKD' => array(
			'admin_label' => sprintf( __( 'Macedonian denar (%1$s)', 'give' ), '&#x434;&#x435;&#x43d;' ),
			'symbol'      => '&#x434;&#x435;&#x43d;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'NPR' => array(
			'admin_label' => sprintf( __( 'Nepalese rupee (%1$s)', 'give' ), '&#8360;' ),
			'symbol'      => '&#8360;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'OMR' => array(
			'admin_label' => sprintf( __( 'Omani rial (%1$s)', 'give' ), '&#x631;.&#x639;&#46;' ),
			'symbol'      => '&#x631;.&#x639;&#46;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 3,
			),
		),
		'PEN' => array(
			'admin_label' => sprintf( __( 'Peruvian nuevo sol (%1$s)', 'give' ), 'S/.' ),
			'symbol'      => 'S/.',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'PKR' => array(
			'admin_label' => sprintf( __( 'Pakistani rupee (%1$s)', 'give' ), '&#8360;' ),
			'symbol'      => '&#8360;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'RON' => array(
			'admin_label' => sprintf( __( 'Romanian leu (%1$s)', 'give' ), '&#76;' ),
			'symbol'      => '&#76;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'SAR' => array(
			'admin_label' => sprintf( __( 'Saudi riyal (%1$s)', 'give' ), '&#x631;.&#x633;' ),
			'symbol'      => '&#x631;.&#x633;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'SZL' => array(
			'admin_label' => sprintf( __( 'Swazi lilangeni (%1$s)', 'give' ), '&#76;'),
			'symbol'      => '&#76;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'TOP' => array(
			'admin_label' => sprintf( __( 'Tongan paʻanga (%1$s)', 'give' ), '&#84;&#36;'),
			'symbol'      => '&#84;&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'TZS' => array(
			'admin_label' => sprintf( __( 'Tanzanian shilling (%1$s)', 'give' ), '&#84;&#83;&#104;'),
			'symbol'      => '&#84;&#83;&#104;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'UAH' => array(
			'admin_label' => sprintf( __( 'Ukrainian hryvnia (%1$s)', 'give' ), '&#8372;'),
			'symbol'      => '&#8372;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'UYU' => array(
			'admin_label' => sprintf( __( 'Uruguayan peso (%1$s)', 'give' ), '&#36;&#85;'),
			'symbol'      => '&#36;&#85;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'VEF' => array(
			'admin_label' => sprintf( __( 'Venezuelan bolívar (%1$s)', 'give' ), '&#66;&#115;'),
			'symbol'      => '&#66;&#115;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'XCD' => array(
			'admin_label' => sprintf( __( 'East Caribbean dollar (%1$s)', 'give' ), '&#69;&#67;&#36;'),
			'symbol'      => '&#69;&#67;&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'AFN' => array(
			'admin_label' => sprintf( __('Afghan afghani (%1$s)', 'give'), '&#x60b;'),
			'symbol'      => '&#x60b;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'ALL' => array(
			'admin_label' => sprintf( __('Albanian lek (%1$s)', 'give'), 'L'),
			'symbol'      => 'L',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'AOA' => array(
			'admin_label' => sprintf( __('Angolan kwanza (%1$s)', 'give'), 'Kz'),
			'symbol'      => 'Kz',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'AZN' => array(
			'admin_label' => sprintf( __('Azerbaijani manat (%1$s)', 'give'), 'AZN'),
			'symbol'      => 'AZN',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'BBD' => array(
			'admin_label' => sprintf( __('Barbadian dollar (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'BGN' => array(
			'admin_label' => sprintf( __('Bulgarian lev (%1$s)', 'give'), '&#1083;&#1074;.'),
			'symbol'      => '&#1083;&#1074;.',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'BIF' => array(
			'admin_label' => sprintf( __('Burundian franc (%1$s)', 'give'), 'Fr'),
			'symbol'      => 'Fr',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'BTC' => array(
			'admin_label' => sprintf( __('Bitcoin (%1$s)', 'give'), '&#3647;'),
			'symbol'      => '&#3647;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 8,
			),
		),
		'BTN' => array(
			'admin_label' => sprintf( __('Bhutanese ngultrum (%1$s)', 'give'), 'Nu.'),
			'symbol'      => 'Nu.',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 1,
			),
		),
		'BYR' => array(
			'admin_label' => sprintf( __('Belarusian ruble (old) (%1$s)', 'give'), 'Br'),
			'symbol'      => 'Br',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'BYN' => array(
			'admin_label' => sprintf( __('Belarusian ruble (%1$s)', 'give'), 'Br'),
			'symbol'      => 'Br',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'CDF' => array(
			'admin_label' => sprintf( __('Congolese franc (%1$s)', 'give'), 'Fr'),
			'symbol'      => 'Fr',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'CVE' => array(
			'admin_label' => sprintf( __('Cape Verdean escudo (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'DJF' => array(
			'admin_label' => sprintf( __('Djiboutian franc (%1$s)', 'give'), 'Fr'),
			'symbol'      => 'Fr',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'DZD' => array(
			'admin_label' => sprintf( __('Algerian dinar (%1$s)', 'give'), '&#x62f;.&#x62c;'),
			'symbol'      => '&#x62f;.&#x62c;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'ERN' => array(
			'admin_label' => sprintf( __('Eritrean nakfa (%1$s)', 'give'), 'Nfk'),
			'symbol'      => 'Nfk',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'ETB' => array(
			'admin_label' => sprintf( __('Ethiopian birr (%1$s)', 'give'), 'Br'),
			'symbol'      => 'Br',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'FJD' => array(
			'admin_label' => sprintf( __('Fijian dollar (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'FKP' => array(
			'admin_label' => sprintf( __('Falkland Islands pound (%1$s)', 'give'), '&pound;'),
			'symbol'      => '&pound;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'GEL' => array(
			'admin_label' => sprintf( __('Georgian lari (%1$s)', 'give'), '&#x20be;'),
			'symbol'      => '&#x20be;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'GGP' => array(
			'admin_label' => sprintf( __('Guernsey pound (%1$s)', 'give'), '&pound;'),
			'symbol'      => '&pound;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'GHS' => array(
			'admin_label' => sprintf( __('Ghana cedi (%1$s)', 'give'), '&#x20b5;'),
			'symbol'      => '&#x20b5;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'GMD' => array(
			'admin_label' => sprintf( __('Gambian dalasi (%1$s)', 'give'), 'D'),
			'symbol'      => 'D',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'GNF' => array(
			'admin_label' => sprintf( __('Guinean franc (%1$s)', 'give'), 'Fr'),
			'symbol'      => 'Fr',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'GYD' => array(
			'admin_label' => sprintf( __('Guyanese dollar (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'HTG' => array(
			'admin_label' => sprintf( __('Haitian gourde (%1$s)', 'give'), 'G'),
			'symbol'      => 'G',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'IMP' => array(
			'admin_label' => sprintf( __('Manx pound (%1$s)', 'give'), '&pound;'),
			'symbol'      => '&pound;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'IQD' => array(
			'admin_label' => sprintf( __('Iraqi dinar (%1$s)', 'give'), '&#x639;.&#x62f;'),
			'symbol'      => '&#x639;.&#x62f;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'IRT' => array(
			'admin_label' => sprintf( __('Iranian toman (%1$s)', 'give'), '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;'),
			'symbol'      => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'JEP' => array(
			'admin_label' => sprintf( __('Jersey pound (%1$s)', 'give'), '&pound;'),
			'symbol'      => '&pound;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'KGS' => array(
			'admin_label' => sprintf( __('Kyrgyzstani som (%1$s)', 'give'), '&#x441;&#x43e;&#x43c;'),
			'symbol'      => '&#x441;&#x43e;&#x43c;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => '-',
				'number_decimals'     => 2,
			),
		),
		'KHR' => array(
			'admin_label' => sprintf( __('Cambodian riel (%1$s)', 'give'), '&#x17db;'),
			'symbol'      => '&#x17db;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'KMF' => array(
			'admin_label' => sprintf( __('Comorian franc (%1$s)', 'give'), 'Fr'),
			'symbol'      => 'Fr',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'KPW' => array(
			'admin_label' => sprintf( __('North Korean won (%1$s)', 'give'), '&#x20a9;'),
			'symbol'      => '&#x20a9;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'KZT' => array(
			'admin_label' => sprintf( __('Kazakhstani tenge (%1$s)', 'give'), 'KZT'),
			'symbol'      => 'KZT',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ' ',
				'decimal_separator'   => '-',
				'number_decimals'     => 2,
			),
		),
		'LAK' => array(
			'admin_label' => sprintf( __('Lao kip (%1$s)', 'give'), '&#8365;'),
			'symbol'      => '&#8365;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'LBP' => array(
			'admin_label' => sprintf( __('Lebanese pound (%1$s)', 'give'), '&#x644;.&#x644;'),
			'symbol'      => '&#x644;.&#x644;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'LKR' => array(
			'admin_label' => sprintf( __('Sri Lankan rupee (%1$s)', 'give'), '&#xdbb;&#xdd4;'),
			'symbol'      => '&#xdbb;&#xdd4;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'LRD' => array(
			'admin_label' => sprintf( __('Liberian dollar (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'LSL' => array(
			'admin_label' => sprintf( __('Lesotho loti (%1$s)', 'give'), 'L'),
			'symbol'      => 'L',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'LYD' => array(
			'admin_label' => sprintf( __('Libyan dinar (%1$s)', 'give'), '&#x644;.&#x62f;'),
			'symbol'      => '&#x644;.&#x62f;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 3,
			),
		),
		'MDL' => array(
			'admin_label' => sprintf( __('Moldovan leu (%1$s)', 'give'), 'MDL'),
			'symbol'      => 'MDL',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'MGA' => array(
			'admin_label' => sprintf( __('Malagasy ariary (%1$s)', 'give'), 'Ar'),
			'symbol'      => 'Ar',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'MMK' => array(
			'admin_label' => sprintf( __('Burmese kyat (%1$s)', 'give'), 'Ks'),
			'symbol'      => 'Ks',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'MNT' => array(
			'admin_label' => sprintf( __('Mongolian tögrög (%1$s)', 'give'), '&#x20ae;'),
			'symbol'      => '&#x20ae;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'MOP' => array(
			'admin_label' => sprintf( __('Macanese pataca (%1$s)', 'give'), 'P'),
			'symbol'      => 'P',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'MRO' => array(
			'admin_label' => sprintf( __('Mauritanian ouguiya (%1$s)', 'give'), 'UM'),
			'symbol'      => 'UM',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'MUR' => array(
			'admin_label' => sprintf( __('Mauritian rupee (%1$s)', 'give'), '&#x20a8;'),
			'symbol'      => '&#x20a8;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'MVR' => array(
			'admin_label' => sprintf( __('Maldivian rufiyaa (%1$s)', 'give'), '.&#x783;'),
			'symbol'      => '.&#x783;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 1,
			),
		),
		'MWK' => array(
			'admin_label' => sprintf( __('Malawian kwacha (%1$s)', 'give'), 'MK'),
			'symbol'      => 'MK',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'MZN' => array(
			'admin_label' => sprintf( __('Mozambican metical (%1$s)', 'give'), 'MT'),
			'symbol'      => 'MT',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'NAD' => array(
			'admin_label' => sprintf( __('Namibian dollar (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'NGN' => array(
			'admin_label' => sprintf( __('Nigerian naira (%1$s)', 'give'), '&#8358;'),
			'symbol'      => '&#8358;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'NIO' => array(
			'admin_label' => sprintf( __('Nicaraguan córdoba (%1$s)', 'give'), 'C&#36;'),
			'symbol'      => 'C&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'PAB' => array(
			'admin_label' => sprintf( __('Panamanian balboa (%1$s)', 'give'), 'B/.'),
			'symbol'      => 'B/.',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'PGK' => array(
			'admin_label' => sprintf( __('Papua New Guinean kina (%1$s)', 'give'), 'K'),
			'symbol'      => 'K',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'PRB' => array(
			'admin_label' => sprintf( __('Transnistrian ruble (%1$s)', 'give'), '&#x440;.'),
			'symbol'      => '&#x440;.',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'PYG' => array(
			'admin_label' => sprintf( __('Paraguayan guaraní (%1$s)', 'give'), '&#8370;'),
			'symbol'      => '&#8370;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'QAR' => array(
			'admin_label' => sprintf( __('Qatari riyal (%1$s)', 'give'), '&#x631;.&#x642;'),
			'symbol'      => '&#x631;.&#x642;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'RSD' => array(
			'admin_label' => sprintf( __('Serbian dinar (%1$s)', 'give'), '&#x434;&#x438;&#x43d;.'),
			'symbol'      => '&#x434;&#x438;&#x43d;.',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'RWF' => array(
			'admin_label' => sprintf( __('Rwandan franc (%1$s)', 'give'), 'Fr'),
			'symbol'      => 'Fr',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'SBD' => array(
			'admin_label' => sprintf( __('Solomon Islands dollar (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'SCR' => array(
			'admin_label' => sprintf( __('Seychellois rupee (%1$s)', 'give'), '&#x20a8;'),
			'symbol'      => '&#x20a8;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'SDG' => array(
			'admin_label' => sprintf( __('Sudanese pound (%1$s)', 'give'), '&#x62c;.&#x633;.'),
			'symbol'      => '&#x62c;.&#x633;.',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'SHP' => array(
			'admin_label' => sprintf( __('Saint Helena pound (%1$s)', 'give'), '&pound;'),
			'symbol'      => '&pound;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'SLL' => array(
			'admin_label' => sprintf( __('Sierra Leonean leone (%1$s)', 'give'), 'Le'),
			'symbol'      => 'Le',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'SOS' => array(
			'admin_label' => sprintf( __('Somali shilling (%1$s)', 'give'), 'Sh'),
			'symbol'      => 'Sh',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'SRD' => array(
			'admin_label' => sprintf( __('Surinamese dollar (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'SSP' => array(
			'admin_label' => sprintf( __('South Sudanese pound (%1$s)', 'give'), '&pound;'),
			'symbol'      => '&pound;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'STD' => array(
			'admin_label' => sprintf( __('São Tomé and Príncipe dobra (%1$s)', 'give'), 'Db'),
			'symbol'      => 'Db',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'SYP' => array(
			'admin_label' => sprintf( __('Syrian pound (%1$s)', 'give'), '&#x644;.&#x633;'),
			'symbol'      => '&#x644;.&#x633;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'TJS' => array(
			'admin_label' => sprintf( __('Tajikistani somoni (%1$s)', 'give'), '&#x405;&#x41c;'),
			'symbol'      => '&#x405;&#x41c;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ';',
				'number_decimals'     => 2,
			),
		),
		'TMT' => array(
			'admin_label' => sprintf( __('Turkmenistan manat (%1$s)', 'give'), 'm'),
			'symbol'      => 'm',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'TND' => array(
			'admin_label' => sprintf( __('Turkmenistan manat (%1$s)', 'give'), '&#x62f;.&#x62a;'),
			'symbol'      => '&#x62f;.&#x62a;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 3,
			),
		),
		'TTD' => array(
			'admin_label' => sprintf( __('Trinidad and Tobago dollar (%1$s)', 'give'), '&#36;'),
			'symbol'      => '&#36;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'UGX' => array(
			'admin_label' => sprintf( __('Ugandan shilling (%1$s)', 'give'), 'UGX'),
			'symbol'      => 'UGX',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'UZS' => array(
			'admin_label' => sprintf( __('Uzbekistani som (%1$s)', 'give'), 'UZS'),
			'symbol'      => 'UZS',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'VND' => array(
			'admin_label' => sprintf( __('Vietnamese đồng (%1$s)', 'give'), '&#8363;'),
			'symbol'      => '&#8363;',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => '.',
				'decimal_separator'   => ',',
				'number_decimals'     => 1,
			),
		),
		'VUV' => array(
			'admin_label' => sprintf( __('Vanuatu vatu (%1$s)', 'give'), 'Vt'),
			'symbol'      => 'Vt',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 0,
			),
		),
		'WST' => array(
			'admin_label' => sprintf( __('Samoan tālā (%1$s)', 'give'), 'T'),
			'symbol'      => 'T',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'XAF' => array(
			'admin_label' => sprintf( __('Central African CFA franc (%1$s)', 'give'), 'CFA'),
			'symbol'      => 'CFA',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'XOF' => array(
			'admin_label' => sprintf( __('West African CFA franc (%1$s)', 'give'), 'CFA'),
			'symbol'      => 'CFA',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ' ',
				'decimal_separator'   => ',',
				'number_decimals'     => 2,
			),
		),
		'XPF' => array(
			'admin_label' => sprintf( __('CFP franc (%1$s)', 'give'), 'Fr'),
			'symbol'      => 'Fr',
			'setting'     => array(
				'currency_position'   => 'after',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'YER' => array(
			'admin_label' => sprintf( __('Yemeni rial (%1$s)', 'give'), '&#xfdfc;'),
			'symbol'      => '&#xfdfc;',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
		'ZMW' => array(
			'admin_label' => sprintf( __('Zambian kwacha (%1$s)', 'give'), 'ZK'),
			'symbol'      => 'ZK',
			'setting'     => array(
				'currency_position'   => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.',
				'number_decimals'     => 2,
			),
		),
	);

	/**
	 * Filter the currencies
	 * Note: you can register new currency by using this filter
	 * array(
	 *     'admin_label' => '',  // required
	 *     'symbol'      => '',  // required
	 *     'setting'     => ''   // required
	 *     ....
	 * )
	 *
	 * @since 1.8.15
	 *
	 * @param array $currencies
	 */
	return (array) apply_filters( 'give_currencies', $currencies );
}

/**
 * Get Currencies
 *
 * @since 1.0
 *
 * @param string $info Specify currency info
 *
 * @return array $currencies A list of the available currencies
 */
function give_get_currencies( $info = 'admin_label' ) {

	$currencies = give_get_currencies_list();

	// Backward compatibility: handle old way of currency registration.
	// Backward compatibility: Return desired result.
	if ( ! empty( $currencies ) ) {
		foreach ( $currencies as $currency_code => $currency_setting ) {
			if ( is_string( $currency_setting ) ) {
				$currencies[ $currency_code ] = array(
					'admin_label' => $currency_setting,
				);
			}

			$currencies[ $currency_code ] = wp_parse_args(
				$currencies[ $currency_code ],
				array(
					'admin_label' => '',
					'symbol'      => $currency_code,
					'setting'     => array(),
				)
			);
		}

		if ( ! empty( $info ) && is_string( $info ) && 'all' !== $info ) {
			$currencies = wp_list_pluck( $currencies, $info );
		}
	}

	return $currencies;
}


/**
 * Get all currency symbols
 *
 * @since 1.8.14
 *
 * @param bool $decode_currencies
 *
 * @return array
 */
function give_currency_symbols( $decode_currencies = false ) {
	$currencies = give_get_currencies( 'symbol' );

	if ( $decode_currencies ) {
		array_walk( $currencies, function ( &$currency_symbol ) {
			$currency_symbol = html_entity_decode( $currency_symbol, ENT_COMPAT, 'UTF-8' );
		} );
	}

	/**
	 * Filter the currency symbols
	 *
	 * @since 1.8.14
	 *
	 * @param array $currencies
	 */
	return apply_filters( 'give_currency_symbols', $currencies );
}


/**
 * Give Currency Symbol
 *
 * Given a currency determine the symbol to use. If no currency given, site default is used. If no symbol is determine,
 * the currency string is returned.
 *
 * @since      1.0
 *
 * @param  string $currency        The currency string.
 * @param  bool   $decode_currency Option to HTML decode the currency symbol.
 *
 * @return string           The symbol to use for the currency
 */
function give_currency_symbol( $currency = '', $decode_currency = false ) {

	if ( empty( $currency ) ) {
		$currency = give_get_currency();
	}

	$currencies = give_currency_symbols( $decode_currency );
	$symbol     = array_key_exists( $currency, $currencies ) ? $currencies[ $currency ] : $currency;

	/**
	 * Filter the currency symbol
	 *
	 * @since 1.0
	 *
	 * @param string $symbol
	 * @param string $currency
	 */
	return apply_filters( 'give_currency_symbol', $symbol, $currency );
}


/**
 * Get currency name.
 *
 * @since 1.8.8
 *
 * @param string $currency_code
 *
 * @return string
 */
function give_get_currency_name( $currency_code ) {
	$currency_name  = '';
	$currency_names = give_get_currencies();

	if ( $currency_code && array_key_exists( $currency_code, $currency_names ) ) {
		$currency_name = explode( '(', $currency_names[ $currency_code ] );
		$currency_name = trim( current( $currency_name ) );
	}

	/**
	 * Filter the currency name
	 *
	 * @since 1.8.8
	 *
	 * @param string $currency_name
	 * @param string $currency_code
	 */
	return apply_filters( 'give_currency_name', $currency_name, $currency_code );
}

/**
 * Formats the currency displayed.
 *
 * @since 1.0
 *
 * @param string $price The donation amount.
 * @param array  $args  It accepts 'currency_code', 'decode_currency' and 'form_id'.
 *
 * @return mixed|string
 */
function give_currency_filter( $price = '', $args = array() ) {

	// Get functions arguments.
	$func_args = func_get_args();

	// Backward compatibility: modify second param to array
	if ( isset( $func_args[1] ) && is_string( $func_args[1] ) ) {
		$args = array(
			'currency_code'   => isset( $func_args[1] ) ? $func_args[1] : '',
			'decode_currency' => isset( $func_args[2] ) ? $func_args[2] : false,
			'form_id'         => isset( $func_args[3] ) ? $func_args[3] : '',
		);

		give_doing_it_wrong( __FUNCTION__, 'Pass second argument as Array.', GIVE_VERSION );
	}

	// Set default values.
	$args = wp_parse_args(
		$args,
		array(
			'currency_code'   => '',
			'decode_currency' => false,
			'form_id'         => '',
		)
	);

	if ( empty( $args['currency_code'] ) || ! array_key_exists( (string) $args['currency_code'], give_get_currencies() ) ) {
		$args['currency_code'] = give_get_currency( $args['form_id'] );
	}

	$args['position'] = give_get_option( 'currency_position', 'before' );

	$negative = $price < 0;

	if ( $negative ) {
		// Remove proceeding "-".
		$price = substr( $price, 1 );
	}

	$args['symbol'] = give_currency_symbol( $args['currency_code'], $args['decode_currency'] );

	switch ( $args['currency_code'] ) :
		case 'GBP' :
		case 'BRL' :
		case 'EUR' :
		case 'USD' :
		case 'AUD' :
		case 'CAD' :
		case 'HKD' :
		case 'MXN' :
		case 'NZD' :
		case 'SGD' :
		case 'JPY' :
		case 'THB' :
		case 'INR' :
		case 'IDR' :
		case 'IRR' :
		case 'TRY' :
		case 'RUB' :
		case 'SEK' :
		case 'PLN' :
		case 'PHP' :
		case 'TWD' :
		case 'MYR' :
		case 'CZK' :
		case 'DKK' :
		case 'HUF' :
		case 'ILS' :
		case 'MAD' :
		case 'KRW' :
		case 'ZAR' :
			$formatted = ( 'before' === $args['position'] ? $args['symbol'] . $price : $price . $args['symbol'] );
			break;
		case 'NOK':
			$formatted = ( 'before' === $args['position'] ? $args['symbol'] . ' ' . $price : $price . ' ' . $args['symbol'] );
			break;
		default:
			$formatted = ( 'before' === $args['position'] ? $args['symbol'] . ' ' . $price : $price . ' ' . $args['symbol'] );
			break;
	endswitch;

	/**
	 * Filter formatted amount
	 *
	 * @since 1.8.17
	 */
	$formatted = apply_filters( 'give_currency_filter', $formatted, $args, $price );

	/**
	 * Filter formatted amount with currency
	 *
	 * Filter name depends upon current value of currency and currency position.
	 * For example :
	 *           if currency is USD and currency position is before then
	 *           filter name will be give_usd_currency_filter_before
	 *
	 *           and if currency is USD and currency position is after then
	 *           filter name will be give_usd_currency_filter_after
	 */
	$formatted = apply_filters(
		'give_' . strtolower( $args['currency_code'] ) . "_currency_filter_{$args['position']}",
		$formatted,
		$args['currency_code'],
		$price,
		$args
	);

	if ( $negative ) {
		// Prepend the minus sign before the currency sign.
		$formatted = '-' . $formatted;
	}

	return $formatted;
}

/**
 * This function is used to fetch list of zero based currencies.
 *
 * @since 2.3.0
 *
 * @return array
 */
function give_get_zero_based_currencies() {

	$zero_based_currencies = array(
		'JPY', // Japanese Yen.
		'KRW', // South Korean Won.
		'CLP', // Chilean peso.
		'ISK', // Icelandic króna.
		'BIF', // Burundian franc.
		'DJF', // Djiboutian franc.
		'GNF', // Guinean franc.
		'KHR', // Cambodian riel.
		'KPW', // North Korean won.
		'LAK', // Lao kip.
		'LKR', // Sri Lankan rupee.
		'MGA', // Malagasy ariary.
		'MZN', // Mozambican metical.
		'VUV', // Vanuatu vatu.
	);

	/**
	 * This filter hook can be used to update the list of zero based currencies.
	 *
	 * @since 2.3.0
	 */
	return apply_filters( 'give_get_zero_based_currencies', $zero_based_currencies );
}

/**
 * Zero Decimal based Currency.
 *
 * @since 1.8.14
 * @since 2.2.0 Modified list.
 * @see   https://github.com/impress-org/give/issues/2191
 *
 * @param string $currency Currency code
 *
 * @return bool
 */
function give_is_zero_based_currency( $currency = '' ) {

	$zero_based_currency = give_get_zero_based_currencies();

	// Set default currency.
	if ( empty( $currency ) ) {
		$currency = give_get_currency();
	}

	// Check for Zero Based Currency.
	if ( in_array( $currency, $zero_based_currency ) ) {
		return true;
	}

	return false;
}


/**
 * Check if currency support right to left direction or not.
 *
 * @param string $currency
 *
 * @return bool
 */
function give_is_right_to_left_supported_currency( $currency = '' ) {
	$zero_based_currency = apply_filters(
		'give_right_to_left_supported_currency',
		array(
			'IRR',
			'RIAL',
			'MAD',
			'AED',
			'BHD',
			'KWD',
			'OMR',
			'SAR',
			'TND', //https://en.wikipedia.org/wiki/Tunisian_dinar
			'QAR', //https://en.wikipedia.org/wiki/Qatari_riyal
			'LYD', //https://en.wikipedia.org/wiki/Libyan_dinar
			'LBP', //https://en.wikipedia.org/wiki/Lebanese_pound
			'IRT', //https://en.wikipedia.org/wiki/Iranian_toman
			'IQD', //https://en.wikipedia.org/wiki/Iraqi_dinar
			'DZD', //https://en.wikipedia.org/wiki/Algerian_dinar
			'AFN', //https://en.wikipedia.org/wiki/Afghan_afghani
		)
	);

	// Set default currency.
	if ( empty( $currency ) ) {
		$currency = give_get_currency();
	}

	// Check for Zero Based Currency.
	if ( in_array( $currency, $zero_based_currency ) ) {
		return true;
	}

	return false;
}
