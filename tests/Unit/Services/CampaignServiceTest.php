<?php

namespace Tests\Unit\Services;

use App\Events\Campaign\CampaignStatusUpdated;
use App\Models\Campaign;
use App\Repositories\Campaign\CampaignRepository;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\Services\CampaignService;
use App\ValueObjects\Payloads\CampaignPayload;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
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
    function it_should_return_dashboard_data_with_overview_and_provider_status()
    {
        Event::fake();
        $campaignPayload = new CampaignPayload([
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
            ->with($campaignPayload->toSave())
            ->willReturn($campaign);

        $this->assertEquals(['success' => true], $this->service->create($campaignPayload));
        Event::assertDispatched(
            CampaignStatusUpdated::class,
            function (CampaignStatusUpdated $event) use ($campaign) {
                $this->assertProperty($event, 'campaignId', $campaign->id);
                $this->assertProperty($event, 'provider', self::DEFAULT_PROVIDER);
                $this->assertProperty($event, 'status', self::QUEUED);

                return true;
            }
        );
    }
}
