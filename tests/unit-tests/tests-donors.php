<?php

/**
 * Class Give_Tests_Donors
 */
class Give_Tests_Donors extends Give_Unit_Test_Case {

	protected $_post_id = null;

	protected $_user_id = null;

	protected $_donor_id = null;

	protected $_donor = array();
	/**
	 * Set it up
	 */
	public function setUp() {
		parent::setUp();

		// Create a Donation Form
		$this->_post_id = $this->factory->post->create( array(
			'post_title'  => 'Test Form',
			'post_type'   => 'give_forms',
			'post_status' => 'publish',
		) );

		$_multi_level_donations = array(
			array(
				'_give_id'     => array(
					'level_id' => '1',
				),
				'_give_amount' => '10.00',
				'_give_text'   => 'Basic Level',
			),
			array(
				'_give_id'     => array(
					'level_id' => '2',
				),
				'_give_amount' => '20.00',
				'_give_text'   => 'Intermediate Level',
			),
			array(
				'_give_id'     => array(
					'level_id' => '3',
				),
				'_give_amount' => '40.00',
				'_give_text'   => 'Advanced Level',
			),
		);

		$meta = array(
			'give_price'            => '0.00',
			'_give_price_option'    => 'multi',
			'_give_donation_levels' => array_values( $_multi_level_donations ),
			'give_product_notes'    => 'Donation Notes',
			'_give_product_type'    => 'default',
		);
		foreach ( $meta as $key => $value ) {
			give_update_meta( $this->_post_id, $key, $value );
		}

		// Create User.
		$this->_user_id = $this->factory->user->create( array(
			'role' => 'administrator',
		) );
		$user           = get_userdata( $this->_user_id );

		// Create a donor.
		$this->_donor = new Give_Donor();
		$args = array(
			'name'  => 'Test Admin',
			'email' => 'testadmin@domain.com',
			'user_id' => $user->ID
		);
		$this->_donor->create( $args );

		// Generate Donations.
		$user_info = array(
			'id'         => $user->ID,
			'email'      => 'admin@example.org',
			'first_name' => $user->first_name,
			'last_name'  => $user->last_name,
		);

		$donation_details = array(
			array(
				'id'      => $this->_post_id,
				'options' => array(
					'price_id' => 1,
				),
			),
		);

		$total = 0;

		$prices     = give_get_meta( $donation_details[0]['id'], '_give_donation_levels', true );
		$item_price = $prices[1]['_give_amount'];

		$total += $item_price;

		$purchase_data = array(
			'price'           => number_format( (float) $total, 2 ),
			'give_form_title' => get_the_title( $this->_post_id ),
			'give_form_id'    => $this->_post_id,
			'date'            => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
			'purchase_key'    => strtolower( md5( uniqid() ) ),
			'user_email'      => $user_info['email'],
			'user_info'       => $user_info,
			'currency'        => 'USD',
			'status'          => 'pending',
			'gateway'         => 'manual',
		);

		$_SERVER['REMOTE_ADDR'] = '10.0.0.0';
		$_SERVER['SERVER_NAME'] = 'give_virtual';

		$payment_id = give_insert_payment( $purchase_data );

		give_update_payment_status( $payment_id, 'complete' );

	}

	/**
	 * Tear it Down
	 */
	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Test Add Donor
	 *
	 * @covers Give_Donor::create
	 */
	public function test_add_donor() {

		$test_email = 'testaccount@domain.com';

		$donor = new Give_Donor( $test_email );
		$this->assertEquals( 0, $donor->id );

		$data = array(
			'email' => $test_email,
		);

		$donor_id = $donor->create( $data );
		$this->assertTrue( is_numeric( $donor_id ) );
		$this->assertEquals( $donor->email, $test_email );
		$this->assertEquals( $donor->id, $donor_id );

	}

	/**
	 * Test Update Donor
	 *
	 * @covers Give_Donor::update
	 */
	public function test_update_donor() {

		$test_email = 'testaccount2@domain.com';

		$donor    = new Give_Donor( $test_email );
		$donor_id = $donor->create( array(
			'email' => $test_email,
		) );
		$this->assertEquals( $donor_id, $donor->id );

		$data_to_update = array(
			'email' => 'testaccountupdated@domain.com',
			'name' => 'Test Account',
		);
		$donor->update( $data_to_update );
		$this->assertEquals( 'testaccountupdated@domain.com', $donor->email );
		$this->assertEquals( 'Test Account', $donor->name );

		// Verify if we have an empty array we get false
		$this->assertFalse( $donor->update() );

	}

