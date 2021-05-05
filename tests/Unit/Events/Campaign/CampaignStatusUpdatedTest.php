<?php

namespace Tests\Unit\Events\Campaign;

use App\Events\Campaign\CampaignStatusUpdated;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class CampaignStatusUpdatedTest
 * @package Tests\Unit\Events\Campaign
 * @coversDefaultClass \App\Events\Campaign\CampaignStatusUpdated
 */
class CampaignStatusUpdatedTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     * @covers ::__construct
     * @covers ::getCampaignId
     */
    function it_should_return_campaign_id()
    {
        $campaignId = random_int(1, 10);
        $event = new CampaignStatusUpdated($campaignId, $this->faker->word, random_int(0, 2));

        $this->assertEquals($campaignId, $event->getCampaignId());
    }

    /**
     * @test
     * @covers ::getStatus
     */
    function it_should_return_status()
    {
        $status = random_int(0, 2);
        $event = new CampaignStatusUpdated(random_int(1, 10), $this->faker->word, $status);

        $this->assertEquals($status, $event->getStatus());
    }

    /**
     * @test
     * @covers ::getProvider
     */
    function it_should_return_provider()
    {
        $provider = $this->faker->word;
        $event = new CampaignStatusUpdated(random_int(1, 10), $provider, random_int(0, 2));

        $this->assertEquals($provider, $event->getProvider());
    }
}
