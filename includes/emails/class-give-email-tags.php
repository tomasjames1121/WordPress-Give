<?php
/**
 * Give API for creating Email template tags
 *
 * Email tags are wrapped in { }
 *
 * A few examples:
 *
 * {name}
 * {sitename}
 *
 *
 * To replace tags in content, use: give_do_email_tags( $content, payment_id );
 *
 * To add tags, use: give_add_email_tag( $tag, $description, $func ). Be sure to wrap give_add_email_tag()
 * in a function hooked to the 'give_email_tags' action.
 *
 * @package     Give
 * @subpackage  Emails
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Give_Email_Template_Tags
 */
class Give_Email_Template_Tags {

	/**
	 * Container for storing all tags.
	 *
	 * @since 1.0
	 */
	private $tags;

	/**
	 * Tags arguments
	 *
	 * @since 1.9
	 */
	private $tag_args;

	/**
	 * Add an email tag.
	 *
	 * @since 1.0
	 *
	 * @param string   $tag         Email tag to be replace in email
	 * @param string   $description Email tag description text
	 * @param callable $func        Hook to run when email tag is found
	 * @param string   $context     Email tag category
	 */
	public function add( $tag, $description, $func, $context = '' ) {
		if ( is_callable( $func ) ) {
			$this->tags[ $tag ] = array(
				'tag'         => $tag,
				'description' => $description,
				'func'        => $func,
				'context'     => give_check_variable( $context, 'empty', 'general' ),
			);
		}
	}

	/**
	 * Remove an email tag.
	 *
	 * @since 1.0
	 *
	 * @param string $tag Email tag to remove hook from
	 */
	public function remove( $tag ) {
		unset( $this->tags[ $tag ] );
	}

	/**
	 * Check if $tag is a registered email tag.
	 *
	 * @since 1.0
	 *
	 * @param string $tag Email tag that will be searched.
	 *
	 * @return bool
	 */
	public function email_tag_exists( $tag ) {
		return array_key_exists( $tag, $this->tags );
	}

	/**
	 * Returns a list of all email tags
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_tags() {
		return $this->tags;
	}

	/**
	 * Search content for email tags and filter email tags through their hooks.
	 *
	 * @param string $content  Content to search for email tags.
	 * @param array  $tag_args Email template tag arguments.
	 *
	 * @since 1.0
	 * @since 1.9 $payment_id deprecated.
	 * @since 1.9 $tag_args added.
	 *
	 * @return string Content with email tags filtered out.
	 */
	public function do_tags( $content, $tag_args ) {

		// Check if there is at least one tag added.
		if ( empty( $this->tags ) || ! is_array( $this->tags ) ) {
			return $content;
		}

		$this->tag_args = $tag_args;

		$new_content = preg_replace_callback( "/{([A-z0-9\-\_]+)}/s", array( $this, 'do_tag' ), $content );

		$this->tag_args = null;

		return $new_content;
	}

	/**
	 * Do a specific tag, this function should not be used. Please use give_do_email_tags instead.
	 *
	 * @since 1.0
	 *
	 * @param $m array
	 *
	 * @return mixed
	 */
	public function do_tag( $m ) {

		// Get tag
		$tag = $m[1];

		// Return tag if tag not set
		if ( ! $this->email_tag_exists( $tag ) ) {
			return $m[0];
		}

		return call_user_func( $this->tags[ $tag ]['func'], $this->tag_args, $tag );
	}

}

/**
 * Add an email tag.
 *
 * @since 1.0
 *
 * @param string   $tag         Email tag to be replace in email
 * @param string   $description Description of the email tag added
 * @param callable $func        Hook to run when email tag is found
 * @param string   $context     Email tag category
 */
function give_add_email_tag( $tag, $description, $func, $context = '' ) {
	Give()->email_tags->add( $tag, $description, $func, $context );
}

/**
 * Remove an email tag
 *
 * @since 1.0
 *
 * @param string $tag Email tag to remove hook from
 */
function give_remove_email_tag( $tag ) {
	Give()->email_tags->remove( $tag );
}

/**
 * Check if $tag is a registered email tag
 *
 * @since 1.0
 *
 * @param string $tag Email tag that will be searched
 *
 * @return bool
 */
function give_email_tag_exists( $tag ) {
	return Give()->email_tags->email_tag_exists( $tag );
}

/**
 * Get all email tags
 *
 * @since 1.0
 *
 * @return array
 */
function give_get_email_tags() {
	return Give()->email_tags->get_tags();
}

