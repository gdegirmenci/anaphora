<?php

namespace Tests\Unit\Services;

use App\Entities\CampaignEntity;
use App\Jobs\Campaign\CampaignSenderDispatcher;
use App\Models\Campaign;
use App\Repositories\Campaign\CampaignRepository;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\Services\CampaignService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Suites\ServiceTestSuite;

/**
 * Class CampaignServiceTest
 * @package Tests\Unit\Services
 * @coversDefaultClass \App\Services\CampaignService
 */
class CampaignServiceTest extends ServiceTestSuite
{
    use WithFaker;

    const QUEUED = 0;
    const DEFAULT_PROVIDER = 'sendgrid';

    /** @var CampaignRepositoryInterface|MockObject */
    private $campaignRepository;
    /** @var CampaignService */
    private $service;

    /**
     * @return void
     */
    public function setService(): void
    {
        $this->campaignRepository = $this->createMock(CampaignRepository::class);
        $this->service = new CampaignService($this->campaignRepository);
    }

    /**
     * @test
     * @covers ::create
     * @covers ::__construct
     */
    function it_should_return_success_with_dispatching_campaign_sender_dispatcher()
    {
        Queue::fake();
        $campaignEntity = new CampaignEntity([
            'name' => $this->faker->word,
            'template' => $this->faker->word,
            'type' => $this->faker->word,
            'to' => ['email' => $this->faker->email],
        ]);
        $campaign = new Campaign();
        $campaign->id = random_int(1, 10);

        $this->campaignRepository
            ->expects($this->once())
            ->method('create')
            ->with($campaignEntity->toSave())
            ->willReturn($campaign);

        $this->assertEquals(['success' => true], $this->service->create($campaignEntity));
        Queue::assertPushed(
            CampaignSenderDispatcher::class,
            function (CampaignSenderDispatcher $campaignSenderDispatcher) use ($campaignEntity) {
                $this->assertProperty($campaignSenderDispatcher, 'campaignEntity', $campaignEntity);
                $this->assertProperty($campaignSenderDispatcher, 'provider', config('mail.primary_provider'));

                return true;
            }
        );
    }
}
