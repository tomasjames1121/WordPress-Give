<?php

namespace Give\PaymentGateways\PayPalCommerce;

use Exception;
use Give\Framework\PaymentGateways\Commands\GatewayCommand;
use Give\Framework\PaymentGateways\Commands\PaymentComplete;
use Give\Framework\PaymentGateways\Exceptions\PaymentGatewayException;
use Give\Framework\PaymentGateways\PaymentGateway;
use Give\Helpers\Call;
use Give\Helpers\Hooks;
use Give\PaymentGateways\DataTransferObjects\GatewayPaymentData;
use Give\PaymentGateways\PayPalCommerce\Actions\GetPayPalOrderFromRequest;
use Give\PaymentGateways\PayPalCommerce\Models\MerchantDetail;
use Give\PaymentGateways\PayPalCommerce\Webhooks\WebhookChecker;

/**
 * Class PayPalCommerce
 *
 * Boots the PayPalCommerce gateway and provides its basic registration properties
 *
 * @since 2.9.0
 */
class PayPalCommerce extends PaymentGateway
{
    /**
     * @deprecated
     *
     * Use getId or id function to access payment gateway id.
     *
     */
    const GATEWAY_ID = 'paypal-commerce';

    /**
     * @unreleased
     *
     * @param int $formId
     * @param array $args
     *
     * @return string
     */
    public function getLegacyFormFieldMarkup($formId, $args)
    {
        ob_start();
        give(AdvancedCardFields::class)->addCreditCardForm($formId);

        return ob_get_clean();
    }

    /**
     * @unreleased
     *
     * @return string
     */
    public static function id()
    {
        return 'paypal-commerce';
    }

    /**
     * @unreleased
     *
     * @return string
     */
    public function getId()
    {
        return self::id();
    }

    /**
     * @unreleased
     *
     * @return string
     */
    public function getName()
    {
        return esc_html__('PayPal Donations', 'give');
    }

    /**
     * @unreleased
     *
     * @return string
     */
    public function getPaymentMethodLabel()
    {
        return esc_html__('Credit Card', 'give');
    }

    /**
     * @unreleased
     *
     * @param GatewayPaymentData $paymentData
     *
     * @return GatewayCommand
     * @throws PaymentGatewayException
     */
    public function createPayment(GatewayPaymentData $paymentData)
    {
        try {
            $paypalOrder = Call::invoke(GetPayPalOrderFromRequest::class);
            $command = PaymentComplete::make($paypalOrder->payment->id);
            $command->paymentNotes = [
                sprintf(
                    __('Transaction Successful. PayPal Transaction ID: %1$s    PayPal Order ID: %2$s', 'give'),
                    $paypalOrder->payment->id,
                    $paypalOrder->id
                )
            ];

            give('payment_meta')->update_meta(
                $paymentData->donationId,
                '_give_order_id',
                $paypalOrder->id
            );

            return $command;
        } catch (Exception $e) {
            throw new PaymentGatewayException($e->getMessage());
        }
    }