/**
 * Get a formatted HTML list of all available email tags
 *
 * @since 1.0
 *
 * @return string
 */
function give_get_emails_tags_list() {

	// Get all email tags.
	$email_tags = give_get_email_tags();

	ob_start();
	if ( count( $email_tags ) > 0 ) : ?>
		<div class="give-email-tags-wrap">
			<?php foreach ( $email_tags as $email_tag ) : ?>
				<span class="give_<?php echo $email_tag['tag']; ?>_tag">
					<code>{<?php echo $email_tag['tag']; ?>}</code> - <?php echo $email_tag['description']; ?>
				</span>
			<?php endforeach; ?>
		</div>
	<?php endif;

	// Return the list.
	return ob_get_clean();
}

/**
 * Search content for email tags and filter email tags through their hooks.
 *
 * @param string    $content  Content to search for email tags.
 * @param array|int $tag_args Email template tag arguments.
 *
 * @since 1.0
 * @since 1.9 $payment_id deprecated.
 * @since 1.9 $tag_args added.
 *
 * @return string Content with email tags filtered out.
 */
function give_do_email_tags( $content, $tag_args ) {
	// Backward compatibility < 1.9
	if ( ! is_array( $tag_args ) ) {
		$tag_args = array( 'payment_id' => $tag_args );
	}

	// Replace all tags
	$content = Give()->email_tags->do_tags( $content, $tag_args );

	/**
	 * Filter the filtered content text.
	 *
	 * @since 1.0
	 * @since 1.9 $payment_meta removed.
	 * @since 1.9 $payment_id removed.
	 * @since 1.9 $tag_args added.
	 */
	$content = apply_filters( 'give_email_template_tags', $content, $tag_args );

	// Return content
	return $content;
}

/**
 * Load email tags.
 *
 * @since 1.0
 */
function give_load_email_tags() {
	/**
	 * Fires when loading email tags.
	 *
	 * Allows you to add new email tags.
	 *
	 * @since 1.0
	 */
	do_action( 'give_add_email_tags' );
}

add_action( 'init', 'give_load_email_tags', - 999 );

/**
 * Add default Give email template tags.
 *
 * @since 1.0
 */
function give_setup_email_tags() {

	// Setup default tags array
	$email_tags = array(
		/*	Donation Payment */
		array(
			'tag'         => 'donation',
			'description' => esc_html__( 'The donation form name, and the donation level (if applicable).', 'give' ),
			'function'    => 'give_email_tag_donation',
			'context'     => 'donation',
		),
		array(
			'tag'         => 'amount',
			'description' => esc_html__( 'The total donation amount with currency sign.', 'give' ),
			'function'    => 'give_email_tag_amount',
			'context'     => 'donation',
		),
		array(
			'tag'         => 'price',
			'description' => esc_html__( 'The total donation amount with currency sign.', 'give' ),
			'function'    => 'give_email_tag_price',
			'context'     => 'donation',
		),
		array(
			'tag'         => 'billing_address',
			'description' => esc_html__( 'The donor\'s billing address.', 'give' ),
			'function'    => 'give_email_tag_billing_address',
			'context'     => 'donation',
		),
		array(
			'tag'         => 'date',
			'description' => esc_html__( 'The date of the donation.', 'give' ),
			'function'    => 'give_email_tag_date',
			'context'     => 'donation',
		),
		array(
			'tag'         => 'payment_id',
			'description' => esc_html__( 'The unique ID number for this donation.', 'give' ),
			'function'    => 'give_email_tag_payment_id',
			'context'     => 'donation',
		),
		array(
			'tag'         => 'payment_method',
			'description' => esc_html__( 'The method of payment used for this donation.', 'give' ),
			'function'    => 'give_email_tag_payment_method',
			'context'     => 'donation',
		),
		array(
			'tag'         => 'payment_total',
			'description' => esc_html__( 'The payment total for this donation.', 'give' ),
			'function'    => 'give_email_tag_payment_total',
			'context'     => 'donation',
		),
		array(
			'tag'         => 'receipt_id',
			'description' => esc_html__( 'The unique ID number for this donation receipt.', 'give' ),
			'function'    => 'give_email_tag_receipt_id',
			'context'     => 'donation',
		),
		array(
			'tag'         => 'receipt_link',
			'description' => esc_html__( 'The donation receipt direct link, to view the receipt on the website.', 'give' ),
			'function'    => 'give_email_tag_receipt_link',
			'context'     => 'donation',
		),
		array(
			'tag'         => 'receipt_link_url',
			'description' => esc_html__( 'The donation receipt direct URL, to view the receipt on the website.', 'give' ),
			'function'    => 'give_email_tag_receipt_link_url',
			'context'     => 'donation',
		),

		/* Donation Form */
		array(
			'tag'         => 'form_title',
			'description' => esc_html__( 'The donation form name.', 'give' ),
			'function'    => 'give_email_tag_form_title',
			'context'     => 'form',
		),

		/* Donor */
		array(
			'tag'         => 'name',
			'description' => esc_html__( 'The donor\'s first name.', 'give' ),
			'function'    => 'give_email_tag_first_name',
			'context'     => 'donor',
		),
		array(
			'tag'         => 'fullname',
			'description' => esc_html__( 'The donor\'s full name, first and last.', 'give' ),
			'function'    => 'give_email_tag_fullname',
			'context'     => 'donor',
		),
		array(
			'tag'         => 'username',
			'description' => esc_html__( 'The donor\'s user name on the site, if they registered an account.', 'give' ),
			'function'    => 'give_email_tag_username',
			'context'     => 'donor',
		),
		array(
			'tag'         => 'user_email',
			'description' => esc_html__( 'The donor\'s email address.', 'give' ),
			'function'    => 'give_email_tag_user_email',
			'context'     => 'donor',
		),

		/* General */
		array(
			'tag'         => 'sitename',
			'description' => esc_html__( 'The name of your site.', 'give' ),
			'function'    => 'give_email_tag_sitename',
			'context'     => 'general',
		),

	);

	// Apply give_email_tags filter
	$email_tags = apply_filters( 'give_email_tags', $email_tags );

	// Add email tags
	foreach ( $email_tags as $email_tag ) {
		give_add_email_tag( $email_tag['tag'], $email_tag['description'], $email_tag['function'], $email_tag['context'] );
	}

}

