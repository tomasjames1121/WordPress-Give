const give = require( './test-utility' );

describe( 'Shortcode Profile Editor', () => {

	beforeAll( async () => {
		await page.goto( `${give.utility.vars.rootUrl}/give-profile-editor-shortcode/` )
	})

	/**
	 * Fill the login form that is generated by Give login shortcode
	 * when trying to access Give Profile Shortcode while being logged out.
	 */
	it( 'INTERACTION: login through shortcode', async () => {
		await expect( page ).toFillForm( '#give-login-form', {
			give_user_login: 'sam.smith@gmail.com',
			give_user_pass: 'sam12345',
		})

		await Promise.all([
			page.click( '#give_login_submit' ),
			page.waitForNavigation()
		])
	})

	/**
	 * Change the password.
	 */ 
	it( 'INTERACTION: verify change password', async () => {
		
		// Visit the /give-profile-editor-shortcode page again.
		await page.goto( `${give.utility.vars.rootUrl}/give-profile-editor-shortcode/` )

		// Update with new password.
		await expect( page ).toFillForm( '#give_profile_editor_form', {
			give_new_user_pass1: 'sam12345',
			give_new_user_pass2: 'sam12345',
		})

		// Submit and wait for navigation.
		await Promise.all([
			page.click( '#give_profile_editor_submit' ),
			page.waitForNavigation()
		])
	})

	// Verify front-end notice to test whether the password change was succesfull.
	it( 'EXISTENCE: verify success password change', async () => {
		await expect( page ).toMatch( 'Your password has been updated.' )
	})

	// Logout of WordPress.
	afterAll( async () => {
		const logoutLink = await page.evaluate( ()  => {
			return document.querySelector( '#wp-admin-bar-logout a' ).href
		})

		page.goto( logoutLink )
	})
})
