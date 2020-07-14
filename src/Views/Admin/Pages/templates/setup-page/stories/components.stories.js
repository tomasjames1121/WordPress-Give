import styles from './src/styles';
import paypal from './src/paypal.js';
import stripe from './src/stripe.js';
import section from '../section.html';
import footer from '../footer.html';
import dismiss from '../dismiss.html';

import { withA11y } from '@storybook/addon-a11y';

export default {
<<<<<<< HEAD
	title: 'Setup Page/Components',
=======
	title: 'Components',
>>>>>>> Implement Setup Page design.
	decorators: [ withA11y ],
};

const Styles = '<style>' + styles + '</style>';

export const header = () => Styles +
	section
		.replace( /{{\s*title\s*}}/gi, 'Connect a payment gateway to begin accepting donations' )
<<<<<<< HEAD
		.replace( /{{\s*badge\s*}}/gi, '<span class="badge badge-complete">Complete</span>' )
=======
		.replace( /{{\s*badge\s*}}/gi, '<span class="badge">Complete</span>' )
>>>>>>> Implement Setup Page design.
		.replace( /{{\s*contents\s*}}/gi, '' )
		.replace( /{{\s*footer\s*}}/gi, '' );

export const Footer = () => Styles +
	'<section>'		+
		footer.replace( /{{\s*contents\s*}}/gi, 'Want to use a different gateway? GiveWP has support for many others including Authorize.net, Square, Razorpay and more!<a href="#">View all gateways</a>' )		+
	'</section>'
;

export const Dismiss = () => Styles +
	dismiss
		.replace( /{{\s*action\s*}}/gi, '' )
		.replace( /{{\s*nonce\s*}}/gi, '' )
		.replace( /{{\s*label\s*}}/gi, 'Dismiss Setup Screen' );

export const SectionBasic = () => Styles + `
<<<<<<< HEAD
	<section>
=======
	'<section>
>>>>>>> Implement Setup Page design.
		<header>
			<h2>Connect a payment gateway to begin accepting donations</h2>
		</header>
		<main>
			` + paypal() + `
		</main>
	</section>
`;

export const SectionMultiple = () => Styles + `
	<section>
		<main>
			` + paypal() + stripe() + `
		</main>
	</section>
`;

export const SectionWithHeader = () => Styles + `
	<section>
		<header>
			<h2>Connect a payment gateway to begin accepting donations</h2>
		</header>
		<main>
			` + paypal() + `
		</main>
	</section>
`;

export const SectionWithFooter = () => Styles + `
	<section>
		<main>
			` + paypal() + `
		</main>
			` +
			footer.replace( /{{\s*contents\s*}}/gi, 'Want to use a different gateway? GiveWP has support for many others including Authorize.net, Square, Razorpay and more!<a href="#">View all gateways</a>' ) +
			`
	</section>
`;

export const SectionMarkedComplete = () => Styles + `
	<section>
		<header>
			<h2>Connect a payment gateway to begin accepting donations</h2>
<<<<<<< HEAD
		<span class="badge badge-complete">Complete</span>
=======
		<span class="badge">Complete</span>
>>>>>>> Implement Setup Page design.
		</header>
			<main>
			` + paypal() + `
		</main>
	</section>
`;
