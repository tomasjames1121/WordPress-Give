<?php
/**
 * Payments Export Class
 *
 * This class handles payment export in batches
 *
 * @package     Give
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Give_Batch_Payments_Export Class
 *
 * @since 1.5
 */
class Give_Batch_Payments_Export extends Give_Batch_Export {

	/**
	 * Our export type. Used for export-type specific filters/actions
	 * @var string
	 * @since 1.5
	 */
	public $export_type = 'payments';

	/**
	 * Set the CSV columns
	 *
	 * @access public
	 * @since 1.5
	 * @return array $cols All the columns
	 */
	public function csv_cols() {
		$cols = array(
			'id'       => esc_html( 'ID', 'give' ), // unaltered payment ID (use for querying)
			'seq_id'   => esc_html( 'Payment Number', 'give' ), // sequential payment ID
			'email'    => esc_html( 'Email', 'give' ),
			'first'    => esc_html( 'First Name', 'give' ),
			'last'     => esc_html( 'Last Name', 'give' ),
			'address1' => esc_html( 'Address', 'give' ),
			'address2' => esc_html( 'Address (Line 2)', 'give' ),
			'city'     => esc_html( 'City', 'give' ),
			'state'    => esc_html( 'State', 'give' ),
			'country'  => esc_html( 'Country', 'give' ),
			'zip'      => esc_html( 'Zip / Postal Code', 'give' ),
			'products' => esc_html( 'Products', 'give' ),
			'amount'   => esc_html( 'Amount', 'give' ) . ' (' . html_entity_decode( give_currency_filter( '' ) ) . ')',
			'gateway'  => esc_html( 'Payment Method', 'give' ),
			'trans_id' => esc_html( 'Transaction ID', 'give' ),
			'key'      => esc_html( 'Purchase Key', 'give' ),
			'date'     => esc_html( 'Date', 'give' ),
			'user'     => esc_html( 'User', 'give' ),
			'status'   => esc_html( 'Status', 'give' )
		);

		if ( ! give_get_option( 'enable_sequential' ) ) {
			unset( $cols['seq_id'] );
		}

		return $cols;
	}