	/**
	 * Test Magic Get Method
	 *
	 * @covers Give_Donor::__get
	 */
	public function test_magic_get_method() {

		$donor = new Give_Donor( 'testadmin@domain.com' );
		$this->assertEquals( 'testadmin@domain.com', $donor->email );
		$this->assertTrue( is_wp_error( $donor->__get( 'asdf' ) ) );

	}

	/**
	 * Test attach payment.
	 */
	public function test_attach_payment() {

		$donor = new Give_Donor( 'testadmin@domain.com' );
		$donor->attach_payment( 5222222 );

		$payment_ids = array_map( 'absint', explode( ',', $donor->payment_ids ) );

		$this->assertTrue( in_array( 5222222, $payment_ids ) );

		// Verify if we don't send a payment, we get false.
		$this->assertFalse( $donor->attach_payment() );

	}

	/**
	 * Test attach duplicate payment.
	 */
	public function test_attach_duplicate_payment() {

		// Verify that if we pass a payment that's already attached we do not change stats.
		$donor = new Give_Donor( 'testadmin@domain.com' );
		$payments = array_map( 'absint', explode( ',', $donor->payment_ids ) );

		$expected_purchase_count = $donor->purchase_count;
		$expected_purchase_value = $donor->purchase_value;

		$donor->attach_payment( $payments[0] );
		$this->assertEquals( $expected_purchase_count, $donor->purchase_count );
		$this->assertEquals( $expected_purchase_value, $donor->purchase_value );

	}

	/**
	 * Test remove payment.
	 */
	public function test_remove_payment() {

		$donor = new Give_Donor( 'testadmin@domain.com' );
		$donor->attach_payment( 5222223, false );

		$payment_ids = array_map( 'absint', explode( ',', $donor->payment_ids ) );
		$this->assertTrue( in_array( 5222223, $payment_ids ) );

		$donor->remove_payment( 5222223, false );

		$payment_ids = array_map( 'absint', explode( ',', $donor->payment_ids ) );
		$this->assertFalse( in_array( 5222223, $payment_ids ) );
	}

	/**
	 * Test increment stats.
	 */
	public function test_increment_stats() {

		$donor = new Give_Donor( 'testadmin@domain.com' );

		$this->assertEquals( '20', $donor->purchase_value );
		$this->assertEquals( '1', $donor->purchase_count );

		$donor->increase_purchase_count();
		$donor->increase_value( 10 );

		$this->assertEquals( '30', $donor->purchase_value );
		$this->assertEquals( '2', $donor->purchase_count );

		$this->assertEquals( give_count_donations_of_donor( $this->_user_id ), '2' );
		$this->assertEquals( give_donation_total_of_user( $this->_user_id ), '30' );

		// Make sure we hit the false conditions
		$this->assertFalse( $donor->increase_purchase_count( - 1 ) );
		$this->assertFalse( $donor->increase_purchase_count( 'abc' ) );

	}

	/**
	 * Test stats decrement.
	 */
	public function test_decrement_stats() {

		$donor = new Give_Donor( 'testadmin@domain.com' );

		$donor->decrease_donation_count();
		$donor->decrease_value( 10 );

		$this->assertEquals( $donor->purchase_value, '10' );
		$this->assertEquals( $donor->purchase_count, '0' );

		$this->assertEquals( give_count_donations_of_donor( $this->_user_id ), '0' );
		$this->assertEquals( give_donation_total_of_user( $this->_user_id ), '10' );

		// Make sure we hit the false conditions
		$this->assertFalse( $donor->decrease_donation_count( - 1 ) );
		$this->assertFalse( $donor->decrease_donation_count( 'abc' ) );

		$donor->decrease_donation_count( 100 );
		$donor->decrease_value( 100000 );

		$this->assertEquals( intval( $donor->purchase_value ), intval( '0' ) );
		$this->assertEquals( intval( $donor->purchase_count ), intval( '0' ) );

	}

