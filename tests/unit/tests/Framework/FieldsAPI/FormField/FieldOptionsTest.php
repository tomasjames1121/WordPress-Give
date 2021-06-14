<?php

use PHPUnit\Framework\TestCase;
use Give\Framework\FieldsAPI\FormField;

final class FieldOptionsTest extends TestCase {

    public function testFieldSupportsOptions() {

        $field = new FormField( 'text', 'my-text-field' );
        $this->assertFalse( $field->supportsOptions() );

        $field = new FormField( 'select', 'my-select-field' );
        $this->assertTrue( $field->supportsOptions() );
    }

    public function testSetOptions() {
        $field = new FormField( 'select', 'my-select-field' );

        $field->options( [ [ 'aye', 'Aye' ] ] );
        $this->assertCount( 1, $field->getOptions() );

        $field->options([
			[ 'aye', 'Aye' ],
			[ 'bee', 'bee' ],
		]);
        $this->assertCount( 2, $field->getOptions() );
    }
}
