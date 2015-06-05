<?php
/**
 * Customer (Donors)
 *
 * @package     Give
 * @subpackage  Admin/Customers
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customers Page
 *
 * Renders the customers page contents.
 *
 * @since  1.0
 * @return void
 */
function give_customers_page() {
	$default_views  = give_customer_views();
	$requested_view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : 'customers';
	if ( array_key_exists( $requested_view, $default_views ) && function_exists( $default_views[ $requested_view ] ) ) {
		give_render_customer_view( $requested_view, $default_views );
	} else {
		give_customers_list();
	}
}

/**
 * Register the views for customer management
 *
 * @since  1.0
 * @return array Array of views and their callbacks
 */
function give_customer_views() {

	$views = array();

	return apply_filters( 'give_customer_views', $views );

}

/**
 * Register the tabs for customer management
 *
 * @since  1.0
 * @return array Array of tabs for the customer
 */
function give_customer_tabs() {

	$tabs = array();

	return apply_filters( 'give_customer_tabs', $tabs );

}

/**
 * List table of customers
 *
 * @since  1.0
 * @return void
 */
function give_customers_list() {
	include( dirname( __FILE__ ) . '/class-customer-table.php' );

	$customers_table = new Give_Customer_Reports_Table();
	$customers_table->prepare_items();
	?>
	<div class="wrap">
		<h2><?php _e( 'Customers', 'give' ); ?></h2>
		<?php do_action( 'give_customers_table_top' ); ?>
		<form id="give-customers-filter" method="get" action="<?php echo admin_url( 'edit.php?post_type=download&page=give-customers' ); ?>">
			<?php
			$customers_table->search_box( __( 'Search Customers', 'give' ), 'give-customers' );
			$customers_table->display();
			?>
			<input type="hidden" name="post_type" value="download" />
			<input type="hidden" name="page" value="give-customers" />
			<input type="hidden" name="view" value="customers" />
		</form>
		<?php do_action( 'give_customers_table_bottom' ); ?>
	</div>
<?php
}

/**
 * Renders the customer view wrapper
 *
 * @since  1.0
 *
 * @param  string $view      The View being requested
 * @param  array  $callbacks The Registered views and their callback functions
 *
 * @return void
 */
function give_render_customer_view( $view, $callbacks ) {

	$render = true;

	$customer_view_role = apply_filters( 'give_view_customers_role', 'view_shop_reports' );

	if ( ! current_user_can( $customer_view_role ) ) {
		give_set_error( 'give-no-access', __( 'You are not permitted to view this data.', 'give' ) );
		$render = false;
	}

	if ( ! isset( $_GET['id'] ) || ! is_numeric( $_GET['id'] ) ) {
		give_set_error( 'give-invalid_customer', __( 'Invalid Customer ID Provided.', 'give' ) );
		$render = false;
	}

	$customer_id = (int) $_GET['id'];
	$customer    = new Give_Customer( $customer_id );

	if ( empty( $customer->id ) ) {
		give_set_error( 'give-invalid_customer', __( 'Invalid Customer ID Provided.', 'give' ) );
		$render = false;
	}

	$customer_tabs = give_customer_tabs();
	?>

	<div class='wrap'>
		<h2><?php _e( 'Customer Details', 'give' ); ?></h2>
		<?php if ( give_get_errors() ) : ?>
			<div class="error settings-error">
				<?php give_print_errors(); ?>
			</div>
		<?php endif; ?>

		<?php if ( $customer && $render ) : ?>

			<div id="customer-tab-wrapper">
				<ul id="customer-tab-wrapper-list">
					<?php foreach ( $customer_tabs as $key => $tab ) : ?>
						<?php $active = $key === $view ? true : false; ?>
						<?php $class = $active ? 'active' : 'inactive'; ?>

						<?php if ( ! $active ) : ?>
							<a title="<?php echo esc_attr( $tab['title'] ); ?>" aria-label="<?php echo esc_attr( $tab['title'] ); ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=download&page=give-customers&view=' . $key . '&id=' . $customer->id ) ); ?>">
						<?php endif; ?>

						<li class="<?php echo sanitize_html_class( $class ); ?>">
							<span class="dashicons <?php echo sanitize_html_class( $tab['dashicon'] ); ?>"></span></li>

						<?php if ( ! $active ) : ?>
							</a>
						<?php endif; ?>

					<?php endforeach; ?>
				</ul>
			</div>

			<div id="give-customer-card-wrapper" style="float: left">
				<?php $callbacks[ $view ]( $customer ) ?>
			</div>

		<?php endif; ?>

	</div>
<?php

}


