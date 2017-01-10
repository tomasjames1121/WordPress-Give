<?php

/**
 * Email Notification Setting Fields
 *
 * @package     Give
 * @subpackage  Classes/Emails
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.9
 */
class Give_Email_Setting_Field {
	/**
	 * Get setting field.
	 *
	 * @since  1.9
	 * @access public
	 *
	 * @param Give_Email_Notification $email
	 *
	 * @return array|int
	 */
	public static function get_setting_fields( Give_Email_Notification $email ) {
		$setting_fields = self::get_default_setting_fields( $email );

		// Recipient field.
		if ( $email->has_recipient_field() ) {
			$setting_fields[] = self::get_recipient_setting_field( $email );
		}

		// Preview field.
		if ( $email->has_preview() ) {
			$setting_fields[] = self::get_preview_setting_field( $email );
		}

		// Add extra setting field.
		if ( $extra_setting_field = $email->get_extra_setting_fields() ) {
			$setting_fields = array_merge( $setting_fields, $extra_setting_field );
		}

		$setting_fields = self::add_section_end( $setting_fields, $email );

		return $setting_fields;
	}


	/**
	 * Check if email notification setting has section end or not.
	 *
	 * @since  1.9
	 * @access private
	 *
	 * @param $setting
	 *
	 * @return bool
	 */
	public static function has_section_end( $setting ) {
		$last_field      = end( $setting );
		$has_section_end = false;

		if ( 'sectionend' === $last_field['type'] ) {
			$has_section_end = true;
		}

		return $has_section_end;
	}

	/**
	 * Check if email notification setting has section end or not.
	 *
	 * @since  1.9
	 * @access private
	 *
	 * @param array                   $setting
	 * @param Give_Email_Notification $email
	 *
	 * @return array
	 */
	public static function add_section_end( $setting, Give_Email_Notification $email ) {
		if ( ! self::has_section_end( $setting ) ) {
			// Add section end field.
			$setting[] = array(
				'id'   => "give_title_email_settings_{$email->get_id()}",
				'type' => 'sectionend',
			);
		}

		return $setting;
	}

	/**
	 * Get default setting field.
	 *
	 * @since  1.9
	 * @access static
	 *
	 * @param Give_Email_Notification $email
	 *
	 * @return array
	 */
	public static function get_default_setting_fields( Give_Email_Notification $email ) {
		return array(
			array(
				'id'    => "give_title_email_settings_{$email->get_id()}",
				'type'  => 'title',
				'title' => $email->get_label(),
			),
			array(
				'name'    => esc_html__( 'Notification', 'give' ),
				'desc'    => esc_html__( 'Choose option if you want to send email notification or not.', 'give' ),
				'id'      => "{$email->get_id()}_notification",
				'type'    => 'radio_inline',
				'default' => $email->get_notification_status(),
				'options' => array(
					'enabled'  => __( 'Enabled', 'give' ),
					'disabled' => __( 'Disabled', 'give' ),
				),
			),
			array(
				'id'      => "{$email->get_id()}_email_subject",
				'name'    => esc_html__( 'Email Subject', 'give' ),
				'desc'    => esc_html__( 'Enter the subject line for email.', 'give' ),
				'default' => $email->get_default_email_subject(),
				'type'    => 'text',
			),
			array(
				'id'      => "{$email->get_id()}_email_message",
				'name'    => esc_html__( 'Email message', 'give' ),
				'desc'    => $email->get_email_message_field_description(),
				'type'    => 'wysiwyg',
				'default' => $email->get_default_email_message(),
			),
		);
	}

	/**
	 * Get recipient setting field.
	 *
	 * @since  1.9
	 * @access static
	 *
	 * @param Give_Email_Notification $email
	 *
	 * @return array
	 */
	public static function get_recipient_setting_field( Give_Email_Notification $email ) {
		return array(
			'id'               => "{$email->get_id()}_recipient",
			'name'             => esc_html__( 'Email Recipients', 'give' ),
			'desc'             => __( 'Enter the email address(es) that should receive a notification anytime a donation is made.', 'give' ),
			'type'             => 'email',
			'default'          => get_bloginfo( 'admin_email' ),
			'repeat'           => true,
			'repeat_btn_title' => esc_html__( 'Add Recipient', 'give' ),
		);
	}

	/**
	 * Get preview setting field.
	 *
	 * @since  1.9
	 * @access static
	 *
	 * @param Give_Email_Notification $email
	 *
	 * @return array
	 */
	public static function get_preview_setting_field( Give_Email_Notification $email ) {
		return array(
			'name' => esc_html__( 'Preview Email', 'give' ),
			'desc' => esc_html__( 'Click the buttons to preview emails.', 'give' ),
			'id'   => "{$email->get_id()}_preview_buttons",
			'type' => 'email_preview_buttons',
		);
	}
}