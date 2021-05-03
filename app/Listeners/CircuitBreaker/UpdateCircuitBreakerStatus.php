<?php

namespace App\Listeners\CircuitBreaker;

use App\Enums\CampaignStatusEnums;
use App\Events\CircuitBreaker\CircuitBreakerStatusUpdated;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\Services\CircuitBreakerService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class UpdateCircuitBreakerStatus
 * @package App\Listeners\CircuitBreaker
 */
class UpdateCircuitBreakerStatus implements ShouldQueue
{
    /** @var CircuitBreakerService  */
    private $circuitBreakerService;
    /** @var CampaignRepositoryInterface */
    private $campaignRepository;

    /**
     * UpdateCircuitBreakerStatus constructor.
     * @param CircuitBreakerService $circuitBreakerService
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(
        CircuitBreakerService $circuitBreakerService,
        CampaignRepositoryInterface $campaignRepository
    ) {
        $this->circuitBreakerService = $circuitBreakerService;
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @param CircuitBreakerStatusUpdated $circuitBreakerStatusUpdated
     * @return void
     */
    public function handle(CircuitBreakerStatusUpdated $circuitBreakerStatusUpdated): void
    {
        $tracker = $circuitBreakerStatusUpdated->getTracker();
        $this->circuitBreakerService->updateCircuitBreakerStatus($tracker);
        $this->campaignRepository
            ->updateCampaignStatus(
                $tracker->getCampaignId(),
                $tracker->getKeys()->getProvider(),
                CampaignStatusEnums::FAILED
            );
    }
}