/**
 * View a customer
 *
 * @since  1.0
 *
 * @param  $customer The Customer object being displayed
 *
 * @return void
 */
function give_customers_view( $customer ) {

	$customer_edit_role = apply_filters( 'give_edit_customers_role', 'edit_shop_payments' );

	?>

	<?php do_action( 'give_customer_card_top', $customer ); ?>

	<div class="info-wrapper customer-section">

		<form id="edit-customer-info" method="post" action="<?php echo admin_url( 'edit.php?post_type=download&page=give-customers&view=overview&id=' . $customer->id ); ?>">

			<div class="customer-info">

				<div class="avatar-wrap left" id="customer-avatar">
					<?php echo get_avatar( $customer->email ); ?><br />
					<?php if ( current_user_can( $customer_edit_role ) ): ?>
						<span class="info-item editable customer-edit-link"><a title="<?php _e( 'Edit Customer', 'give' ); ?>" href="#" id="edit-customer"><?php _e( 'Edit Customer', 'give' ); ?></a></span>
					<?php endif; ?>
				</div>

				<div class="customer-id right">
					#<?php echo $customer->id; ?>
				</div>

				<div class="customer-address-wrapper right">
					<?php if ( isset( $customer->user_id ) && $customer->user_id > 0 ) : ?>

						<?php
						$address  = get_user_meta( $customer->user_id, '_give_user_address', true );
						$defaults = array(
							'line1'   => '',
							'line2'   => '',
							'city'    => '',
							'state'   => '',
							'country' => '',
							'zip'     => ''
						);

						$address = wp_parse_args( $address, $defaults );
						?>

						<?php if ( ! empty( $address ) ) : ?>
							<strong><?php _e( 'Customer Address', 'give' ); ?></strong>
							<span class="customer-address info-item editable">
						<span class="info-item" data-key="line1"><?php echo $address['line1']; ?></span>
						<span class="info-item" data-key="line2"><?php echo $address['line2']; ?></span>
						<span class="info-item" data-key="city"><?php echo $address['city']; ?></span>
						<span class="info-item" data-key="state"><?php echo $address['state']; ?></span>
						<span class="info-item" data-key="country"><?php echo $address['country']; ?></span>
						<span class="info-item" data-key="zip"><?php echo $address['zip']; ?></span>
					</span>
						<?php endif; ?>
						<span class="customer-address info-item edit-item">
						<input class="info-item" type="text" data-key="line1" name="customerinfo[line1]" placeholder="<?php _e( 'Address 1', 'give' ); ?>" value="<?php echo $address['line1']; ?>" />
						<input class="info-item" type="text" data-key="line2" name="customerinfo[line2]" placeholder="<?php _e( 'Address 2', 'give' ); ?>" value="<?php echo $address['line2']; ?>" />
						<input class="info-item" type="text" data-key="city" name="customerinfo[city]" placeholder="<?php _e( 'City', 'give' ); ?>" value="<?php echo $address['city']; ?>" />
						<select data-key="country" name="customerinfo[country]" id="billing_country" class="billing_country give-select edit-item">
							<?php

							$selected_country = $address['country'];

							$countries = give_get_country_list();
							foreach ( $countries as $country_code => $country ) {
								echo '<option value="' . esc_attr( $country_code ) . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
							}
							?>
						</select>
							<?php
							$selected_state = give_get_shop_state();
							$states         = give_get_shop_states( $selected_country );

							$selected_state = isset( $address['state'] ) ? $address['state'] : $selected_state;

							if ( ! empty( $states ) ) : ?>
								<select data-key="state" name="customerinfo[state]" id="card_state" class="card_state give-select info-item">
									<?php
									foreach ( $states as $state_code => $state ) {
										echo '<option value="' . $state_code . '"' . selected( $state_code, $selected_state, false ) . '>' . $state . '</option>';
									}
									?>
								</select>
							<?php else : ?>
								<input type="text" size="6" data-key="state" name="customerinfo[state]" id="card_state" class="card_state give-input info-item" placeholder="<?php _e( 'State / Province', 'give' ); ?>" />
							<?php endif; ?>
							<input class="info-item" type="text" data-key="zip" name="customerinfo[zip]" placeholder="<?php _e( 'Postal', 'give' ); ?>" value="<?php echo $address['zip']; ?>" />
					</span>
					<?php endif; ?>
				</div>

				<div class="customer-main-wrapper left">

					<span class="customer-name info-item edit-item"><input size="15" data-key="name" name="customerinfo[name]" type="text" value="<?php echo esc_attr( $customer->name ); ?>" placeholder="<?php _e( 'Customer Name', 'give' ); ?>" /></span>
					<span class="customer-name info-item editable"><span data-key="name"><?php echo $customer->name; ?></span></span>
					<span class="customer-name info-item edit-item"><input size="20" data-key="email" name="customerinfo[email]" type="text" value="<?php echo $customer->email; ?>" placeholder="<?php _e( 'Customer Email', 'give' ); ?>" /></span>
					<span class="customer-email info-item editable" data-key="email"><?php echo $customer->email; ?></span>
					<span class="customer-since info-item">
						<?php _e( 'Customer since', 'give' ); ?>
						<?php echo date_i18n( get_option( 'date_format' ), strtotime( $customer->date_created ) ) ?>
					</span>
					<span class="customer-user-id info-item edit-item">
						<?php

						$user_id   = $customer->user_id > 0 ? $customer->user_id : '';
						$data_atts = array( 'key' => 'user_login', 'exclude' => $user_id );
						$user_args = array(
							'name'  => 'customerinfo[user_login]',
							'class' => 'give-user-dropdown',
							'data'  => $data_atts,
						);

						if ( ! empty( $user_id ) ) {
							$userdata           = get_userdata( $user_id );
							$user_args['value'] = $userdata->user_login;
						}

						echo EDD()->html->ajax_user_search( $user_args );
						?>
						<input type="hidden" name="customerinfo[user_id]" data-key="user_id" value="<?php echo $customer->user_id; ?>" />
					</span>

					<span class="customer-user-id info-item editable">
						<?php _e( 'User ID', 'give' ); ?>:&nbsp;
						<?php if ( intval( $customer->user_id ) > 0 ) : ?>
							<span data-key="user_id"><?php echo $customer->user_id; ?></span>
						<?php else : ?>
							<span data-key="user_id"><?php _e( 'none', 'give' ); ?></span>
						<?php endif; ?>
						<?php if ( current_user_can( $customer_edit_role ) && intval( $customer->user_id ) > 0 ) : ?>
							<span class="disconnect-user"> - <a id="disconnect-customer" href="#disconnect" title="<?php _e( 'Disconnects the current user ID from this customer record', 'give' ); ?>"><?php _e( 'Disconnect User', 'give' ); ?></a></span>
						<?php endif; ?>
					</span>

				</div>

			</div>

			<span id="customer-edit-actions" class="edit-item">
				<input type="hidden" data-key="id" name="customerinfo[id]" value="<?php echo $customer->id; ?>" />
				<?php wp_nonce_field( 'edit-customer', '_wpnonce', false, true ); ?>
				<input type="hidden" name="give_action" value="edit-customer" />
				<input type="submit" id="give-edit-customer-save" class="button-secondary" value="<?php _e( 'Update Customer', 'give' ); ?>" />
				<a id="give-edit-customer-cancel" href="" class="delete"><?php _e( 'Cancel', 'give' ); ?></a>
			</span>

		</form>
	</div>

	<?php do_action( 'give_customer_before_stats', $customer ); ?>

	<div id="customer-stats-wrapper" class="customer-section">
		<ul>
			<li>
				<a title="<?php _e( 'View All Purchases', 'give' ); ?>" href="<?php echo admin_url( 'edit.php?post_type=download&page=give-payment-history&user=' . urlencode( $customer->email ) ); ?>">
					<span class="dashicons dashicons-cart"></span>
					<?php printf( _n( '%d Completed Sale', '%d Completed Sales', $customer->purchase_count, 'give' ), $customer->purchase_count ); ?>
				</a>
			</li>
			<li>
				<span class="dashicons dashicons-chart-area"></span>
				<?php echo give_currency_filter( give_format_amount( $customer->purchase_value ) ); ?> <?php _e( 'Lifetime Value', 'give' ); ?>
			</li>
			<?php do_action( 'give_customer_stats_list', $customer ); ?>
		</ul>
	</div>

	<?php do_action( 'give_customer_before_tables_wrapper', $customer ); ?>

	<div id="customer-tables-wrapper" class="customer-section">

		<?php do_action( 'give_customer_before_tables', $customer ); ?>

		<h3><?php _e( 'Recent Payments', 'give' ); ?></h3>
		<?php
		$payment_ids = explode( ',', $customer->payment_ids );
		$payments    = give_get_payments( array( 'post__in' => $payment_ids ) );
		$payments    = array_slice( $payments, 0, 10 );
		?>
		<table class="wp-list-table widefat striped payments">
			<thead>
			<tr>
				<th><?php _e( 'ID', 'give' ); ?></th>
				<th><?php _e( 'Amount', 'give' ); ?></th>
				<th><?php _e( 'Date', 'give' ); ?></th>
				<th><?php _e( 'Status', 'give' ); ?></th>
				<th><?php _e( 'Actions', 'give' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $payments ) ) : ?>
				<?php foreach ( $payments as $payment ) : ?>
					<tr>
						<td><?php echo $payment->ID; ?></td>
						<td><?php echo give_payment_amount( $payment->ID ); ?></td>
						<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $payment->post_date ) ); ?></td>
						<td><?php echo give_get_payment_status( $payment, true ); ?></td>
						<td>
							<a title="<?php _e( 'View Details for Payment', 'give' );
							echo ' ' . $payment->ID; ?>" href="<?php echo admin_url( 'edit.php?post_type=download&page=give-payment-history&view=view-order-details&id=' . $payment->ID ); ?>">
								<?php _e( 'View Details', 'give' ); ?>
							</a>
							<?php do_action( 'give_customer_recent_purcahses_actions', $customer, $payment ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="5"><?php _e( 'No Payments Found', 'give' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>

		<h3><?php printf( __( 'Purchased %s', 'give' ), give_get_label_plural() ); ?></h3>
		<?php
		$downloads = give_get_users_purchased_products( $customer->email );
		?>
		<table class="wp-list-table widefat striped downloads">
			<thead>
			<tr>
				<th><?php echo give_get_label_singular(); ?></th>
				<th width="120px"><?php _e( 'Actions', 'give' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $downloads ) ) : ?>
				<?php foreach ( $downloads as $download ) : ?>
					<tr>
						<td><?php echo $download->post_title; ?></td>
						<td>
							<a title="<?php echo esc_attr( sprintf( __( 'View %s', 'give' ), $download->post_title ) ); ?>" href="<?php echo esc_url( admin_url( 'post.php?action=edit&post=' . $download->ID ) ); ?>">
								<?php printf( __( 'View %s', 'give' ), give_get_label_singular() ); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="2"><?php printf( __( 'No %s Found', 'give' ), give_get_label_plural() ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>

		<?php do_action( 'give_customer_after_tables', $customer ); ?>

	</div>

	<?php do_action( 'give_customer_card_bottom', $customer ); ?>

<?php
}

/**
 * View the notes of a customer
 *
 * @since  1.0
 *
 * @param  $customer The Customer being displayed
 *
 * @return void
 */
function give_customer_notes_view( $customer ) {

	$paged       = isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) ? $_GET['paged'] : 1;
	$paged       = absint( $paged );
	$note_count  = $customer->get_notes_count();
	$per_page    = apply_filters( 'give_customer_notes_per_page', 20 );
	$total_pages = ceil( $note_count / $per_page );

	$customer_notes = $customer->get_notes( $per_page, $paged );
	?>

	<div id="customer-notes-wrapper">
		<div class="customer-notes-header">
			<?php echo get_avatar( $customer->email, 30 ); ?> <span><?php echo $customer->name; ?></span>
		</div>
		<h3><?php _e( 'Notes', 'give' ); ?></h3>

		<?php if ( 1 == $paged ) : ?>
			<div style="display: block; margin-bottom: 35px;">
				<form id="give-add-customer-note" method="post" action="<?php echo admin_url( 'edit.php?post_type=download&page=give-customers&view=notes&id=' . $customer->id ); ?>">
					<textarea id="customer-note" name="customer_note" class="customer-note-input" rows="10"></textarea>
					<br />
					<input type="hidden" id="customer-id" name="customer_id" value="<?php echo $customer->id; ?>" />
					<input type="hidden" name="give_action" value="add-customer-note" />
					<?php wp_nonce_field( 'add-customer-note', 'add_customer_note_nonce', true, true ); ?>
					<input id="add-customer-note" class="right button-primary" type="submit" value="Add Note" />
				</form>
			</div>
		<?php endif; ?>

		<?php
		$pagination_args = array(
			'base'     => '%_%',
			'format'   => '?paged=%#%',
			'total'    => $total_pages,
			'current'  => $paged,
			'show_all' => true
		);

		echo paginate_links( $pagination_args );
		?>

		<div id="give-customer-notes">
			<?php if ( count( $customer_notes ) > 0 ) : ?>
				<?php foreach ( $customer_notes as $key => $note ) : ?>
					<div class="customer-note-wrapper dashboard-comment-wrap comment-item">
					<span class="note-content-wrap">
						<?php echo stripslashes( $note ); ?>
					</span>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="give-no-customer-notes">
					<?php _e( 'No Customer Notes', 'give' ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php echo paginate_links( $pagination_args ); ?>

	</div>

<?php
}

function give_customers_delete_view( $customer ) {
	$customer_edit_role = apply_filters( 'give_edit_customers_role', 'edit_shop_payments' );

	?>

	<?php do_action( 'give_customer_delete_top', $customer ); ?>

	<div class="info-wrapper customer-section">

		<form id="delete-customer" method="post" action="<?php echo admin_url( 'edit.php?post_type=download&page=give-customers&view=delete&id=' . $customer->id ); ?>">

			<div class="customer-notes-header">
				<?php echo get_avatar( $customer->email, 30 ); ?> <span><?php echo $customer->name; ?></span>
			</div>


			<div class="customer-info delete-customer">

				<span class="delete-customer-options">
					<p>
						<?php echo EDD()->html->checkbox( array( 'name' => 'give-customer-delete-confirm' ) ); ?>
						<label for="give-customer-delete-confirm"><?php _e( 'Are you sure you want to delete this customer?', 'give' ); ?></label>
					</p>

					<p>
						<?php echo EDD()->html->checkbox( array(
							'name'    => 'give-customer-delete-records',
							'options' => array( 'disabled' => true )
						) ); ?>
						<label for="give-customer-delete-records"><?php _e( 'Delete all associated payments and records?', 'give' ); ?></label>
					</p>

					<?php do_action( 'give_customer_delete_inputs', $customer ); ?>
				</span>

				<span id="customer-edit-actions">
					<input type="hidden" name="customer_id" value="<?php echo $customer->id; ?>" />
					<?php wp_nonce_field( 'delete-customer', '_wpnonce', false, true ); ?>
					<input type="hidden" name="give_action" value="delete-customer" />
					<input type="submit" disabled="disabled" id="give-delete-customer" class="button-primary" value="<?php _e( 'Delete Customer', 'give' ); ?>" />
					<a id="give-delete-customer-cancel" href="<?php echo admin_url( 'edit.php?post_type=download&page=give-customers&view=overview&id=' . $customer->id ); ?>" class="delete"><?php _e( 'Cancel', 'give' ); ?></a>
				</span>

			</div>

		</form>
	</div>

	<?php

	do_action( 'give_customer_delete_bottom', $customer );
}