add_action( 'give_add_email_tags', 'give_setup_email_tags' );


/**
 * Email template tag: {name}
 *
 * The donor's first name.
 *
 * @param array $tag_args Email template tag arguments.
 *
 * @return string $firstname
 */
function give_email_tag_first_name( $tag_args ) {
	$user_info = array();
	$firstname = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment = new Give_Payment( $tag_args['payment_id'] );

			// Get firstname.
			if ( ! empty( $payment->user_info ) ) {
				$email_names = give_get_email_names( $payment->user_info );
				$firstname   = $email_names['name'];
			}
			break;

		case give_check_variable( $tag_args, 'isset', 0, 'user_id' ):
			$user_info = get_user_by( 'id', $tag_args['user_id'] );
			$firstname = $user_info->first_name;
			break;
	}

	/**
	 * Filter the {firstname} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $firstname
	 * @param array  $tag_args
	 */
	$firstname = apply_filters( 'give_email_tag_first_name', $firstname, $tag_args );

	return $firstname;
}

/**
 * Email template tag: {fullname}
 *
 * The donor's full name, first and last.
 *
 * @param array $tag_args
 *
 * @return string $fullname
 */
function give_email_tag_fullname( $tag_args ) {
	$fullname = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment = new Give_Payment( $tag_args['payment_id'] );

			// Get fullname.
			if ( ! empty( $payment->user_info ) ) {
				$email_names = give_get_email_names( $payment->user_info );
				$fullname    = $email_names['fullname'];
			}
			break;

		case give_check_variable( $tag_args, 'isset', 0, 'user_id' ):
			$user_info = get_user_by( 'id', $tag_args['user_id'] );
			$fullname  = trim( "{$user_info->first_name} {$user_info->last_name}" );
			break;
	}

	/**
	 * Filter the {fullname} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $fullname
	 * @param array  $tag_args
	 */
	$fullname = apply_filters( 'give_email_tag_fullname', $fullname, $tag_args );

	return $fullname;
}

/**
 * Email template tag: {username}
 *
 * The donor's user name on the site, if they registered an account.
 *
 * @param array $tag_args
 *
 * @return string username.
 */
function give_email_tag_username( $tag_args ) {
	$username = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment = new Give_Payment( $tag_args['payment_id'] );

			// Get username.
			if ( ! empty( $payment->user_info ) ) {
				$email_names = give_get_email_names( $payment->user_info );
				$username    = $email_names['username'];
			}
			break;

		case give_check_variable( $tag_args, 'isset', 0, 'user_id' ):
			$user_info = get_user_by( 'id', $tag_args['user_id'] );
			$username  = $user_info->user_login;
			break;
	}

	/**
	 * Filter the {username} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $username
	 * @param array  $tag_args
	 */
	$username = apply_filters( 'give_email_tag_username', $username, $tag_args );

	return $username;
}

