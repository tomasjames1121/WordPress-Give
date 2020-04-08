<?php
/**
 * Give Form Widget
 *
 * @package     GiveWP
 * @subpackage  Admin/Forms
 * @copyright   Copyright (c) 2016, GiveWP
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Give Form widget
 *
 * @since 1.0
 */
class Give_Forms_Widget extends WP_Widget {

	/**
	 * The widget class name
	 *
	 * @var string
	 */
	protected $self;

	/**
	 * Instantiate the class
	 */
	public function __construct() {
		$this->self = get_class( $this );

		parent::__construct(
			strtolower( $this->self ),
			esc_html__( 'GiveWP - Donation Form', 'give' ),
			array(
				'description' => esc_html__( 'Display a GiveWP Donation Form in your theme\'s widget powered sidebar.', 'give' ),
			)
		);

		add_action( 'widgets_init', array( $this, 'widget_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_widget_scripts' ) );
	}

	/**
	 * Load widget assets only on the widget page
	 *
	 * @param string $hook Use it to target a specific admin page.
	 *
	 * @return void
	 */
	public function admin_widget_scripts( $hook ) {

		// Directories of assets.
		$js_dir = GIVE_PLUGIN_URL . 'assets/dist/js/';

		// Use minified libraries if SCRIPT_DEBUG is turned off.
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Widget Script.
		if ( 'widgets.php' === $hook ) {

			wp_enqueue_script( 'give-admin-widgets-scripts', $js_dir . 'admin-widgets' . $suffix . '.js', array( 'jquery' ), GIVE_VERSION, false );
		}
	}

	/**
	 * Echo the widget content.
	 *
	 * @param array $args     Display arguments including before_title, after_title,
	 *                        before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		$title   = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$title   = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$form_id = (int) $instance['id'];

		echo $args['before_widget']; // XSS ok.

		/**
		 * Fires before widget settings form in the admin area.
		 *
		 * @param integer $form_id Form ID.
		 *
		 * @since 1.0
		 */
		do_action( 'give_before_forms_widget', $form_id );

		echo $title ? $args['before_title'] . $title . $args['after_title'] : ''; // XSS ok.

		echo give_form_shortcode( $instance );

		echo $args['after_widget']; // XSS ok.

