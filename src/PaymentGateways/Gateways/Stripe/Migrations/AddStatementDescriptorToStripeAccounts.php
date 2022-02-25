<?php

namespace Give\PaymentGateways\Gateways\Stripe\Migrations;

use Give\Framework\Migrations\Contracts\Migration;
use Give\PaymentGateways\Stripe\Repositories\Settings;
use Give\PaymentGateways\Stripe\Traits\HasStripeStatementDescriptorText;

/**
 * @since 2.19.0
 */
class AddStatementDescriptorToStripeAccounts extends Migration
{
    use HasStripeStatementDescriptorText;

    /**
     * @inerhitDoc
     * @since 2.19.0
     */
    public function run()
    {
        $stripeSettings = give(Settings::class);
        $allStripeAccount = $stripeSettings->getAllStripeAccounts();

        if ($allStripeAccount) {
            $statementDescriptor = give_get_option('stripe_statement_descriptor', get_bloginfo('name'));
            foreach ($allStripeAccount as $index => $stripAccount) {
                if (!isset($stripAccount['statement_descriptor'])) {
                    $statementDescriptor = trim($statementDescriptor);
                    $this->validateStatementDescriptor($statementDescriptor);

                    $allStripeAccount[$index]['statement_descriptor'] = $statementDescriptor;
                }
            }

            give_update_option('_give_stripe_get_all_accounts', $allStripeAccount);
        }

        give_delete_option('stripe_statement_descriptor');
    }

    /**
     * @inerhitDoc
     * @since 2.19.0
     */
    public static function id()
    {
        return 'add-statement-descriptor-to-stripe-accounts';
    }

    /**
     * @inerhitDoc
     * @since 2.19.0
     */
    public static function timestamp()
    {
        return strtotime('10-02-2022');
    }

    /**
     * @inerhitDoc
     * @since 2.19.0
     */
    public static function title()
    {
        return 'Add Statement Descriptor To Stripe Accounts';
    }
}