/**
 * Email template tag: {user_email}
 *
 * The donor's email address
 *
 * @param array $tag_args
 *
 * @return string user_email
 */
function give_email_tag_user_email( $tag_args ) {
	$email = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment = new Give_Payment( $tag_args['payment_id'] );
			$email   = $payment->email;
			break;

		case give_check_variable( $tag_args, 'isset', 0, 'user_id' ):
			$user_info = get_user_by( 'id', $tag_args['user_id'] );
			$email     = $user_info->user_email;
			break;
	}

	/**
	 * Filter the {email} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $email
	 * @param array  $tag_args
	 */
	$email = apply_filters( 'give_email_tag_user_email', $email, $tag_args );

	return $email;
}

/**
 * Email template tag: {billing_address}
 *
 * The donor's billing address
 *
 * @param array $tag_args
 *
 * @return string billing_address
 */
function give_email_tag_billing_address( $tag_args ) {
	$address = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$user_info    = give_get_payment_meta_user_info( $tag_args['payment_id'] );
			$user_address = ! empty( $user_info['address'] ) ? $user_info['address'] : array(
				'line1'   => '',
				'line2'   => '',
				'city'    => '',
				'country' => '',
				'state'   => '',
				'zip'     => '',
			);

			$address = $user_address['line1'] . "\n";

			if ( ! empty( $user_address['line2'] ) ) {
				$address .= $user_address['line2'] . "\n";
			}

			$address .= $user_address['city'] . ' ' . $user_address['zip'] . ' ' . $user_address['state'] . "\n";
			$address .= $user_address['country'];
			break;
	}

	/**
	 * Filter the {billing_address} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $address
	 * @param array  $tag_args
	 */
	$address = apply_filters( 'give_email_tag_billing_address', $address, $tag_args );

	return $address;
}

/**
 * Email template tag: {date}
 *
 * Date of donation
 *
 * @param array $tag_args
 *
 * @return string date
 */
function give_email_tag_date( $tag_args ) {
	$date = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment = new Give_Payment( $tag_args['payment_id'] );
			$date    = date_i18n( give_date_format(), strtotime( $payment->date ) );
			break;
	}

	/**
	 * Filter the {date} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $date
	 * @param array  $tag_args
	 */
	$date = apply_filters( 'give_email_tag_date', $date, $tag_args );

	return $date;
}

/**
 * Email template tag: give_amount.
 *
 * The total amount of the donation given.
 *
 * @param array $tag_args
 *
 * @return string amount
 */
function give_email_tag_amount( $tag_args ) {
	$amount = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment     = new Give_Payment( $tag_args['payment_id'] );
			$give_amount = give_currency_filter( give_format_amount( $payment->total ), $payment->currency );
			$amount      = html_entity_decode( $give_amount, ENT_COMPAT, 'UTF-8' );
			break;
	}

	/**
	 * Filter the {amount} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $amount
	 * @param array  $tag_args
	 */
	$amount = apply_filters( 'give_email_tag_amount', $amount, $tag_args );

	return $amount;
}

/**
 * Email template tag: {price}
 *
 * The total price of the donation.
 *
 * @param array $tag_args
 *
 * @return string price
 */
function give_email_tag_price( $tag_args ) {
	return give_email_tag_amount( $tag_args );
}

/**
 * Email template tag: {payment_id}
 *
 * The unique ID number for this donation.
 *
 * @param array $tag_args
 *
 * @return int payment_id
 */
function give_email_tag_payment_id( $tag_args ) {
	$payment_id = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment    = new Give_Payment( $tag_args['payment_id'] );
			$payment_id = $payment->number;
			break;
	}

	/**
	 * Filter the {payment_id} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $payment_id
	 * @param array  $tag_args
	 */
	return apply_filters( 'give_email_tag_payment_id', $payment_id, $tag_args );
}

/**
 * Email template tag: {receipt_id}
 *
 * The unique ID number for this donation receipt
 *
 * @param array $tag_args
 *
 * @return string receipt_id
 */
function give_email_tag_receipt_id( $tag_args ) {

	$receipt_id = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment    = new Give_Payment( $tag_args['payment_id'] );
			$receipt_id = $payment->key;
			break;
	}

	/**
	 * Filter the {receipt_id} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $receipt_id
	 * @param array  $tag_args
	 */
	return apply_filters( 'give_email_tag_receipt_id', $receipt_id, $tag_args );
}