	/**
	 * Test donor notes.
	 */
	public function test_donor_notes() {

		$donor = new Give_Donor( 'testadmin@domain.com' );

		$this->assertInternalType( 'array', $donor->notes );
		$this->assertEquals( 0, $donor->get_notes_count() );

		$note_1 = $donor->add_note( 'Testing' );
		$this->assertEquals( 0, array_search( $note_1, $donor->notes ) );
		$this->assertEquals( 1, $donor->get_notes_count() );

		$note_2 = $donor->add_note( 'Test 2nd Note' );
		$this->assertEquals( 1, array_search( $note_1, $donor->notes ) );
		$this->assertEquals( 0, array_search( $note_2, $donor->notes ) );
		$this->assertEquals( 2, $donor->get_notes_count() );

		// Verify we took out all empty rows.
		$this->assertEquals( count( $donor->notes ), count( array_values( $donor->notes ) ) );

		// Test 1 note per page, page 1.
		$newest_note = $donor->get_notes( 1 );
		$this->assertEquals( 1, count( $newest_note ) );
		$this->assertEquals( $newest_note[0], $note_2 );

		// Test 1 note per page, page 2.
		$second_note = $donor->get_notes( 1, 2 );
		$this->assertEquals( 1, count( $second_note ) );
		$this->assertEquals( $second_note[0], $note_1 );
	}

	/**
	 * Test users donations.
	 */
	public function test_users_donations() {

		$out = give_get_users_donations( $this->_user_id );

		$this->assertInternalType( 'object', $out[0] );
		$this->assertEquals( 'give_payment', $out[0]->post_type );
		$this->assertTrue( give_has_donations( $this->_user_id ) );
		$this->assertEquals( 1, give_count_donations_of_donor( $this->_user_id ) );

		$no_user = give_get_users_donations( 0 );
		$this->assertFalse( $no_user );

		$no_user_count = give_count_donations_of_donor();
		$this->assertEquals( 0, $no_user_count );

	}

	/**
	 * Test users completed donations.
	 */
	public function test_give_get_users_completed_donations() {

		$out2 = give_get_users_completed_donations( $this->_user_id );

		$this->assertInternalType( 'array', $out2 );
		$this->assertEquals( 1, count( $out2 ) );
		$this->assertInternalType( 'object', $out2[0] );
		$this->assertEquals( $out2[0]->post_type, 'give_forms' );

	}

	/**
	 * Test donation stats by user.
	 */
	public function test_get_donation_stats_by_user() {

		$donation_stats = give_get_donation_stats_by_user( $this->_user_id );
		$this->assertInternalType( 'array', $donation_stats );
		$this->assertEquals( 2, count( $donation_stats ) );
		$this->assertTrue( isset( $donation_stats['purchases'] ) );
		$this->assertTrue( isset( $donation_stats['total_spent'] ) );

	}

	/**
	 * Test get donation total of user.
	 */
	public function test_get_donation_total_of_user() {
		$donation_total = give_donation_total_of_user( $this->_user_id );
		$this->assertEquals( 20, $donation_total );
	}

	/**
	 * Test validate usernmame.
	 */
	public function test_validate_username() {
		$this->assertTrue( give_validate_username( 'giveuser' ) );
		$this->assertFalse( give_validate_username( 'give12345$%&+-!@£%^&()(*&^%$£@!' ) );
	}

	/**
	 * Test total donor count.
	 */
	public function test_count_total_donors() {
		$donor_count = give_count_total_donors();
		$this->assertEquals( 1, $donor_count );
	}

	/**
	 * Tests get_first_name function of Give_Donor class.
	 *
	 * @since 1.8.12
	 *
	 * @cover Give_Donor::get_first_name()
	 */
	public function test_get_first_name() {

		$first_name = $this->_donor->get_first_name();
		$this->assertEquals( 'Test', $first_name );

	}

	/**
	 * Tests get_last_name function of Give_Donor class.
	 *
	 * @since 1.8.12
	 *
	 * @cover Give_Donor::get_last_name()
	 */
	public function test_get_last_name() {

		$last_name = $this->_donor->get_last_name();
		$this->assertEquals( 'Admin', $last_name );

	}

	/**
	 * Tests split_donor_name function of Give_Donor class.
	 *
	 * @since 1.8.12
	 *
	 * @cover Give_Donor::split_donor_name()
	 */
	public function test_split_donor_name() {

		$donor_name_split = $this->_donor->split_donor_name( $this->_donor->id );

		/**
		 * Check 1 - Check for type object.
		 *
		 * @since 1.8.12
		 */
		$this->assertInternalType( 'object', $donor_name_split );

		/**
		 * Check 2 - Check for existence of attribute first_name in object.
		 *
		 * @since 1.8.12
		 */
		$this->assertObjectHasAttribute( 'first_name', $donor_name_split );

		/**
		 * Check 3 - Check that first_name attribute of object is not empty.
		 *
		 * @since 1.8.12
		 */
		$this->assertNotEmpty( $donor_name_split->first_name );

		/**
		 * Check 4 - Check for existence of attribute last_name in object.
		 *
		 * @since 1.8.12
		 */
		$this->assertObjectHasAttribute( 'last_name', $donor_name_split );

	}
}
