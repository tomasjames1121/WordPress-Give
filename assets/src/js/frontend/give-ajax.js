/*!
 * Give AJAX JS
 *
 * @description: The Give AJAX scripts
 * @package:     Give
 * @subpackage:  Assets/JS
 * @copyright:   Copyright (c) 2016, GiveWP
 * @license:     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/* globals jQuery, Give */
jQuery( document ).ready( function( $ ) {
	// console.log( $.fn.iFrameResize);
	if ( $.fn.iFrameResize ) {
		// Parent page.
		$( 'iframe[name="give-embed-form"]' ).iFrameResize(
			{
				log: true,
				sizeWidth: true,
				heightCalculationMethod: 'documentElementOffset',
				widthCalculationMethod: 'documentElementOffset',
				onMessage: function( messageData ) {
					switch ( messageData.message ) {
						case 'give_embed_form_loaded':
							messageData.iframe.style.visibility = 'visible';
					}
				},
				onInit: function( iframe ) {
					iframe.iFrameResizer.sendMessage( {
						currentPage: window.location.href,
					} );
				},
			}
		);
	}

	// Reset nonce only if form exists.
	if ( Give.form.fn.isFormExist() ) {
		//Hide loading elements
		$( '.give-loading-text' ).hide();

		// Update and invalidate cached nonce.
		$( '.give-form' ).each( function( index, $form ) {
			let nonceInfo, nonceTime, currentTime, timeDiff;

			$form = jQuery( $form );
			nonceInfo = Give.form.fn.getNonceInfo( $form );

			if ( ! nonceInfo.el.attr( 'data-donor-session' ) ) {
				// Backward compatibility.
				// @see https://github.com/impress-org/give/issues/3820
				Give.form.fn.resetAllNonce( $form );
			} else if (
				(
					nonceInfo.createdInDonorSession ||
					Give.donor.fn.hasSession( $form )
				) &&
				! Give.donor.fn.isLoggedIn()
			) {
				// Reset nonce if nonce cached when donor was in session or logged in.
				Give.form.fn.resetAllNonce( $form );
			}

			nonceTime = ( parseInt( nonceInfo.el.data( 'time' ) ) + parseInt( nonceInfo.el.data( 'nonce-life' ) ) ) * 1000,
			currentTime = Date.now();

			// We need time in ms.
			timeDiff = nonceTime - currentTime;

			timeDiff = 0 > timeDiff ? timeDiff : ( timeDiff + 100 );

			// Update nonce in background.
			window.setTimeout( function() {
				Give.form.fn.resetAllNonce( $form );
			}, timeDiff );
		} );
	}

	giveMoveFieldsUnderPaymentGateway( true );

	// Show the login form in the checkout when the user clicks the "Login" link
	$( document ).on( 'click', '.give-checkout-login', function( e ) {
		const $this = $( this );
		const this_form = $( this ).parents( 'form' );
		const loading_animation = $this.parents( 'div.give-login-account-wrap' ).find( '.give-loading-text' );
		const data = {
			action: $this.data( 'action' ),
			form_id: $( this_form ).find( '[name="give-form-id"]' ).val(),
		};

		// Show the ajax loader
		loading_animation.show();

		$.post( Give.fn.getGlobalVar( 'ajaxurl' ), data, function( checkout_response ) {
			const oldPosition = $( this_form ).find( '[id^=give-checkout-login-register]' );

			//Show fields
			if ( oldPosition.length && parseInt( oldPosition.html().trim().length ) ) {
				$( this_form ).find( '[id^=give-checkout-login-register]' ).html( checkout_response );
			} else {
				// Insert html on correct position for elegent form style (form in embed).
				$( this_form ).find( '[id^="give_checkout_user_info"]' ).html( checkout_response );
			}

			$( this_form ).find( '.give-submit-button-wrap' ).hide();
		} ).done( function() {
			// Hide the ajax loader
			loading_animation.hide();
			// Trigger float-labels
			give_fl_trigger();
		} );

		return false;
	} );

	// Register/Login Cancel
	$( document ).on( 'click', '.give-checkout-register-cancel', function( e ) {
		e.preventDefault();
		// User cancelled login.
		const $this = $( this );
		const this_form = $( this ).parents( 'form' );
		const data = {
			action: $this.data( 'action' ),
			form_id: $( this_form ).find( '[name="give-form-id"]' ).val(),
		};
		// AJAX get the payment fields.
		$.post( Give.fn.getGlobalVar( 'ajaxurl' ), data, function( checkout_response ) {
			$( this_form ).find( '[id^=give-checkout-login-register]' ).replaceWith( $.parseJSON( checkout_response.fields ) );
			$( this_form ).find( '.give-submit-button-wrap' ).show();
		} ).done( function() {
			// Trigger float-labels
			give_fl_trigger();
		} );
	} );

	// Process the login form via ajax when the user clicks "login"
	$( document ).on( 'click', '[id^=give-login-fields] input[type=submit]', function( e ) {
		e.preventDefault();

		const complete_purchase_val = $( this ).val();
		const this_form = $( this ).parents( 'form' );

		$( this ).val( Give.fn.getGlobalVar( 'purchase_loading' ) );

		this_form.find( '[id^=give-login-fields] .give-loading-animation' ).fadeIn();

		const data = {
			action: 'give_process_donation_login',
			give_ajax: 1,
			give_user_login: this_form.find( '[name=give_user_login]' ).val(),
			give_user_pass: this_form.find( '[name=give_user_pass]' ).val(),
			give_form_id: this_form.find( '[name=give-form-id]' ).val(),
		};

		$.post( Give.fn.getGlobalVar( 'ajaxurl' ), data, function( response ) {
			//user is logged in
			if ( $.trim( typeof ( response.success ) ) != undefined && response.success == true && typeof ( response.data ) !== undefined ) {
				//remove errors
				this_form.find( '.give_errors' ).remove();

				// Login successfully message.
				this_form.find( '#give-payment-mode-select' ).after( response.data );
				this_form.find( '.give_notices.give_errors' ).delay( 5000 ).slideUp();

				// This function will run only for embed donation form.
				if ( this_form.parent().hasClass( 'give-embed-form' ) ) {
					// @todo: add a way to load personal information fields.
				}

				Give.form.fn.resetAllNonce( this_form ).then(
					response => {
						//reload the selected gateway so it contains their logged in information
						give_load_gateway( this_form, this_form.find( '.give-gateway-option-selected input' ).val() );
					}
				);
			} else {
				//Login failed, show errors
				this_form.find( '[id^=give-login-fields] input[type=submit]' ).val( complete_purchase_val );
				this_form.find( '.give-loading-animation' ).fadeOut();
				this_form.find( '.give_errors' ).remove();
				this_form.find( '[id^=give-user-login-submit]' ).before( response.data );
			}
		} );
	} );

	//Switch the gateway on gateway selection field change
	$( 'select#give-gateway, input.give-gateway' ).on( 'change', function( e ) {
		e.preventDefault();

		//Which payment gateway to load?
		const payment_mode = $( this ).val();

		//Problema? Bounce
		if ( payment_mode == '0' ) {
			console.log( 'There was a problem loading the selected gateway' );
			return false;
		}

		give_load_gateway( $( this ).parents( 'form' ), payment_mode );

		return false;
	} );

	/**
	 * Donation history non login user want to see email list after making a donation
	 *
	 * @since 1.8.17
	 */
	$( 'body' ).on( 'click', '#give-confirm-email-btn', function( e ) {
		const $this = $( this );
		const data = {
			action: 'give_confirm_email_for_donations_access',
			email: $this.data( 'email' ),
			nonce: Give.fn.getGlobalVar( 'ajax_vars' ).ajaxNonce,
		};

		$this.text( Give.fn.getGlobalVar( 'loading' ) );
		$this.attr( 'disabled', 'disabled' );

		$.post( Give.fn.getGlobalVar( 'ajaxurl' ), data, function( response ) {
			response = JSON.parse( response );
			if ( 'error' === response.status ) {
				$this.closest( '#give_user_history tfoot' ).hide();
				$this.closest( '.give_user_history_main' ).find( '.give_user_history_notice' ).html( response.message );
			} else if ( 'success' === response.status ) {
				$this.closest( '.give_user_history_main' ).find( '.give_user_history_notice' ).html( response.message );
				$this.hide();
				$this.closest( '.give-security-button-wrap' ).find( 'span' ).show();
			}
		} );

		return false;
	} );

	/**
	 * Donation Form AJAX Submission
	 *
	 * @description: Process the donation submit
	 */
	$( 'body' ).on( 'click touchend', 'form.give-form input[name="give-purchase"].give-submit', function( e ) {
		//this form object
		const $this = $( this );
		const this_form = $this.parents( 'form.give-form' );

		//loading animation
		const loading_animation = this_form.find( 'input[type="submit"].give-submit + .give-loading-animation' );
		loading_animation.fadeIn();

		//this form selector
		const give_purchase_form = this_form.get( 0 );

		//HTML5 required check validity
		if ( typeof give_purchase_form.checkValidity === 'function' && give_purchase_form.checkValidity() === false ) {
			//Don't leave any hanging loading animations
			loading_animation.fadeOut();

			//Check for Safari (doesn't support HTML5 required)
			if ( ( navigator.userAgent.indexOf( 'Safari' ) != -1 && navigator.userAgent.indexOf( 'Chrome' ) == -1 ) === false ) {
				//Not safari: Support HTML5 "required" so skip the rest of this function
				return;
			}
		}

		//prevent form from submitting normally
		e.preventDefault();

		//Submit btn text
		const complete_purchase_val = $( this ).val();

		//Update submit button text
		$( this ).val( Give.fn.getGlobalVar( 'purchase_loading' ) );

		// Disable the form donation button.
		Give.form.fn.disable( this_form, true );

		//Submit form via AJAX
		$.post( Give.fn.getGlobalVar( 'ajaxurl' ), this_form.serialize() + '&action=give_process_donation&give_ajax=true', function( data ) {
			if ( $.trim( data ) == 'success' ) {
				//Remove any errors
				this_form.find( '.give_errors' ).remove();
				//Submit form for normal processing
				$( give_purchase_form ).submit();

				this_form.trigger( 'give_form_validation_passed' );
			} else {
				//There was an error / remove old errors and prepend new ones
				$this.val( complete_purchase_val );
				loading_animation.fadeOut();
				this_form.find( '.give_errors' ).remove();
				this_form.find( '#give_purchase_submit input[type="submit"].give-submit' ).before( data );

				// Enable the form donation button.
				Give.form.fn.disable( this_form, false );
			}
		} );
	} );

	/**
	 * Render receipt by Ajax
	 *
	 * @since 2.2.0
	 */
	const receiptContainer = document.getElementById( 'give-receipt' );

	if ( receiptContainer ) {
		const data = {
			action: 'get_receipt',
			shortcode_atts: receiptContainer.getAttribute( 'data-shortcode' ),
			donation_id: receiptContainer.getAttribute( 'data-donation-key' ),
			receipt_type: receiptContainer.getAttribute( 'data-receipt-type' ),
		};

		const cookie_name = Give.fn.getGlobalVar( 'session_cookie_name' );

		// Set cookie.
		data[ cookie_name ] = Give.fn.__getCookie( Give.fn.getGlobalVar( 'session_cookie_name' ) );

		$.ajax( {
			url: Give.fn.getGlobalVar( 'ajaxurl' ),
			method: 'GET',
			data: data,
			success: function( response ) {
				receiptContainer.innerHTML = response;
			},
		} );
	}
} );

