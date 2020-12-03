<?php
namespace Give\Tracking;

use Give\Onboarding\Setup\PageView;

/**
 * Class OnBoarding
 * @package Give\Tracking
 *
 * This class uses to setup notice nag to website administrator if admin is not opt in for usage tracking and gives admin an option to directly opt-in.
 *
 * @since 2.10.0
 */
class AnonymousUsageTrackingOnBoarding {
	const ANONYMOUS_USAGE_TRACING_NOTICE_ID = 'anonymous-usage-tracking-nag';

	/**
	 * Register notice.
	 *
	 * @sicne 2.10.0
	 */
	public function addNotice() {
		if ( ! current_user_can( 'manage_give_settings' ) ) {
			return;
		}

		$notice = $this->getNotice();

		$isAdminOptedIn = give_is_setting_enabled( give_get_option( AdminSettings::ANONYMOUS_USAGE_TRACKING_OPTION_NAME, 'disabled' ) );
		if ( $isAdminOptedIn || give()->notices->is_notice_dismissed( $notice ) ) {
			return;
		}

		give()->notices->register_notice( $notice );
	}

	/**
	 * Get option name of notice.
	 *
	 * We use this option key to disable notice nag for specific user for a interval.
	 *
	 * @since 2.10.0
	 * @return string
	 */
	public function getNoticeOptionKey() {
		return give()->notices->get_notice_key( self::ANONYMOUS_USAGE_TRACING_NOTICE_ID, 'permanent' );
	}

	/**
	 * Render notice.
	 *
	 * @since 2.10.0
	 */
	public function renderNotice() {
		echo $this->getNotice( true );
	}

	/**
	 * Get notice.
	 *
	 * @since 2.10.0
	 *
	 * @return string
	 */
	public function getNotice( $wrapper = false ) {
		/* @var PageView $pageView */
		$pageView = give()->make( PageView::class );

		$notice = $pageView->render_template(
			'section',
			[
				'contents' => $pageView->render_template(
					'row-item',
					[
						'icon'        => $pageView->image( 'hands-in.svg' ),
						'class'       => ! $wrapper ? 'anonymous-usage-tracking' : '',
						'icon_alt'    => esc_html__( 'Anonymous usage tracking icon', 'give' ),
						'title'       => esc_html__( 'Help us improve yor fundraising experience', 'give' ),
						'description' => sprintf(
							'%1$s<br><br><a href="https://givewp.com" class="learn-more-link">%2$s</a>',
							esc_html__( 'You can contribute to improve GiveWP. the Give Team uses non-sensitive data to improve donation from conversion rates, increase average donation amounts, and streamline the fundraising experience. We never share this information with anyone and we never spam.', 'give' ),
							esc_html__( 'Learn more about how GiveWP respects your privacy while improving the plugin >', 'give' )
						),
						'action'      => sprintf(
							'<a class="button" href="%1$s">%2$s</a><div class="sub-links"><a href="%3$s">%4$s</a><a href="%5$s">%6$s</a></div>',
							add_query_arg( [ 'give_action' => 'opt_in_into_tracking' ] ),
							esc_html__( 'Opt-in', 'give' ),
							add_query_arg( [ 'give_action' => 'hide_opt_in_notice_shortly' ] ),
							esc_html__( 'Not Right Now', 'give' ),
							add_query_arg( [ 'give_action' => 'hide_opt_in_notice_permanently' ] ),
							esc_html__( 'Dismiss Forever', 'give' )
						),
					]
				),
			]
		);

		return $wrapper ? sprintf( '<div class="anonymous-usage-tracking notice">%1$s</div>', $notice ) : $notice;
	}
}
