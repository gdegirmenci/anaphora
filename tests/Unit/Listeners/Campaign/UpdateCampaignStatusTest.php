<?php

namespace Tests\Unit\Listeners\Campaign;

use App\Events\Campaign\CampaignStatusUpdated;
use App\Listeners\Campaign\UpdateCampaignStatus;
use App\Repositories\Campaign\CampaignRepository;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Class UpdateCampaignStatusTest
 * @package Tests\Unit\Listeners\Campaign
 * @coversDefaultClass \App\Listeners\Campaign\UpdateCampaignStatus
 */
class UpdateCampaignStatusTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     * @covers ::__construct
     * @covers ::handle
     */
    function it_should_update_campaign_status()
    {
        $campaignId = random_int(1, 10);
        $provider = $this->faker->word;
        $status = random_int(0, 2);
        /** @var CampaignRepositoryInterface|MockObject $campaignRepository */
        $campaignRepository = $this->createMock(CampaignRepository::class);
        /** @var CampaignStatusUpdated|MockObject $campaignStatusUpdated */
        $campaignStatusUpdated = $this->createMock(CampaignStatusUpdated::class);
        $listener = new UpdateCampaignStatus($campaignRepository);

        $campaignStatusUpdated->expects($this->once())->method('getCampaignId')->willReturn($campaignId);
        $campaignStatusUpdated->expects($this->once())->method('getProvider')->willReturn($provider);
        $campaignStatusUpdated->expects($this->once())->method('getStatus')->willReturn($status);
        $campaignRepository->expects($this->once())
            ->method('updateCampaignStatus')
            ->with($campaignId, $provider, $status);

        $listener->handle($campaignStatusUpdated);
    }
}
