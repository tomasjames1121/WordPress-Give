/**
 * Give - Stripe Core Admin JS
 *
 * @since 2.5.0
 */
window.addEventListener( 'DOMContentLoaded', function() {
	const modalCheckout = document.querySelectorAll( '.stripe-checkout-field' );
	const ccFormatSettings = document.querySelector( '.stripe-cc-field-format-settings' );
	const modalOption = document.getElementById( 'stripe_checkout_enabled' );
	const stripeFonts = document.querySelectorAll( 'input[name="stripe_fonts"]' );
	const stripeStylesBase = document.getElementById( 'stripe_styles_base' );
	const stripeStylesEmpty = document.getElementById( 'stripe_styles_empty' );
	const stripeStylesInvalid = document.getElementById( 'stripe_styles_invalid' );
	const stripeStylesComplete = document.getElementById( 'stripe_styles_complete' );
	const stripeCustomFonts = document.getElementById( 'stripe_custom_fonts' );
	const donationStatus = document.getElementById( 'give-payment-status' );

	jsonFormattedTextarea( stripeStylesBase );
	jsonFormattedTextarea( stripeStylesEmpty );
	jsonFormattedTextarea( stripeStylesInvalid );
	jsonFormattedTextarea( stripeStylesComplete );
	jsonFormattedTextarea( stripeCustomFonts );

	if ( null !== donationStatus ) {
		donationStatus.addEventListener( 'change', ( event ) => {
			const stripeCheckbox = document.getElementById( 'give-stripe-opt-refund' );

			if ( null === stripeCheckbox ) {
				return;
			}

			stripeCheckbox.checked = false;

			// If donation status is complete, then show refund checkbox
			if ( 'refunded' === event.target.value ) {
				document.getElementById( 'give-stripe-opt-refund-wrap' ).style.display = 'block';
			} else {
				document.getElementById( 'give-stripe-opt-refund-wrap' ).style.display = 'none';
			}
		} );
	}

	// Toggle based on selection of stripe fonts admin settings.
	if ( null !== stripeFonts ) {
		stripeFonts.forEach( ( element ) => {
			const stripeGoogleFontsWrap = document.querySelector( '.give-stripe-google-fonts-wrap' );
			const stripeCustomFontsWrap = document.querySelector( '.give-stripe-custom-fonts-wrap' );

			element.addEventListener( 'change', ( event ) => {
				if ( 'custom_fonts' === event.target.value ) {
					stripeGoogleFontsWrap.style.display = 'none';
					stripeCustomFontsWrap.style.display = 'table-row';
				} else if ( 'google_fonts' === event.target.value ) {
					stripeGoogleFontsWrap.style.display = 'table-row';
					stripeCustomFontsWrap.style.display = 'none';
				}
			} );
		} );
	}

	// Bail out, if modal or enable apple/google pay option is null.
	if ( null === modalOption ) {
		return;
	}

	modalCheckout.forEach( function( element, index ) {
		if ( modalOption.checked ) {
			modalCheckout[ index ].style.display = 'table-row';
			ccFormatSettings.style.display = 'none';
		}
	} );

	modalOption.addEventListener( 'click', function() {
		modalCheckout.forEach( function( element, index ) {
			if ( modalOption.checked ) {
				modalCheckout[ index ].style.display = 'table-row';
				ccFormatSettings.style.display = 'none';
			} else {
				modalCheckout[ index ].style.display = 'none';
				ccFormatSettings.style.display = 'table-row';
			}
		} );
	} );

	const dismissConnectBanner = document.querySelector( '.give-stripe-connect-temp-dismiss' );
	if ( null !== dismissConnectBanner ) {
		dismissConnectBanner.on( 'click', function() {
			const dismissBanner = new XMLHttpRequest();
			const formData = new FormData();

			formData.append( 'give_action', 'stripe_connect_dismiss' );

			dismissBanner.onreadystatechange = function() {
				// Do nothing.
			};
			dismissBanner.open( 'POST', give_global_vars.ajaxurl, false );
			dismissBanner.send( formData );
		} );
	}
} );

/**
 * This function will help to beautify JSON data.
 *
 * @param element
 * @param value
 *
 * @since 2.5.0
 */
function prettyJson( element, value ) {
	let jsonData = '';
	const saveButton = document.querySelector( '.give-save-button' );

	try {
		jsonData = JSON.parse( value );
		element.value = JSON.stringify( jsonData, undefined, 2 );
		element.style.border = 'none';
		saveButton.removeAttribute( 'disabled' );
	} catch ( e ) {
		element.style.border = '1px solid red';
		saveButton.setAttribute( 'disabled', 'disabled' );
	}
}

/**
 * This will trigger textarea to validate json formatted input.
 *
 * @param element
 *
 * @since 2.5.0
 */
function jsonFormattedTextarea( element ) {
	if ( null !== element ) {
		prettyJson( element, element.value );

		element.addEventListener( 'blur', ( event ) => {
			prettyJson( element, event.target.value );
		} );
	}
}

/**
 * Webhook Sync related js.
 */
jQuery.noConflict();
( function( $ ) {
	// On DOM Ready.
	$( function() {
		give_stripe_check_webhook_status();

		$( '.give-stripe-sync-webhooks' ).on( 'click', function( e ) {
			// Prevent form submission.
			e.preventDefault();

			// Show loading icon.
			$( '.give-stripe-syncing-status' ).show();

			give_stripe_check_webhook_status();
		} );
	} );

	/**
	 * Check Stripe Webhooks status.
	 *
	 * AJAX to see if webhooks are already setup
	 *
	 * @since 2.5.0
	 */
	function give_stripe_check_webhook_status() {
		const data = {
			action: 'give_stripe_check_webhook_status',
		};

		$.post( ajaxurl, data, function( response ) {
			$( '.give-stripe-syncing-status' ).hide();
			$( '.give-stripe-webhook-sync-wrap' ).show();

			if ( true === response.data.live_webhooks_setup ) {
				$( '.give-stripe-live-webhook-not-connected' ).hide();
				$( '.give-stripe-live-webhook-connected' ).show();
			} else if ( true === response.data.sandbox_webhooks_setup ) {
				$( '.give-stripe-sandbox-webhook-not-connected' ).hide();
				$( '.give-stripe-sandbox-webhook-connected' ).show();
			} else {
				$( '.give-stripe-webhook-general-error' ).show();
			}

			$( '.give-stripe-sync-webhooks' ).show();
		} );
	}
}( jQuery ) );

