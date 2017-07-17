<?php
/**
 * Upgrade Screen
 *
 * @package     Give
 * @subpackage  Admin/Upgrades
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$action = isset( $_GET['give-upgrade'] ) ? sanitize_text_field( $_GET['give-upgrade'] ) : '';
$step   = isset( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;
$total  = isset( $_GET['total'] ) ? absint( $_GET['total'] ) : false;
$custom = isset( $_GET['custom'] ) ? absint( $_GET['custom'] ) : 0;
$number = isset( $_GET['number'] ) ? absint( $_GET['number'] ) : 100;
$steps  = round( ( $total / $number ), 0 );

$doing_upgrade_args = array(
	'page'         => 'give-upgrades',
	'give-upgrade' => $action,
	'step'         => $step,
	'total'        => $total,
	'custom'       => $custom,
	'steps'        => $steps
);
update_option( 'give_doing_upgrade', $doing_upgrade_args );

if ( $step > $steps ) {
	// Prevent a weird case where the estimate was off. Usually only a couple.
	$steps = $step;
}

$give_updates = Give_Updates::get_instance();
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Give - Upgrades', 'give' ); ?></h1>
	<div id="give-updates" class="panel-content">
		<p>
			<?php _e( 'You have updates. Please <a href="%s">create a backup</a> of your site before updating. It is important to always stay up-to-date with latest versions of Give core and it\'s add-ons. We regularly release new features, bug fixes, and enhancements. To update add-ons be sure your <a href="%s">license keys</a> are active.', 'give' ); ?>
		</p>
	</div>

	<?php $update_counter = 1;?>

	<?php $db_updates = $give_updates->get_updates('database'); ?>
	<?php if( ! empty( $db_updates ) ) : ?>
		<?php $db_update_url = add_query_arg( array( 'type' => 'database' ) ); ?>
		<div id="give-db-updates">
			<h2><?php _e( 'Database Updates', 'give' ); ?></h2>
			<div class="panel-content">
				<p>
					<?php echo sprintf( __( 'Give needs to update the database. <a href="%s">Update now ></a>', 'give' ), $db_update_url ); ?>
				</p>
			</div>
		</div>
	<?php endif; ?>

	<?php $plugin_updates = $give_updates->get_plugin_update_count(); ?>
	<?php if( ! empty( $plugin_updates ) ) : ?>
	<?php $plugin_update_url = add_query_arg( array( 's' => 'Give' ), admin_url( '/plugins.php' ) ); ?>
	<div id="give-db-updates">
		<h2><?php _e( 'Plugin Updates', 'give' ); ?></h2>
		<div class="panel-content">
			<p>
				<?php echo sprintf( __( 'There are %s Give %s that need to be updated. <a href="%s">Update now ></a>', 'give' ), $plugin_updates, _n( 'addon', 'addons', $plugin_updates, 'give' ), $plugin_update_url ); ?>
			</p>
		</div>
	</div>
	<?php endif; ?>

</div>
