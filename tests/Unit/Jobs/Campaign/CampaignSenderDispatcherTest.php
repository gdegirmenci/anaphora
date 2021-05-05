<?php

namespace Tests\Unit\Jobs\Campaign;

use App\Entities\CampaignEntity;
use App\Events\Campaign\CampaignStatusUpdated;
use App\Jobs\Campaign\CampaignSender;
use App\Jobs\Campaign\CampaignSenderDispatcher;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Class CampaignSenderDispatcherTest
 * @package Tests\Unit\Jobs\Campaign
 * @coversDefaultClass \App\Jobs\Campaign\CampaignSenderDispatcher
 */
class CampaignSenderDispatcherTest extends TestCase
{
    use WithFaker;

    const QUEUED = 0;
    const FAILED = 2;

    /**
     * @test
     * @covers ::__construct
     * @covers ::handle
     */
    function it_should_handle()
    {
        Event::fake();
        Queue::fake();
        $campaignEntity = new CampaignEntity([]);
        $campaignId = random_int(1, 10);
        $campaignEntity->setCampaignId($campaignId);
        $provider = $this->faker->word;
        $job = new CampaignSenderDispatcher($campaignEntity, $provider);

        $job->handle();

        Queue::assertPushed(
            CampaignSender::class,
            function (CampaignSender $campaignSender) use ($campaignEntity, $provider) {
                $this->assertProperty($campaignSender, 'campaignEntity', $campaignEntity);
                $this->assertProperty($campaignSender, 'provider', $provider);

                return true;
            }
        );
        Event::assertDispatched(
            CampaignStatusUpdated::class,
            function (CampaignStatusUpdated $event) use ($campaignId, $provider) {
                $this->assertProperty($event, 'campaignId', $campaignId);
                $this->assertProperty($event, 'provider', $provider);
                $this->assertProperty($event, 'status', self::QUEUED);

                return true;
            }
        );
    }

    /**
     * @test
     * @covers ::failed
     */
    function it_should_update_campaign_status_when_failed()
    {
        Event::fake();
        $campaignEntity = new CampaignEntity([]);
        $campaignId = random_int(1, 10);
        $campaignEntity->setCampaignId($campaignId);
        $provider = $this->faker->word;
        $job = new CampaignSenderDispatcher($campaignEntity, $provider);

        $job->failed();

        Event::assertDispatched(
            CampaignStatusUpdated::class,
            function (CampaignStatusUpdated $event) use ($campaignId, $provider) {
                $this->assertProperty($event, 'campaignId', $campaignId);
                $this->assertProperty($event, 'provider', $provider);
                $this->assertProperty($event, 'status', self::FAILED);

                return true;
            }
        );
    }
}
