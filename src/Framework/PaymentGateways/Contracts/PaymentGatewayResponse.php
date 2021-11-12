<?php

namespace Give\Framework\PaymentGateways\Contracts;

/**
 * @unreleased
 */
abstract class PaymentGatewayResponse implements PaymentGatewayResponseInterface
{

    /**
     * @param  int  $paymentId
     * @param  string  $transactionId
     */
    public function updatePaymentMeta($paymentId, $transactionId)
    {
        give_update_payment_status($paymentId);
        give_set_payment_transaction_id($paymentId, $transactionId);
    }
}