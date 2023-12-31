<?php

namespace Give\Tests\Unit\Donors\Models;

use Exception;
use Give\Donations\Models\Donation;
use Give\Donors\Models\Donor;
use Give\Subscriptions\Models\Subscription;
use Give\Tests\TestCase;
use Give\Tests\TestTraits\RefreshDatabase;

/**
 * @since 2.19.6
 *
 * @coversDefaultClass \Give\Subscriptions\Models\Subscription
 */
class TestDonor extends TestCase
{
    use RefreshDatabase;

    /**
     * @since 2.19.6
     *
     * @return void
     *
     * @throws Exception
     */
    public function testCreateShouldInsertDonor()
    {
        $donor = Donor::factory()->create();

        $donorFromDatabase = Donor::find($donor->id);

        $this->assertEquals($donor->getAttributes(), $donorFromDatabase->getAttributes());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testShouldGetDonations()
    {
        /** @var Donor $donor */
        $donor = Donor::factory()->create();

        Donation::factory()->create(['donorId' => $donor->id]);
        Donation::factory()->create(['donorId' => $donor->id]);

        $this->assertCount(2, $donor->donations);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testShouldGetTotalDonations()
    {
        /** @var Donor $donor */
        $donor = Donor::factory()->create();

        Donation::factory()->create(['donorId' => $donor->id]);
        Donation::factory()->create(['donorId' => $donor->id]);

        $this->assertEquals(2, $donor->totalDonations());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testShouldGetSubscriptions()
    {
        /** @var Donor $donor */
        $donor = Donor::factory()->create();

        Subscription::factory()->createWithDonation(['donorId' => $donor->id]);
        Subscription::factory()->createWithDonation(['donorId' => $donor->id]);

        $this->assertCount(2, $donor->subscriptions);
    }
}