	/**
	 * Get the Export Data
	 *
	 * @access public
	 * @since 1.5
	 * @global object $wpdb Used to query the database using the WordPress
	 *   Database API
	 * @return array $data The data for the CSV file
	 */
	public function get_data() {
		global $wpdb;

		$data = array();

		$args = array(
			'number' => 30,
			'page'   => $this->step,
			'status' => $this->status
		);

		if ( ! empty( $this->start ) || ! empty( $this->end ) ) {

			$args['date_query'] = array(
				array(
					'after'     => date( 'Y-n-d 00:00:00', strtotime( $this->start ) ),
					'before'    => date( 'Y-n-d 23:59:59', strtotime( $this->end ) ),
					'inclusive' => true
				)
			);

		}

		//echo json_encode($args ); exit;

		$payments = give_get_payments( $args );

		if ( $payments ) {

			foreach ( $payments as $payment ) {
				$payment_meta = give_get_payment_meta( $payment->ID );
				$user_info    = give_get_payment_meta_user_info( $payment->ID );
				$total        = give_get_payment_amount( $payment->ID );
				$user_id      = isset( $user_info['id'] ) && $user_info['id'] != - 1 ? $user_info['id'] : $user_info['email'];
				$products     = '';
				$skus         = '';

				if ( $downloads ) {
					foreach ( $downloads as $key => $download ) {

						// Form ID
						$id  = isset( $payment_meta['cart_details'] ) ? $download['id'] : $download;
						$qty = isset( $download['quantity'] ) ? $download['quantity'] : 1;

						if ( isset( $download['price'] ) ) {
							$price = $download['price'];
						} else {
							// If the download has variable prices, override the default price
							$price_override = isset( $payment_meta['cart_details'] ) ? $download['price'] : null;
							$price          = give_get_download_final_price( $id, $user_info, $price_override );
						}


						// Display the Downoad Name
						$products .= html_entity_decode( get_the_title( $id ) );

						if ( $qty > 1 ) {
							$products .= html_entity_decode( ' (' . $qty . ')' );
						}

						$products .= ' - ';

						if ( give_use_skus() ) {
							$sku = give_get_download_sku( $id );

							if ( ! empty( $sku ) ) {
								$skus .= $sku;
							}
						}

						if ( isset( $downloads[ $key ]['item_number'] ) && isset( $downloads[ $key ]['item_number']['options'] ) ) {
							$price_options = $downloads[ $key ]['item_number']['options'];

							if ( isset( $price_options['price_id'] ) ) {
								$products .= html_entity_decode( give_get_price_option_name( $id, $price_options['price_id'], $payment->ID ) ) . ' - ';
							}
						}

						$products .= html_entity_decode( give_currency_filter( give_format_amount( $price ) ) );

						if ( $key != ( count( $downloads ) - 1 ) ) {
							$products .= ' / ';

							if ( give_use_skus() ) {
								$skus .= ' / ';
							}
						}
					}
				}

				if ( is_numeric( $user_id ) ) {
					$user = get_userdata( $user_id );
				} else {
					$user = false;
				}

				$data[] = array(
					'id'       => $payment->ID,
					'seq_id'   => give_get_payment_number( $payment->ID ),
					'email'    => $payment_meta['email'],
					'first'    => $user_info['first_name'],
					'last'     => $user_info['last_name'],
					'address1' => isset( $user_info['address']['line1'] ) ? $user_info['address']['line1'] : '',
					'address2' => isset( $user_info['address']['line2'] ) ? $user_info['address']['line2'] : '',
					'city'     => isset( $user_info['address']['city'] ) ? $user_info['address']['city'] : '',
					'state'    => isset( $user_info['address']['state'] ) ? $user_info['address']['state'] : '',
					'country'  => isset( $user_info['address']['country'] ) ? $user_info['address']['country'] : '',
					'zip'      => isset( $user_info['address']['zip'] ) ? $user_info['address']['zip'] : '',
					'products' => $products,
					'skus'     => $skus,
					'amount'   => html_entity_decode( give_format_amount( $total ) ),
					'gateway'  => give_get_gateway_admin_label( get_post_meta( $payment->ID, '_give_payment_gateway', true ) ),
					'trans_id' => give_get_payment_transaction_id( $payment->ID ),
					'key'      => $payment_meta['key'],
					'date'     => $payment->post_date,
					'user'     => $user ? $user->display_name : esc_html( 'guest', 'give' ),
					'status'   => give_get_payment_status( $payment, true )
				);

			}

			$data = apply_filters( 'give_export_get_data', $data );
			$data = apply_filters( 'give_export_get_data_' . $this->export_type, $data );

			return $data;

		}

		return false;

	}

	/**
	 * Return the calculated completion percentage
	 *
	 * @since 1.5
	 * @return int
	 */
	public function get_percentage_complete() {

		$status = $this->status;
		$args   = array(
			'start-date' => date( 'n/d/Y', strtotime( $this->start ) ),
			'end-date'   => date( 'n/d/Y', strtotime( $this->end ) ),
		);

		if ( 'any' == $status ) {

			$total = array_sum( (array) give_count_payments( $args ) );

		} else {

			$total = give_count_payments( $args )->$status;

		}

		$percentage = 100;

		if ( $total > 0 ) {
			$percentage = ( ( 30 * $this->step ) / $total ) * 100;
		}

		if ( $percentage > 100 ) {
			$percentage = 100;
		}

		return $percentage;
	}

	/**
	 * Set the properties specific to the payments export
	 *
	 * @since 1.5
	 *
	 * @param array $request The Form Data passed into the batch processing
	 */
	public function set_properties( $request ) {
		$this->start  = isset( $request['start'] ) ? sanitize_text_field( $request['start'] ) : '';
		$this->end    = isset( $request['end'] ) ? sanitize_text_field( $request['end'] ) : '';
		$this->status = isset( $request['status'] ) ? sanitize_text_field( $request['status'] ) : 'complete';
	}
}
