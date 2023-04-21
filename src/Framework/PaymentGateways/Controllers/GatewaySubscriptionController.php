<?php

namespace Give\Framework\PaymentGateways\Controllers;

use Give\Donations\Models\Donation;
use Give\Framework\Exceptions\Primitives\Exception;
use Give\Framework\FieldsAPI\Exceptions\TypeNotSupported;
use Give\Framework\PaymentGateways\Actions\HandleGatewaySubscriptionCommand;
use Give\Framework\PaymentGateways\Commands\GatewayCommand;
use Give\Framework\PaymentGateways\Log\PaymentGatewayLog;
use Give\Framework\PaymentGateways\PaymentGateway;
use Give\Framework\PaymentGateways\Traits\HandleHttpResponses;
use Give\Subscriptions\Models\Subscription;

/**
 * @unreleased
 */
class GatewaySubscriptionController
{
    use HandleHttpResponses;

    /**
     * @var PaymentGateway
     */
    protected $gateway;

    /**
     * @unreleased
     */
    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @unreleased
     */
    public function create(Donation $donation, Subscription $subscription, array $gatewayData = [])
    {
        try {
            $command = $this->gateway->createSubscription($donation, $subscription, $gatewayData);
            $this->handleGatewayCommand($command, $donation, $subscription);
        } catch (\Exception $exception) {
            PaymentGatewayLog::error(
                $exception->getMessage(),
                [
                    'Payment Gateway' => $this->gateway::id(),
                    'Donation' => $donation->toArray(),
                    'Subscription' => $subscription->toArray(),
                ]
            );

            $message = __(
                'An unexpected error occurred while processing the subscription.  Please try again or contact the site administrator.',
                'give'
            );

            $this->handleExceptionResponse($exception, $message);
        }
    }

    /**
     * Handle gateway subscription command
     *
     * @unreleased move logic into action
     * @since 2.26.0 add RespondToBrowser command
     * @since 2.21.0 Handle RedirectOffsite response.
     * @since 2.18.0
     *
     * @throws TypeNotSupported
     * @throws Exception
     */
    public function handleGatewayCommand(
        GatewayCommand $command,
        Donation $donation,
        Subscription $subscription
    ) {
        $response = (new HandleGatewaySubscriptionCommand())($command, $donation, $subscription);

        $this->handleResponse($response);
    }
}
