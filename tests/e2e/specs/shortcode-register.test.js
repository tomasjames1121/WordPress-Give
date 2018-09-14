/**
 * This test performs EXISTENCE test for the shortcode [give_register]
 *
 * The EXISTENCE test will first test for
 * - All the form labels and fields
 * - verify username after succesfull registration
 *
 * The INTERACTION test
 * - Fill the form with user registration details
 */

const give = require( './test-utility' );

describe( 'Shortcode Registration', () => {

	// Visit the /give-register-shortcode page.
	beforeAll( async () => await page.goto( `${give.utility.vars.rootUrl}/give-register-shortcode/` ) )

	give.utility.fn.verifyExistence( page, [
		{
			desc: 'verify form legend as "Register a New Account"',
			selector: '#give-register-form legend',
			innerText: 'Register a New Account',
		},

		{
			desc: 'verify username label as "Username"',
			selector: 'label[for="give-user-login"]',
			innerText: 'Username',
		},

		{
			desc: 'verify username text input field',
			selector: '#give-user-login',
		},

		{
			desc: 'verify email label as "Email"',
			selector: 'label[for="give-user-email"]',
			innerText: 'Email',
		},

		{
			desc: 'verify email text input field',
			selector: '#give-user-email',
		},

		{
			desc: 'verify password label as "Password"',
			selector: 'label[for="give-user-pass"]',
			innerText: 'Password',
		},

		{
			desc: 'verify confirm password label as "Confirm PW"',
			selector: 'label[for="give-user-pass2"]',
			innerText: 'Confirm PW',
		},

		{
			desc: 'verify confirm password text input field',
			selector: '#give-user-pass',
		},

		{
			desc: 'verify register submit button as "Register"',
			selector: 'input[name="give_register_submit"]',
			value: 'Register',
		},
	])

	it( 'INTERACTION: register through shortcode', async () => {
		await expect( page ).toFillForm( '#give-register-form', {
			give_user_login: 'darryl.phylbin',
			give_user_email: 'darryl.phylbin@gmail.com',
			give_user_pass: 'darryl12345',
			give_user_pass2: 'darryl12345',
		})

		await Promise.all([
			page.click( 'input[name="give_register_submit"]' ),
			page.waitForNavigation()
		])
	})

	it( 'EXISTENCE: verify login success', async () => {
		await expect( page ).toMatchElement( '.display-name', { text: 'darryl' } )
	})

	// Logout of WordPress.
	afterAll( async () => {
		const logoutLink = await page.evaluate( ()  => {
			return document.querySelector( '#wp-admin-bar-logout a' ).href
		})

		await page.goto( logoutLink )
	})
})
