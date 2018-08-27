const give = require( './test-utility' );

describe( 'Display option: Button', () => {

	beforeAll( async () => {
		// Visit the Set donation form page.
		await page.goto( `${give.utility.vars.rootUrl}/donations/button-form/` )

	})

	give.utility.fn.verifyDonationTitle( page, 'Button Form' )
	give.utility.fn.verifyCurrencySymbol( page, '$' )
	give.utility.fn.verifyCurrency( page, '10.00' )
	give.utility.fn.verifyDonationLevels( page )
	give.utility.fn.verifyPaymentMethods( page )
	give.utility.fn.verifyFormContent( page )
	give.utility.fn.verifyPersonalInfoFields( page )
})
