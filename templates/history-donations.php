<?php
/**
 * This template is used to display the donation history of the current user.
 */

$donation_history_args = Give()->session->get( 'give_donation_history_args' );

$donations = array();

// User's Donations.
if ( is_user_logged_in() ) {
	$donations = give_get_users_donations( get_current_user_id(), 20, true, 'any' );
} elseif ( Give()->email_access->token_exists ) {
	// Email Access Token?
	$donations = give_get_users_donations( 0, 20, true, 'any' );
} elseif ( Give()->session->get_session_expiration() !== false ) {
	// Session active?
	$email     = Give()->session->get( 'give_email' );
	$donations = give_get_users_donations( $email, 20, true, 'any' );
}


if ( $donations ) : ?>
	<?php
	$table_headings = array(
		'id'             => __( 'ID', 'give' ),
		'date'           => __( 'Date', 'give' ),
		'donor'          => __( 'Donor', 'give' ),
		'amount'         => __( 'Amount', 'give' ),
		'status'         => __( 'Status', 'give' ),
		'payment_method' => __( 'Payment Method', 'give' ),
		'details'        => __( 'Details', 'give' ),
	);
	?>
	<table id="give_user_history" class="give-table">
		<thead>
			<tr class="give-donation-row">
				<?php
				/**
				 * Fires in current user donation history table, before the header row start.
				 *
				 * Allows you to add new <th> elements to the header, before other headers in the row.
				 *
				 * @since 1.7
				 */
				do_action( 'give_donation_history_header_before' );

				foreach ( $donation_history_args as $index => $value ) {
					if ( filter_var( $donation_history_args[ $index ], FILTER_VALIDATE_BOOLEAN ) ) :
						echo sprintf(
							'<th scope="col" class="give-donation-%1$s>">%2$s</th>',
							$index,
							$table_headings[ $index ]
						);
					endif;
				}

				/**
				 * Fires in current user donation history table, after the header row ends.
				 *
				 * Allows you to add new <th> elements to the header, after other headers in the row.
				 *
				 * @since 1.7
				 */
				do_action( 'give_donation_history_header_after' );
				?>
			</tr>
		</thead>
		<?php foreach ( $donations as $post ) :
			setup_postdata( $post );
			$donation_data = give_get_payment_meta( $post->ID ); ?>
			<tr class="give-donation-row">
				<?php
				/**
				 * Fires in current user donation history table, before the row statrs.
				 *
				 * Allows you to add new <td> elements to the row, before other elements in the row.
				 *
				 * @since 1.7
				 *
				 * @param int   $post_id       The ID of the post.
				 * @param mixed $donation_data Payment meta data.
				 */
				do_action( 'give_donation_history_row_start', $post->ID, $donation_data );

				if ( filter_var( $donation_history_args['id'], FILTER_VALIDATE_BOOLEAN ) ) :
					echo sprintf(
						'<td class="give-donation-id">#%s</td>',
						give_get_payment_number( $post->ID )
					);
				endif;

				if ( filter_var( $donation_history_args['date'], FILTER_VALIDATE_BOOLEAN ) ) :
					echo sprintf(
						'<td class="give-donation-date">#%s</td>',
						date_i18n( give_date_format(), strtotime( get_post_field( 'post_date', $post->ID ) ) )
					);
				endif;

				if ( filter_var( $donation_history_args['donor'], FILTER_VALIDATE_BOOLEAN ) ) :
					echo sprintf(
						'<td class="give-donation-donor">#%s</td>',
						give_get_donor_name_by( $post->ID )
					);
				endif;
				?>

				<?php if ( filter_var( $donation_history_args['amount'], FILTER_VALIDATE_BOOLEAN ) ) : ?>
					<td class="give-donation-amount">
						<span class="give-donation-amount">
							<?php
							$currency_code   = give_get_payment_currency_code( $post->ID );
							$donation_amount = give_currency_filter(
								give_format_amount( give_get_payment_amount( $post->ID ), array(
									'sanitize'    => false,
									'currency'    => $currency_code,
									'donation_id' => $post->ID,
								)),
								$currency_code
							);

							/**
							 * Filters the donation amount on Donation History Page.
							 *
							 * @param int $donation_amount Donation Amount.
							 * @param int $post_id         Donation ID.
							 *
							 * @since 1.8.13
							 *
							 * @return int
							 */
							echo apply_filters( 'give_donation_history_row_amount', $donation_amount, $post->ID );
							?>
						</span>
					</td>
				<?php endif; ?>

				<?php
				if ( filter_var( $donation_history_args['status'], FILTER_VALIDATE_BOOLEAN ) ) :
					echo sprintf(
						'<td class="give-donation-status">#%s</td>',
						give_get_payment_status( $post, true )
					);
				endif;

				if ( filter_var( $donation_history_args['payment_method'], FILTER_VALIDATE_BOOLEAN ) ) :
					echo sprintf(
						'<td class="give-donation-payment-method">#%s</td>',
						give_get_gateway_checkout_label( give_get_payment_gateway( $post->ID ) )
					);
				endif;
				?>
				<td class="give-donation-details">
					<?php
					// Display View Receipt or.
					if ( 'publish' !== $post->post_status && 'subscription' !== $post->post_status ) :
						echo sprintf(
							'<a href="%1$s"><span class="give-donation-status %2$s">%3$s</span></a>',
							esc_url(
								add_query_arg(
									'payment_key',
									give_get_payment_key( $post->ID ),
									give_get_history_page_uri()
								)
							),
							$post->post_status,
							__( 'View', 'give' ) . ' ' . give_get_payment_status( $post, true ) . ' &raquo;'
						);

					else :
						echo sprintf(
							'<a href="%1$s">%2$s</a>',
							esc_url(
								add_query_arg(
									'payment_key',
									give_get_payment_key( $post->ID ),
									give_get_history_page_uri()
								)
							),
							__( 'View Receipt &raquo;', 'give' )
						);

					endif;
					?>
				</td>
				<?php
				/**
				 * Fires in current user donation history table, after the row ends.
				 *
				 * Allows you to add new <td> elements to the row, after other elements in the row.
				 *
				 * @since 1.7
				 *
				 * @param int   $post_id       The ID of the post.
				 * @param mixed $donation_data Payment meta data.
				 */
				do_action( 'give_donation_history_row_end', $post->ID, $donation_data );
				?>
			</tr>
		<?php endforeach; ?>
	</table>
	<div id="give-donation-history-pagination" class="give_pagination navigation">
		<?php
		$big = 999999;
		echo paginate_links( array(
			'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'  => '?paged=%#%',
			'current' => max( 1, get_query_var( 'paged' ) ),
			'total'   => ceil( give_count_donations_of_donor() / 20 ) // 20 items per page
		) );
		?>
	</div>
	<?php wp_reset_postdata(); ?>
<?php else : ?>
	<?php Give()->notices->print_frontend_notice( __( 'It looks like you haven\'t made any donations.', 'give' ), true, 'success' ); ?>
<?php endif;
