<?php
// Reports page markup
// #reports-app is replaced by React app
?>
<div id="reports-app">
	<div class="wrap give-settings-page">
		<div class="give-settings-header">
			<h1 class="wp-heading-inline">
				<?php _e( 'Reports', 'give' ); ?>
			</h1>
			<div class="givewp-period-selector">
				<div class="group">
					<button>
						<?php _e( 'Day', 'give' ); ?>
					</button>
					<button class="selected">
						<?php _e( 'Week', 'give' ); ?>
					</button>
					<button>
						<?php _e( 'Month', 'give' ); ?>
					</button>
					<button>
						<?php _e( 'Year', 'give' ); ?>
					</button>
				</div>
			</div>
		</div>
		<div class="nav-tab-wrapper give-nav-tab-wrapper" style="height: auto; overflow: visible;">
			<a class="nav-tab nav-tab-active" href="#/">
				<?php _e( 'Overview', 'give' ); ?>
			</a>
			<a class="nav-tab" href="http://givewp.local/wp-admin/edit.php?post_type=give_forms&amp;page=give-reports">
				<?php _e( 'Legacy Reports Page', 'give' ); ?>
			</a>
		</div>
		<div class="givewp-loading-notice">
			<h2><?php _e( 'Loading...', 'give' ); ?></h2>
		</div>
	</div>
</div>