/**
 * Load the Payment Gateways
 *
 * @description: AJAX load appropriate gateway fields
 * @param form_object Obj The specific form to load a gateway for
 * @param payment_mode
 */
function give_load_gateway( form_object, payment_mode ) {
	const loading_element = jQuery( form_object ).find( '#give-payment-mode-select .give-loading-text' );
	const give_total = jQuery( form_object ).find( '#give-amount' ).val();
	const give_form_id = jQuery( form_object ).find( 'input[name="give-form-id"]' ).val();
	const give_form_id_prefix = jQuery( form_object ).find( 'input[name="give-form-id-prefix"]' ).val();

	// Show the ajax loader
	loading_element.fadeIn();

	const form_data = jQuery( form_object ).data();

	if ( form_data[ 'blockUI.isBlocked' ] != 1 ) {
		jQuery( form_object ).find( '#give_purchase_form_wrap' ).block( {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6,
			},
		} );
	}

	//Post via AJAX to Give
	new Promise( function( res ) {
		giveMoveFieldsUnderPaymentGateway( false );

		jQuery.post( Give.fn.getGlobalVar( 'ajaxurl' ) + '?payment-mode=' + payment_mode, {
			action: 'give_load_gateway',
			give_total: give_total,
			give_form_id: give_form_id,
			give_form_id_prefix: give_form_id_prefix,
			give_payment_mode: payment_mode,
			nonce: Give.form.fn.getNonce( form_object ),
		},
		function( response ) {
			//Success: let's output the gateway fields in the appropriate form space
			jQuery( form_object ).find( '#give_purchase_form_wrap' ).html( response );
			jQuery( '.give-no-js' ).hide();
			jQuery( form_object ).find( '#give-payment-mode-select .give-loading-text' ).fadeOut();

			// trigger an event on success for hooks
			jQuery( document ).trigger( 'give_gateway_loaded', [ response, jQuery( form_object ).attr( 'id' ) ] );

			// Unblock form.
			jQuery( form_object ).unblock();

			return res();
		}
		);
	} ).then( function() {
		giveMoveFieldsUnderPaymentGateway( true );
	} );
}

