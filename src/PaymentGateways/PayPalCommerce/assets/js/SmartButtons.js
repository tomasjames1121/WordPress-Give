/* globals paypal, Give, FormData */
import DonationForm from './DonationForm';
import PaymentMethod from './PaymentMethod';

/**
 * PayPal Smart Buttons.
 */
class SmartButtons extends PaymentMethod {
	/**
	 * Render smart buttons.
	 *
	 * @since 2.8.0
	 */
	renderPaymentMethodOption() {
		const smartButtonContainer = this.form.querySelector( '#give-paypal-commerce-smart-buttons-wrap div' );

		if ( ! smartButtonContainer ) {
			return;
		}

		const onInitHandler = this.onInitHandler.bind( this );
		const onClickHandler = this.onClickHandler.bind( this );
		const createOrderHandler = this.createOrderHandler.bind( this );
		const onApproveHandler = this.onApproveHandler.bind( this );

		paypal.Buttons( {
			onInit: onInitHandler,
			onClick: onClickHandler,
			createOrder: createOrderHandler,
			onApprove: onApproveHandler,
		} ).render( smartButtonContainer );
	}

	/**
	 * On init event handler for smart buttons.
	 *
	 * @since 2.8.0
	 *
	 * @param {object} data PayPal button data.
	 * @param {object} actions PayPal button actions.
	 */
	onInitHandler( data, actions ) { // eslint-disable-line
		// Keeping this for future reference.
	}

	/**
	 * On click event handler for smart buttons.
	 *
	 * @since 2.8.0
	 *
	 * @param {object} data PayPal button data.
	 * @param {object} actions PayPal button actions.
	 *
	 * @return {Promise<unknown>} Return wther or not open PayPal checkout window.
	 */
	async onClickHandler(data, actions) { // eslint-disable-line
		const formData = new FormData( this.form );

		formData.delete( 'card_name' );
		this.resetCreditCardFields();

		if ( ! Give.form.fn.isDonationFormHtml5Valid( this.form, true ) ) {
			return actions.reject();
		}

		Give.form.fn.removeErrors( this.jQueryForm );
		const result = await Give.form.fn.isDonorFilledValidData( this.form, formData );

		if ( 'success' === result ) {
			return actions.resolve();
		}

		Give.form.fn.addErrors( this.jQueryForm, result );
		return actions.reject();
	}

	/**
	 * Create order event handler for smart buttons.
	 *
	 * @since 2.8.0
	 *
	 * @param {object} data PayPal button data.
	 * @param {object} actions PayPal button actions.
	 *
	 * @return {Promise<unknown>} Return PayPal order id.
	 */
	async createOrderHandler(data, actions) { // eslint-disable-line
		// eslint-disable-next-line
		const response = await fetch(`${this.ajaxurl}?action=give_paypal_commerce_create_order`, {
			method: 'POST',
			body: DonationForm.getFormDataWithoutGiveActionField( this.form ),
		} );
		const responseJson = await response.json();

		return responseJson.data.id;
	}

	/**
	 * On approve event handler for smart buttons.
	 *
	 * @since 2.8.0
	 *
	 * @param {object} data PayPal button data.
	 * @param {object} actions PayPal button actions.
	 *
	 * @return {Promise<unknown>} Return whether or not PayPal payment captured.
	 */
	async onApproveHandler( data, actions ) {
		Give.form.fn.showProcessingState();
		Give.form.fn.disable( this.jQueryForm, true );

		const self = this;

		// eslint-disable-next-line
		const response = await fetch( `${ this.ajaxurl }?action=give_paypal_commerce_approve_order&order=` + data.orderID, {
			method: 'post',
			body: DonationForm.getFormDataWithoutGiveActionField( this.form ),
		} );
		const responseJson = await response.json();

		// Three cases to handle:
		//   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
		//   (2) Other non-recoverable errors -> Show a failure message
		//   (3) Successful transaction -> Show a success / thank you message

		// Your server defines the structure of 'orderData', which may differ
		const errorDetail = Array.isArray( responseJson.data.order.details ) && responseJson.data.order.details[ 0 ];
		const orderData = responseJson.data.order;

		if ( errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED' ) {
			Give.form.fn.hideProcessingState();
			Give.form.fn.disable( self.jQueryForm, false );

			// Recoverable state, see: "Handle Funding Failures"
			// https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
			return actions.restart();
		}

		if ( errorDetail ) {
			let msg = 'Sorry, your transaction could not be processed.';
			if ( errorDetail.description ) {
				msg += '\n\n' + errorDetail.description;
			}
			if ( orderData.debug_id ) {
				msg += ' (' + orderData.debug_id + ')';
			}

			// Show a failure message
			alert(msg); // eslint-disable-line

			Give.form.fn.disable( self.jQueryForm, false );
			Give.form.fn.hideProcessingState();
		}

		await DonationForm.attachOrderIdToForm( self.form, orderData.id );

		// Do not submit  empty or filled Name credit card field with form.
		// If we do that we will get `empty_card_name` error or other.
		// We are removing this field before form submission because this donation processed with smart button.
		self.removeCreditCardFields();
		self.form.submit();
	}

	/**
	 * Reset Card fields.
	 *
	 * @since 2.8.0
	 */
	resetCreditCardFields() {
		this.jQueryForm.find( 'input[name="card_name"]' ).val( '' );
	}

	/**
	 * Remove Card fields.
	 *
	 * @since 2.8.0
	 */
	removeCreditCardFields() {
		this.jQueryForm.find( 'input[name="card_name"]' ).remove();
	}
}

export default SmartButtons;
