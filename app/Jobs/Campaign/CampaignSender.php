<?php

namespace App\Jobs\Campaign;

use App\Entities\CampaignEntity;
use App\Enums\CampaignStatusEnums;
use App\Events\Campaign\CampaignStatusUpdated;
use App\Factories\ProviderServiceFactory;
use App\Services\CircuitBreakerService;
use App\ValueObjects\CircuitBreaker\Keys;
use App\ValueObjects\CircuitBreaker\Tracker;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Queue;

/**
 * Class CampaignSender
 * @package App\Jobs\Campaign
 */
class CampaignSender implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    /** @var CampaignEntity */
    private $campaignEntity;
    /** @var string */
    private $provider;

    /**
     * CampaignSender constructor.
     * @param CampaignEntity $campaignEntity
     * @param string $provider
     */
    public function __construct(CampaignEntity $campaignEntity, string $provider)
    {
        $this->campaignEntity = $campaignEntity;
        $this->provider = $provider;
    }

    /**
     * @param ProviderServiceFactory $providerServiceFactory
     * @param CircuitBreakerService $circuitBreakerService
     * @throws GuzzleException
     */
    public function handle(
        ProviderServiceFactory $providerServiceFactory,
        CircuitBreakerService $circuitBreakerService
    ): void {
        $providerService = $providerServiceFactory->make($this->campaignEntity, $this->provider);
        $tracker = new Tracker(new Keys($this->provider), $this->campaignEntity->getCampaignId());

        if ($tracker->isOpened()) {
            Queue::push(new CampaignSenderDispatcher($this->campaignEntity, $providerService->switchProvider()));

            return;
        }

        $status = $circuitBreakerService->makeRequest($providerService->getRequest(), $tracker);

        if (!$status) {
            Queue::push(new CampaignSenderDispatcher($this->campaignEntity, $providerService->switchProvider()));
            event(
                new CampaignStatusUpdated(
                    $this->campaignEntity->getCampaignId(),
                    $this->provider,
                    CampaignStatusEnums::FAILED
                )
            );

            return;
        }

        event(
            new CampaignStatusUpdated(
                $this->campaignEntity->getCampaignId(),
                $this->provider,
                CampaignStatusEnums::SENT
            )
        );
    }

    /**
     * @return void
     */
    public function failed(): void
    {
        event(
            new CampaignStatusUpdated(
                $this->campaignEntity->getCampaignId(),
                $this->provider,
                CampaignStatusEnums::FAILED
            )
        );
    }
}