/**
 * Move form field under payment gateway
 *
 * @param {boolean} $refresh Flag to remove or add form fields to selected payment gateway.
 */
function giveMoveFieldsUnderPaymentGateway( $refresh = false ) {
	// This function will run only for embed donation form.
	if ( 1 !== parseInt( jQuery( 'div.give-embed-form' ).length ) ) {
		return;
	}

	if ( ! $refresh ) {
		const element = jQuery( 'li.give_purchase_form_wrap-clone' );
		element.slideUp( 'slow', function() {
			element.remove();
		} );

		return;
	}

	new Promise( function( res ) {
		const fields = jQuery( '#give_purchase_form_wrap > *' ).not( '.give-donation-submit' );
		let showFields = false;

		jQuery( '.give-gateway-option-selected' ).after( '<li class="give_purchase_form_wrap-clone" style="display: none"></li>' );

		jQuery.each( fields, function( index, $item ) {
			$item = jQuery( $item );
			jQuery( '.give_purchase_form_wrap-clone' ).append( $item.clone() );

			showFields = ! showFields ? !! $item.html().trim() : showFields;

			$item.remove();
		} );

		if ( ! showFields ) {
			jQuery( '.give_purchase_form_wrap-clone' ).remove();
		}

		return res( showFields );
	} ).then( function( showFields ) {
		// eslint-disable-next-line no-unused-expressions
		showFields && jQuery( '.give_purchase_form_wrap-clone' ).slideDown( 'slow' );
	} );
}