    /**
     * @since 2.16.2 Add setting "Transaction type".
     */
    public function getOptions()
    {
        $settings = [
            [
                'type' => 'title',
                'id' => 'give_gateway_settings_1',
                'table_html' => false,
            ],
            [
                'id' => 'paypal_commerce_introduction',
                'type' => 'paypal_commerce_introduction',
            ],
            [
                'type' => 'sectionend',
                'id' => 'give_gateway_settings_1',
                'table_html' => false,
            ],
            [
                'type' => 'title',
                'id' => 'give_gateway_settings_2',
            ],
            [
                'name' => esc_html__('Account Country', 'give'),
                'id' => 'paypal_commerce_account_country',
                'type' => 'paypal_commerce_account_country',
            ],
            [
                'name' => esc_html__('Connect With Paypal', 'give'),
                'id' => 'paypal_commerce_account_manger',
                'type' => 'paypal_commerce_account_manger',
            ],
            [
                'name' => esc_html__('Transaction Type', 'give'),
                'desc' => esc_html__(
                    'Nonprofits must verify their status to withdraw donations they receive via PayPal. PayPal users that are not verified nonprofits must demonstrate how their donations will be used, once they raise more than $10,000. By default, GiveWP transactions are sent to PayPal as donations. You may change the transaction type using this option if you feel you may not meet PayPal\'s donation requirements.',
                    'give'
                ),
                'id' => 'paypal_commerce_transaction_type',
                'type' => 'radio_inline',
                'options' => [
                    'donation' => esc_html__('Donation', 'give'),
                    'standard' => esc_html__('Standard Transaction', 'give'),
                ],
                'default' => 'donation',
            ],
            [
                'name' => esc_html__('PayPal Donations Gateway Settings Docs Link', 'give'),
                'id' => 'paypal_commerce_gateway_settings_docs_link',
                'url' => esc_url('http://docs.givewp.com/paypal-donations'),
                'title' => esc_html__('PayPal Donations Gateway Settings', 'give'),
                'type' => 'give_docs_link',
            ],
            [
                'type' => 'sectionend',
                'id' => 'give_gateway_settings_2',
            ],
        ];

        if (give(MerchantDetail::class)->accountIsReady) {
            $settings = give_settings_array_insert(
                $settings,
                'paypal_commerce_gateway_settings_docs_link',
                [
                    [
                        'name' => esc_html__('Collect Billing Details', 'give'),
                        'id' => 'paypal_commerce_collect_billing_details',
                        'type' => 'radio_inline',
                        'desc' => esc_html__(
                            'If enabled, required billing address fields are added to PayPal Donations Donation forms. These fields are required to process the transaction when enabled. Billing address details are added to both the donation and donor record in GiveWP.',
                            'give'
                        ),
                        'default' => 'disabled',
                        'options' => [
                            'enabled' => esc_html__('Enabled', 'give'),
                            'disabled' => esc_html__('Disabled', 'give'),
                        ],
                    ],
                ]
            );
        }

        /**
         * filter the settings
         *
         * @since 2.9.6
         */
        return apply_filters('give_get_settings_paypal_commerce', $settings);
    }

    /**
     * Setup hook for payment gateway.
     *
     * @since 2.9.0
     */
    public function boot()
    {
        Hooks::addAction(
            'wp_ajax_give_paypal_commerce_user_on_boarded',
            AjaxRequestHandler::class,
            'onBoardedUserAjaxRequestHandler'
        );
        Hooks::addAction(
            'wp_ajax_give_paypal_commerce_get_partner_url',
            AjaxRequestHandler::class,
            'onGetPartnerUrlAjaxRequestHandler'
        );
        Hooks::addAction(
            'wp_ajax_give_paypal_commerce_disconnect_account',
            AjaxRequestHandler::class,
            'removePayPalAccount'
        );
        Hooks::addAction('wp_ajax_give_paypal_commerce_create_order', AjaxRequestHandler::class, 'createOrder');
        Hooks::addAction(
            'wp_ajax_give_paypal_commerce_onboarding_trouble_notice',
            AjaxRequestHandler::class,
            'onBoardingTroubleNotice'
        );
        Hooks::addAction('wp_ajax_nopriv_give_paypal_commerce_create_order', AjaxRequestHandler::class, 'createOrder');
        Hooks::addAction('wp_ajax_give_paypal_commerce_approve_order', AjaxRequestHandler::class, 'approveOrder');
        Hooks::addAction(
            'wp_ajax_nopriv_give_paypal_commerce_approve_order',
            AjaxRequestHandler::class,
            'approveOrder'
        );

        Hooks::addAction('admin_enqueue_scripts', ScriptLoader::class, 'loadAdminScripts');
        Hooks::addAction('wp_enqueue_scripts', ScriptLoader::class, 'loadPublicAssets');
        Hooks::addAction('give_pre_form_output', DonationFormPaymentMethod::class, 'handle');

        Hooks::addAction('give_paypal_commerce_refresh_token', RefreshToken::class, 'refreshToken');

        Hooks::addAction('admin_init', AccountAdminNotices::class, 'displayNotices');
        Hooks::addFilter(
            'give_payment_details_transaction_id-paypal-commerce',
            DonationDetailsPage::class,
            'getPayPalPaymentUrl'
        );

        Hooks::addAction('give_update_edited_donation', RefundPaymentHandler::class, 'refundPayment');
        Hooks::addAction('admin_notices', RefundPaymentHandler::class, 'showPaymentRefundFailureNotice');
        Hooks::addAction(
            'give_view_donation_details_totals_after',
            RefundPaymentHandler::class,
            'optInForRefundFormField'
        );

        Hooks::addAction('admin_init', WebhookChecker::class, 'checkWebhookCriteria');
    }
}
