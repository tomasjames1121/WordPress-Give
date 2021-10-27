jQuery(document).on('give:postInit', function() {

    /**
     * Amount
     */
    GiveDonationSummary.observe( '[name="give-amount"]', function( targetNode, $form ) {
        $form.find( '[data-tag="amount"]' ).html(
            GiveDonationSummary.format_amount( targetNode.value, $form )
        )
    })

    /**
     * Frequency (and Recurring)
     */
    GiveDonationSummary.observe( '[name="give-recurring-period"]', function( targetNode, $form ) {
        const $helpText = $form.find( '.js-give-donation-summary-frequency-help-text' )
        if( targetNode.checked ) {
            $helpText.hide()
            $form.find( '[data-tag="frequency"]' ).hide()
            $form.find( '[data-tag="recurring"]' ).show().html( targetNode.dataset['periodLabel'] )
        } else {
            $helpText.show()
            $form.find( '[data-tag="frequency"]' ).show()
            $form.find( '[data-tag="recurring"]' ).hide()
        }
    })

    /**
     * Fees
     */
    GiveDonationSummary.observe('.give_fee_mode_checkbox', function (targetNode, $form) {
        $form.find('.fee-break-down-message').hide()
        $form.find('.js-give-donation-summary-fees').toggle(targetNode.checked)
        $form.find('[data-tag="fees"]').html(
            GiveDonationSummary.format_amount(document.querySelector('[name="give-fee-amount"]').value, $form)
        )
    })

    /**
     * Total
     */
    GiveDonationSummary.observe( '.give-final-total-amount', function( targetNode, $form ) {
        $form.find( '[data-tag="total"]' ).html(
            GiveDonationSummary.format_amount( targetNode.dataset.total, $form )
        )
    })
})

const GiveDonationSummary = {

    /**
     * Observe an element and respond to changes to that element.
     *
     * @param {string} selectors
     * @param {callable} callback
     */
    observe: function( selectors, callback ) {
        const targetNode = document.querySelector(selectors )

        if( ! targetNode ) return;

        new MutationObserver(function(mutationsList, observer) {
            for(const mutation of mutationsList) { // Use traditional 'for loops' for IE 11
                if (mutation.type === 'attributes') {
                    /**
                     * @param targetNode The node matching the element as defined by the specific selectors
                     * @param $form The closest `.give-form` node to the targetNode, wrapped in jQuery
                     */
                    callback( targetNode, jQuery( targetNode.closest('.give-form') ) )
                }
            }
        }).observe(targetNode, { attributes: true });
    },

    /**
     * Helper function to get the formatted amount
     *
     * @param {string/number} amount
     * @param {jQuery} $form
     */
    format_amount: function( amount, $form ) {
        return accounting.formatMoney( amount, {
            symbol: Give.form.fn.getInfo( 'currency_symbol', $form ),
            position: Give.form.fn.getInfo( 'currency_position', $form ),
            decimal: Give.form.fn.getInfo( 'decimal_separator', $form ),
            thousand: Give.form.fn.getInfo( 'thousands_separator', $form ),
        } )
    }
}