		/**
		 * Fires after widget settings form in the admin area.
		 *
		 * @param integer $form_id Form ID.
		 *
		 * @since 1.0
		 */
		do_action( 'give_after_forms_widget', $form_id );
	}

	/**
	 * Output the settings update form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'                 => '',
			'id'                    => 0,
			'float_labels'          => 'global',
			'display_style'         => 'modal',
			'show_content'          => 'none',
			'continue_button_title' => __( 'Continue', 'give' ),
			'introduction_text'     => __( 'Help our organization by donating today. all contributions go directly to making a difference for our cause', 'give' ),
			'button_text'           => __( 'Donate Now', 'give' ),
			'button_color'          => '',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		// Backward compatibility: Set float labels as default if, it was set as empty previous.
		$instance['float_labels'] = empty( $instance['float_labels'] ) ? 'global' : $instance['float_labels'];
		?>
		<div class="give_forms_widget_container"

			<?php // Widget: widget Title. ?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'give' ); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" /><br>
				<small class="give-field-description"><?php esc_html_e( 'Leave blank to hide the widget title.', 'give' ); ?></small>
			</p>

			<?php // Widget: Give Form. ?>
			<p>
				<?php
				$selectFieldName = esc_attr( $this->get_field_name( 'id' ) );
				$selectFieldId   = esc_attr( sanitize_key( str_replace( '-', '_', esc_attr( $this->get_field_id( 'id' ) ) ) ) );
				printf(
					'<label for="%1$s">%2$s</label>',
					$selectFieldId,
					esc_html__( 'Donation Form:', 'give' )
				);

				echo Give()->html->forms_dropdown(
					[
						'selected'    => $instance['id'] ?: false,
						'id'          => $selectFieldId,
						'name'        => $selectFieldName,
						'placeholder' => esc_attr__( '- Select -', 'give' ),
						'query_args'  => [
							'post_status' => 'publish',
						],
						'select_atts' => 'style="width: 100%"',
					]
				);
				?>
			</p>

			<fieldset class="js-legacy-form-template-settings give-hidden">
				<legend class="screen-reader-text"><?php _e( 'Options for Legacy form template ', 'give' ); ?></legend>
				<?php // Widget: Display Style. ?>
				<p class="give_forms_display_style_setting_row">
					<label for="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>"><?php esc_html_e( 'Display Style:', 'give' ); ?></label><br>
					<label for="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>-onpage"><input type="radio" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>-onpage" name="<?php echo esc_attr( $this->get_field_name( 'display_style' ) ); ?>" value="onpage" <?php checked( $instance['display_style'], 'onpage' ); ?>> <?php echo esc_html__( 'All Fields', 'give' ); ?></label>
					&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>-reveal"><input type="radio" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>-reveal" name="<?php echo esc_attr( $this->get_field_name( 'display_style' ) ); ?>" value="reveal" <?php checked( $instance['display_style'], 'reveal' ); ?>> <?php echo esc_html__( 'Reveal', 'give' ); ?></label>
					&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>-modal"><input type="radio" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>-modal" name="<?php echo esc_attr( $this->get_field_name( 'display_style' ) ); ?>" value="modal" <?php checked( $instance['display_style'], 'modal' ); ?>> <?php echo esc_html__( 'Modal', 'give' ); ?></label>
					&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>-button"><input type="radio" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>-button" name="<?php echo esc_attr( $this->get_field_name( 'display_style' ) ); ?>" value="button" <?php checked( $instance['display_style'], 'button' ); ?>> <?php echo esc_html__( 'Button', 'give' ); ?></label><br>
					<small class="give-field-description">
						<?php echo esc_html__( 'Select a GiveWP donation form style.', 'give' ); ?>
					</small>
				</p>

				<?php // Widget: Continue Button Title. ?>
				<p class="give_forms_continue_button_title_setting_row">
					<label for="<?php echo esc_attr( $this->get_field_id( 'continue_button_title' ) ); ?>"><?php esc_html_e( 'Button Text:', 'give' ); ?></label>
					<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'continue_button_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'continue_button_title' ) ); ?>" value="<?php echo esc_attr( $instance['continue_button_title'] ); ?>" /><br>
					<small class="give-field-description"><?php esc_html_e( 'The button label for displaying the additional payment fields.', 'give' ); ?></small>
				</p>

				<?php // Widget: Floating Labels. ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'float_labels' ) ); ?>"><?php esc_html_e( 'Floating Labels (optional):', 'give' ); ?></label><br>
					<label for="<?php echo esc_attr( $this->get_field_id( 'float_labels' ) ); ?>-global"><input type="radio" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'float_labels' ) ); ?>-global" name="<?php echo esc_attr( $this->get_field_name( 'float_labels' ) ); ?>" value="global" <?php checked( $instance['float_labels'], 'global' ); ?>> <?php echo esc_html__( 'Global Option', 'give' ); ?></label>
					&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'float_labels' ) ); ?>-enabled"><input type="radio" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'float_labels' ) ); ?>-enabled" name="<?php echo esc_attr( $this->get_field_name( 'float_labels' ) ); ?>" value="enabled" <?php checked( $instance['float_labels'], 'enabled' ); ?>> <?php echo esc_html__( 'Enabled', 'give' ); ?></label>
					&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'float_labels' ) ); ?>-disabled"><input type="radio" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'float_labels' ) ); ?>-disabled" name="<?php echo esc_attr( $this->get_field_name( 'float_labels' ) ); ?>" value="disabled" <?php checked( $instance['float_labels'], 'disabled' ); ?>> <?php echo esc_html__( 'Disabled', 'give' ); ?></label><br>
					<small class="give-field-description">
						<?php
						printf(
							/* translators: %s: Documentation link to http://docs.givewp.com/form-floating-labels */
							__( 'Override the <a href="%s" target="_blank">floating labels</a> setting for this GiveWP form.', 'give' ),
							esc_url( 'http://docs.givewp.com/form-floating-labels' )
						);
						?>
					</small>
				</p>

				<?php // Widget: Display Content. ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>"><?php esc_html_e( 'Display Content (optional):', 'give' ); ?></label><br>
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>-none"><input type="radio" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>-none" name="<?php echo esc_attr( $this->get_field_name( 'show_content' ) ); ?>" value="none" <?php checked( $instance['show_content'], 'none' ); ?>> <?php echo esc_html__( 'None', 'give' ); ?></label>
					&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>-above"><input type="radio" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>-above" name="<?php echo esc_attr( $this->get_field_name( 'show_content' ) ); ?>" value="above" <?php checked( $instance['show_content'], 'above' ); ?>> <?php echo esc_html__( 'Above', 'give' ); ?></label>
					&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>-below"><input type="radio" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>-below" name="<?php echo esc_attr( $this->get_field_name( 'show_content' ) ); ?>" value="below" <?php checked( $instance['show_content'], 'below' ); ?>> <?php echo esc_html__( 'Below', 'give' ); ?></label><br>
					<small class="give-field-description"><?php esc_html_e( 'Override the display content setting for this GiveWP form.', 'give' ); ?></small>
				</p>
			</fieldset>

			<fieldset class="js-new-form-template-settings give-hidden">
				<legend class="screen-reader-text"><?php _e( 'Options for Legacy form template ', 'give' ); ?></legend>

				<?php
				// Widget: Display Style.

				$displayStyleFieldId = esc_attr( $this->get_field_id( 'display_style' ) ) . uniqid();

				// Set default value for form template other then legacy.
				$instance['display_style'] = ! in_array( [ 'button', 'onpage' ], $instance['display_style'] ) ? 'button' : $instance['display_style'];
				?>
				<p class="give_forms_display_style_setting_row">
					<label for="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>"><?php esc_html_e( 'Display Style:', 'give' ); ?></label><br>
					<label for="<?php echo $displayStyleFieldId; ?>-button"><input type="radio" class="widefat" id="<?php echo $displayStyleFieldId; ?>-button" name="<?php echo esc_attr( $this->get_field_name( 'display_style' ) ); ?>" value="button" <?php checked( $instance['display_style'], 'button' ); ?>> <?php echo esc_html__( 'Display a button and launch the donation form on click', 'give' ); ?></label><br>
					<label for="<?php echo $displayStyleFieldId; ?>-onpage"><input type="radio" class="widefat" id="<?php echo $displayStyleFieldId; ?>-onpage" name="<?php echo esc_attr( $this->get_field_name( 'display_style' ) ); ?>" value="onpage" <?php checked( $instance['display_style'], 'onpage' ); ?>> <?php echo esc_html__( 'Display the entire donation form in the sidebar', 'give' ); ?></label>
				</p>

				<?php // Widget: Introduction Text. ?>
				<p class="give_forms_introduction_text_setting_row">
					<label for="<?php echo esc_attr( $this->get_field_id( 'introduction_text' ) ); ?>"><?php esc_html_e( 'Widget Text:', 'give' ); ?></label><br>
					<textarea id="<?php echo esc_attr( $this->get_field_id( 'introduction_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'introduction_text' ) ); ?>" class="widefat"><?php echo esc_textarea( $instance['introduction_text'] ); ?></textarea><br>
					<small class="give-field-description"><?php esc_html_e( 'Provide an introduction text to invite the visitor to become a donor. Leave this blank to not display any text.', 'give' ); ?></small>
				</p>

				<?php // Widget: Continue Button Text. ?>
				<p class="give_forms_button_text_setting_row">
					<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e( 'Button Text:', 'give' ); ?></label>
					<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" value="<?php echo esc_attr( $instance['button_text'] ); ?>" /><br>
					<small class="give-field-description"><?php esc_html_e( 'This label will appear on button.', 'give' ); ?></small>
				</p>
			</fieldset>

			<div class="js-loader give-hidden">
				<p><?php _e( 'Loading settings...', 'give' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Register the widget
	 *
	 * @return void
	 */
	public function widget_init() {
		register_widget( $this->self );
	}

	/**
	 * Update the widget
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$this->flush_widget_cache();

		return $new_instance;
	}

	/**
	 * Flush widget cache
	 *
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->self, 'widget' );
	}
}

new Give_Forms_Widget();
