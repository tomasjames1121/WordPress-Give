<?php

namespace Give\PaymentGateways\PayPalStandard\Actions;

use Give\PaymentGateways\DataTransferObjects\GatewayPaymentData;

/**
 * This class create PayPal Standard payment gateway one time payment url on basis of donor donation query.
 *
 * @unlreased
 */
class BuildPayPalStandardPaymentURL
{
    public function __invoke(GatewayPaymentData $paymentData, $redirectUrl)
    {
        // Only send to PayPal if the pending payment is created successfully.
        $payPalIpnListenerUrl = add_query_arg('give-listener', 'IPN', home_url('index.php'));
        $paypalPaymentRedirectUrl = trailingslashit(give_get_paypal_redirect()) . '?';
        $itemName = $paymentData->getDonationTitle();
        $payPalPartnerCode = 'givewp_SP';

        // Setup PayPal API params.
        $paypalPaymentArguments = [
            // PayPal account information
            'business' => give_get_option('paypal_email', false),

            // Donor info
            'first_name' => $paymentData->donorInfo->firstName,
            'last_name' => $paymentData->donorInfo->lastName,
            'email' => $paymentData->donorInfo->email,

            // Donor address
            'address1' => $paymentData->donorInfo->address->line1,
            'address2' => $paymentData->donorInfo->address->line2,
            'city' => $paymentData->donorInfo->address->city,
            'state' => $paymentData->donorInfo->address->state,
            'zip' => $paymentData->donorInfo->address->postalCode,
            'country' => $paymentData->donorInfo->address->country,

            // Donation information.
            'invoice' => $paymentData->purchaseKey,
            'amount' => $paymentData->amount,
            'item_name' => stripslashes($itemName),
            'currency_code' => give_get_currency($paymentData->donationId),

            // Urls
            'return' => $redirectUrl,
            'cancel_return' => give_get_failed_transaction_uri(),
            'notify_url' => $payPalIpnListenerUrl,

            'no_shipping' => '1',
            'shipping' => '0',
            'no_note' => '1',
            'charset' => get_bloginfo('charset'),
            'custom' => $paymentData->donationId,
            'rm' => '2',
            'page_style' => give_get_paypal_page_style(),
            'cbt' => get_bloginfo('name'),
            'bn' => $payPalPartnerCode,
        ];


        // Donations or regular transactions?
        $paypalPaymentArguments['cmd'] = give_get_paypal_button_type();

        /**
         * Filter the PayPal Standard redirect args.
         *
         * @since 1.8
         *
         * @param array $payment_data Payment Data.
         * @param int $donationId Donation ID.
         */
        $paypalPaymentArguments = apply_filters(
            'give_paypal_redirect_args',
            $paypalPaymentArguments,
            $paymentData->donationId // TODO: discuss backward compatibility about change in filter argument
        );

        $paypalPaymentRedirectUrl .= http_build_query($paypalPaymentArguments);

        // Fix for some sites that encode the entities.
        return str_replace('&amp;', '&', $paypalPaymentRedirectUrl);
    }
}
