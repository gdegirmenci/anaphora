<?php

namespace Tests\Unit\Services;

use App\Repositories\Campaign\CampaignRepository;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\Services\CampaignService;
use App\ValueObjects\Payloads\CampaignPayload;
use Illuminate\Foundation\Testing\WithFaker;
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
        $campaignPayload = new CampaignPayload([
            'name' => $this->faker->word,
            'template' => $this->faker->word,
            'type' => $this->faker->word,
        ]);

        $this->campaignRepository->expects($this->once())->method('create')->with($campaignPayload->toSave());

        $this->assertEquals(['success' => true], $this->service->create($campaignPayload));
    }
}
