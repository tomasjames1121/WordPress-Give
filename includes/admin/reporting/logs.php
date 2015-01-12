<?php
/**
 * Logs UI
 *
 * @package     EDD
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Sales Log View
 *
 * @since 1.4
 * @uses EDD_Sales_Log_Table::prepare_items()
 * @uses EDD_Sales_Log_Table::display()
 * @return void
 */
function give_logs_view_sales() {
	include( dirname( __FILE__ ) . '/class-sales-logs-list-table.php' );

	$logs_table = new EDD_Sales_Log_Table();
	$logs_table->prepare_items();
	$logs_table->display();

}
add_action( 'give_logs_view_sales', 'give_logs_view_sales' );

/**
 * File Download Logs
 *
 * @since 1.4
 * @uses EDD_File_Downloads_Log_Table::prepare_items()
 * @uses EDD_File_Downloads_Log_Table::search_box()
 * @uses EDD_File_Downloads_Log_Table::display()
 * @return void
 */
function give_logs_view_file_downloads() {
	include( dirname( __FILE__ ) . '/class-file-downloads-logs-list-table.php' );

	$logs_table = new EDD_File_Downloads_Log_Table();
	$logs_table->prepare_items();
	?>
	<div class="wrap">
		<?php do_action( 'give_logs_file_downloads_top' ); ?>
		<form id="give-logs-filter" method="get" action="<?php echo admin_url( 'edit.php?post_type=download&page=give-reports&tab=logs' ); ?>">
			<?php
			$logs_table->search_box( __( 'Search', 'give' ), 'give-payments' );
			$logs_table->display();
			?>
			<input type="hidden" name="post_type" value="download" />
			<input type="hidden" name="page" value="give-reports" />
			<input type="hidden" name="tab" value="logs" />
		</form>
		<?php do_action( 'give_logs_file_downloads_bottom' ); ?>
	</div>
<?php
}
add_action( 'give_logs_view_file_downloads', 'give_logs_view_file_downloads' );

/**
 * Gateway Error Logs
 *
 * @since 1.4
 * @uses EDD_File_Downloads_Log_Table::prepare_items()
 * @uses EDD_File_Downloads_Log_Table::display()
 * @return void
 */
function give_logs_view_gateway_errors() {
	include( dirname( __FILE__ ) . '/class-gateway-error-logs-list-table.php' );

	$logs_table = new EDD_Gateway_Error_Log_Table();
	$logs_table->prepare_items();
	$logs_table->display();
}
add_action( 'give_logs_view_gateway_errors', 'give_logs_view_gateway_errors' );

/**
 * API Request Logs
 *
 * @since 1.5
 * @uses EDD_API_Request_Log_Table::prepare_items()
 * @uses EDD_API_Request_Log_Table::search_box()
 * @uses EDD_API_Request_Log_Table::display()
 * @return void
 */

function give_logs_view_api_requests() {
	include( dirname( __FILE__ ) . '/class-api-requests-logs-list-table.php' );

	$logs_table = new EDD_API_Request_Log_Table();
	$logs_table->prepare_items();
	?>
	<div class="wrap">
		<?php do_action( 'give_logs_api_requests_top' ); ?>
		<form id="give-logs-filter" method="get" action="<?php echo admin_url( 'edit.php?post_type=download&page=give-reports&tab=logs' ); ?>">
			<?php
			$logs_table->search_box( __( 'Search', 'give' ), 'give-api-requests' );
			$logs_table->display();
			?>
			<input type="hidden" name="post_type" value="download" />
			<input type="hidden" name="page" value="give-reports" />
			<input type="hidden" name="tab" value="logs" />
		</form>
		<?php do_action( 'give_logs_api_requests_bottom' ); ?>
	</div>
<?php
}
add_action( 'give_logs_view_api_requests', 'give_logs_view_api_requests' );


/**
 * Default Log Views
 *
 * @since 1.4
 * @return array $views Log Views
 */
function give_log_default_views() {
	$views = array(
		'file_downloads'  => __( 'File Downloads', 'give' ),
		'sales' 		  => __( 'Sales', 'give' ),
		'gateway_errors'  => __( 'Payment Errors', 'give' ),
		'api_requests'    => __( 'API Requests', 'give' )
	);

	$views = apply_filters( 'give_log_views', $views );

	return $views;
}

/**
 * Renders the Reports page views drop down
 *
 * @since 1.3
 * @return void
*/
function give_log_views() {
	$views        = give_log_default_views();
	$current_view = isset( $_GET['view'] ) && array_key_exists( $_GET['view'], give_log_default_views() ) ? sanitize_text_field( $_GET['view'] ) : 'file_downloads';
	?>
	<form id="give-logs-filter" method="get" action="edit.php">
		<select id="give-logs-view" name="view">
			<option value="-1"><?php _e( 'Log Type', 'give' ); ?></option>
			<?php foreach ( $views as $view_id => $label ): ?>
				<option value="<?php echo esc_attr( $view_id ); ?>" <?php selected( $view_id, $current_view ); ?>><?php echo $label; ?></option>
			<?php endforeach; ?>
		</select>

		<?php do_action( 'give_log_view_actions' ); ?>

		<input type="hidden" name="post_type" value="download"/>
		<input type="hidden" name="page" value="give-reports"/>
		<input type="hidden" name="tab" value="logs"/>

		<?php submit_button( __( 'Apply', 'give' ), 'secondary', 'submit', false ); ?>
	</form>
	<?php
}