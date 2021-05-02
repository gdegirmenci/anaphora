<?php

namespace Tests\Unit\Services;

use App\Repositories\Campaign\CampaignRepository;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Suites\ServiceTestSuite;

/**
 * Class DashboardServiceTest
 * @package Tests\Unit\Services
 * @coversDefaultClass \App\Services\DashboardService
 */
class DashboardServiceTest extends ServiceTestSuite
{
    use WithFaker;

    /** @var CampaignRepositoryInterface|MockObject */
    private $campaignRepository;
    /** @var DashboardService|MockObject */
    private $service;

    /**
     * @return void
     */
    public function setService(): void
    {
        $this->campaignRepository = $this->createMock(CampaignRepository::class);
        $this->service = new DashboardService($this->campaignRepository);
    }

    /**
     * @param array $methods
     * @return void
     */
    public function setServiceMock(array $methods = []): void
    {
        $this->service = $this->getMockBuilder(DashboardService::class)
            ->setConstructorArgs([$this->campaignRepository])
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * @test
     * @covers ::getDashboardData
     * @covers ::__construct
     */
    function it_should_return_dashboard_data_with_overview_and_provider_status()
    {
        $this->setServiceMock(['getOverview', 'getProviderStatus']);
        $overview = ['queued' => random_int(1, 10), 'sent' => random_int(1, 10), 'failed' => random_int(1, 10)];
        $providerStatus = [$this->faker->word => $this->faker->word];
        $dashboardData = compact('overview', 'providerStatus');

        $this->service->expects($this->once())->method('getOverview')->willReturn($overview);
        $this->service->expects($this->once())->method('getProviderStatus')->willReturn($providerStatus);

        $this->assertEquals($dashboardData, $this->service->getDashboardData());
    }

    /**
     * @test
     * @covers ::getOverview
     */
    function it_should_return_overview_with_total_queued_and_total_sent_and_total_failed()
    {
        $queued = random_int(1, 10);
        $sent = random_int(1, 10);
        $failed = random_int(1, 10);
        $overview = compact('queued', 'sent', 'failed');

        $this->campaignRepository->expects($this->once())->method('totalQueued')->willReturn($queued);
        $this->campaignRepository->expects($this->once())->method('totalSent')->willReturn($sent);
        $this->campaignRepository->expects($this->once())->method('totalFailed')->willReturn($failed);

        $this->assertEquals($overview, $this->invokeMethod($this->service, 'getOverview'));
    }

    /**
     * @test
     * @covers ::getProviderStatus
     */
    function it_should_return_provider_status()
    {
        $providerStatus = [
            ['name' => 'SendGrid', 'status' => 'closed'],
            ['name' => 'MailJet', 'status' => 'half-opened'],
        ];

        $this->assertEquals($providerStatus, $this->invokeMethod($this->service, 'getProviderStatus'));
    }
}
