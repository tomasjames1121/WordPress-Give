<?php

use PHPUnit\Framework\TestCase;
use Give\Form\LegacyConsumer\TemplateHooks;
use Give\Form\LegacyConsumer\Commands\DeprecateOldTemplateHook;

final class DeprecateOldTemplateHookTest extends Give_Unit_Test_Case {
    
    public function testDeprecatedHooksShowNotice() {

        /**
         * Spying on `_give_deprecated_function` by listening to an action hook.
         */
        $count = 0;
        add_action( 'give_deprecated_function_run', function( $function, $replacement, $version ) use ( &$count ) {
            $count += 1;
        }, 10, 3 );

        /**
         * Add deprecated actions and then deprecate them.
         */
        $command = new DeprecateOldTemplateHook();
        give( TemplateHooks::class )->walk( function( $hook ) use ( $command ){
            add_action( "give_$hook", function() {});
            $command( $hook );
        });

        // The spied count for `_give_deprecated_function` should equal the number of template hooks.
        $this->assertEquals( count( TemplateHooks::TEMPLATE_HOOKS ), $count );
    }
}
