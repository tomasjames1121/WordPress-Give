<?php
/**
 * Offsite payment gateway Iframe redirect handler view.
 *
 * @since 2.7.0
 */
use Give\TemplateSkinManager;

$bodyContent = sprintf(
	'<p style="text-align: center">%1$s/p>
		<a style="font-size: 0" id="link" href="%3$s" target="_parent">%2$s</a>
		<script>
			document.getElementById( \'link\' ).click();
		</script>',
	__( 'Processing...', 'give' ),
	__( 'Link', 'give' ),
	esc_js( $location )
);

$tm = new TemplateSkinManager();
$tm->setTitle( __( 'Donation Processing...', 'give' ) )
   ->setBody( $bodyContent )
   ->render();
