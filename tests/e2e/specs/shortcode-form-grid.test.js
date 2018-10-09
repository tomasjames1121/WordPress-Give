/**
 * This test performs EXISTENCE and INTERACTION tests for the shortcode [give_form_grid]
 *
 * For EXISTENCE tests, it tests for
 * - Grid item title
 * - Grid item form content
 *
 * For INTERACTION tests, it tests for
 * - hover to test the hover animation
 * - click on the grid-item to open the popup
 * - clicks the close button to close the popup
 */

const give = require( './test-utility' );

describe( 'Shortcode Form Grid', () => {

	// Visit the /form-grid page.
	beforeAll( async () => await page.goto( `${give.utility.vars.rootUrl}/form-grid/` ) )

	give.utility.fn.verifyExistence( page, [
		/**
		 * Form title for grid items
		 */
		{
			desc: 'verify grid item 1 title',
			selector: '.give-grid__item:nth-child(1) .give-card__title',
			innerText: 'Button Form',
		},

		/**
		 * Form content for grid items
		 */
		{
			desc: 'verify grid item 1 form content',
			selector: '.give-grid__item:nth-child(1) .give-card__text',
			innerText: 'Form Content of the Button Form.',
		},
	])

	/**
	 * Test hover animations.
	 */
	give.utility.fn.verifyInteraction( page, [
		{
			desc: 'verify hover grid item 1',
			selector: '.give-grid__item:nth-child(1) .give-card',
			event: 'hover',
		},
	])

	/**
	 * Clicking the grid item to open the popup.
	 */
	give.utility.fn.verifyInteraction( page, [
		{
			desc: 'verify click grid item 1',
			selector: '.give-grid__item:nth-child(1) .give-card',
			event: 'click',
		},

		{
			desc: 'verify close popup',
			selector: '.mfp-close',
			event: 'click',
		}
	])
})