/**
 * Email template tag: {donation}
 *
 * Output the donation form name, and the donation level (if applicable).
 *
 * @param array $tag_args
 *
 * @return string $form_title
 */
function give_email_tag_donation( $tag_args ) {
	$donation_form_title = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment             = new Give_Payment( $tag_args['payment_id'] );
			$payment_meta        = $payment->payment_meta;
			$level_title         = give_has_variable_prices( $payment->form_id );
			$separator           = $level_title ? '-' : '';
			$donation_form_title = strip_tags( give_check_variable( give_get_payment_form_title( $payment_meta, false, $separator ), 'empty', '' ) );
			break;
	}

	/**
	 * Filter the {donation_form_title} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $donation_form_title
	 * @param array  $tag_args
	 */
	return apply_filters(
		'give_email_tag_donation',
		$donation_form_title,
		$tag_args
	);
}

/**
 * Email template tag: {form_title}
 *
 * Output the donation form name.
 *
 * @param array $tag_args
 *
 * @return string $form_title
 */
function give_email_tag_form_title( $tag_args ) {
	$donation_form_title = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment             = new Give_Payment( $tag_args['payment_id'] );
			$payment_meta        = $payment->payment_meta;
			$donation_form_title = strip_tags( give_check_variable( $payment_meta, 'empty', '', 'form_title' ) );
			break;
	}

	/**
	 * Filter the {form_title} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $form_title
	 * @param array  $tag_args
	 */
	return apply_filters(
		'give_email_tag_form_title',
		$donation_form_title,
		$tag_args
	);
}

/**
 * Email template tag: {payment_method}
 *
 * The method of payment used for this donation.
 *
 * @param array $tag_args
 *
 * @return string gateway
 */
function give_email_tag_payment_method( $tag_args ) {
	$payment_method = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment        = new Give_Payment( $tag_args['payment_id'] );
			$payment_method = $payment->gateway;
			break;
	}

	/**
	 * Filter the {payment_method} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $payment_method
	 * @param array  $tag_args
	 */
	return apply_filters(
		'give_email_tag_payment_method',
		$payment_method,
		$tag_args
	);

}

/**
 * Email template tag: {payment_total}
 *
 * The payment donation for this donation.
 *
 * @since 1.8
 *
 * @param array $tag_args
 *
 * @return string
 */
function give_email_tag_payment_total( $tag_args ) {
	$payment_total = '';

	switch ( true ) {
		case give_check_variable( $tag_args, 'isset', 0, 'payment_id' ):
			$payment        = new Give_Payment( $tag_args['payment_id'] );
			$payment_total = give_currency_filter( $payment->total );
			break;
	}

	/**
	 * Filter the {payment_total} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $payment_total
	 * @param array  $tag_args
	 */
	return apply_filters(
		'give_email_tag_payment_total',
		$payment_total,
		$tag_args
	);
}

/**
 * Email template tag: {sitename}
 *
 * The name of the site.
 *
 * @param array $tag_args
 *
 * @return string
 */
function give_email_tag_sitename( $tag_args = array() ) {
	$sitename = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

	/**
	 * Filter the {sitename} email template tag output.
	 *
	 * @since 1.9
	 *
	 * @param string $sitename
	 * @param array  $tag_args
	 */
	return apply_filters(
		'give_email_tag_sitename',
		$sitename,
		$tag_args
	);
}

/**
 * Email template tag: {receipt_link}
 *
 * The donation receipt direct link, to view the receipt on the website.
 *
 * @param int $payment_id
 *
 * @return string receipt_link
 */
function give_email_tag_receipt_link( $payment_id ) {

	$receipt_url = esc_url( add_query_arg( array(
		'payment_key' => give_get_payment_key( $payment_id ),
		'give_action' => 'view_receipt',
	), home_url() ) );
	$formatted   = sprintf(
		'<a href="%1$s">%2$s</a>',
		$receipt_url,
		esc_html__( 'View it in your browser', 'give' )
	);

	if ( give_get_option( 'email_template' ) !== 'none' ) {
		return $formatted;
	} else {
		return $receipt_url;
	}

}

/**
 * Email template tag: {receipt_link_url}
 *
 * The donation receipt direct URL, to view the receipt on the website.
 *
 * @since 1.7
 *
 * @param int $payment_id
 *
 * @return string receipt_url
 */
function give_email_tag_receipt_link_url( $payment_id ) {

	$receipt_url = esc_url( add_query_arg( array(
		'payment_key' => give_get_payment_key( $payment_id ),
		'give_action' => 'view_receipt',
	), home_url() ) );

	return $receipt_url;

}
