<?php

namespace Tests\Unit\Listeners\CircuitBreaker;

use App\Events\CircuitBreaker\CircuitBreakerStatusUpdated;
use App\Listeners\CircuitBreaker\UpdateCircuitBreakerStatus;
use App\Repositories\Campaign\CampaignRepository;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\Services\CircuitBreakerService;
use App\ValueObjects\CircuitBreaker\Keys;
use App\ValueObjects\CircuitBreaker\Tracker;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Class UpdateCircuitBreakerStatusTest
 * @package Tests\Unit\Listeners\CircuitBreaker
 * @coversDefaultClass \App\Listeners\CircuitBreaker\UpdateCircuitBreakerStatus
 */
class UpdateCircuitBreakerStatusTest extends TestCase
{
    use WithFaker;

    const FAILED = 2;

    /**
     * @test
     * @covers ::__construct
     * @covers ::handle
     */
    function it_should_update_circuit_breaker_status()
    {
        /** @var CircuitBreakerService|MockObject $circuitBreakerService */
        $circuitBreakerService = $this->createMock(CircuitBreakerService::class);
        /** @var CampaignRepositoryInterface|MockObject $circuitBreakerService */
        $campaignRepository = $this->createMock(CampaignRepository::class);
        /** @var CircuitBreakerStatusUpdated|MockObject $circuitBreakerStatusUpdated */
        $circuitBreakerStatusUpdated = $this->createMock(CircuitBreakerStatusUpdated::class);
        $provider = $this->faker->word;
        $campaignId = random_int(1, 10);
        $tracker = new Tracker(new Keys($provider), $campaignId);
        $updateCircuitBreakerStatus = new UpdateCircuitBreakerStatus($circuitBreakerService, $campaignRepository);

        $circuitBreakerStatusUpdated->expects($this->once())->method('getTracker')->willReturn($tracker);
        $circuitBreakerService->expects($this->once())->method('updateCircuitBreakerStatus')->with($tracker);
        $campaignRepository->expects($this->once())
            ->method('updateCampaignStatus')
            ->with($campaignId, $provider, self::FAILED);

        $updateCircuitBreakerStatus->handle($circuitBreakerStatusUpdated);
    }
}
