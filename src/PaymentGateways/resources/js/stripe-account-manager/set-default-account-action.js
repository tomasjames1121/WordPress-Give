/**
 * Set Default Stripe Account
 *
 * This will be used to set any non-default Stripe account from the list
 * to set that particular Stripe account as default.
 *
 * @since 2.7.0
 */
const { __, sprintf } = wp.i18n;

window.addEventListener( 'DOMContentLoaded', function() {
	const setStripeDefaults = Array.from( document.querySelectorAll( '.give-stripe-account-set-default' ) );

	if ( ! setStripeDefaults.length ) {
		return
	}

	setStripeDefaults.forEach( ( setStripeDefault ) => {
		setStripeDefault.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			const stripeEmailValueContainer = e.target.parentElement.parentElement.querySelector('.give-stripe-account-email .give-stripe-connect-data-field');
			let accountEmail = '';
			const accountName = sprintf(
				'<p><strong>%1$s</strong><br>%2$s</p>',
				__( 'Account Name', 'give' ),
				e.target.parentElement.parentElement.querySelector('.give-stripe-account-name .give-stripe-connect-data-field').textContent
			);

			if( stripeEmailValueContainer ) {
				accountEmail = sprintf(
					'<p><strong>%1$s</strong><br>%2$s</p>',
					__( 'Account Email', 'give' ),
					stripeEmailValueContainer.textContent
				);
			}

			const docLink = sprintf(
				'<a href="%1$s" target="_blank">%2$s</a>',
				'https://givewp.com/documentation/core/payment-gateways/stripe-free/using-multiple-stripe-accounts-on-a-single-givewp-site/',
				__( 'View Documentation >', 'give' )
			)

			new Give.modal.GiveConfirmModal(
				{
					classes:{
						modalWrapper: 'give-modal--warning'
					},
					modalContent: {
						title: sprintf(
							'<span class="give-stripe-icon stripe-logo-with-circle"></span>%s',
							__( 'Confirm New Default', 'give' )
						),
						desc: sprintf(
							__( 'Please confirm you’d like to set the account below as the new Global Default account. All donation forms set to inherit the Global Settings will use this new default account. %1$s<br>%2$s%3$s', 'give' ),
							docLink,
							accountName,
							accountEmail
						),
					},
					successConfirm: function( args ) {
						const xhr = new XMLHttpRequest();
						const formData = new FormData();
						const formId = Give.fn.getGlobalVar('post_id');

						formData.append( 'action', 'give_stripe_set_account_default' );
						formData.append( 'account_slug', e.target.getAttribute( 'data-account' ) );
						formData.append( 'form_id', formId );

						xhr.open( 'POST', ajaxurl );
						xhr.onload = function() {
							const response = JSON.parse( xhr.response );
							if ( xhr.status === 200 && response.success ) {
								window.location.reload();
							}
						};
						xhr.send( formData );
					},
				}
			).render();
		} );
	} );
});
