<?php

namespace App\Services;

use App\Enums\ProviderEnums;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\ValueObjects\CircuitBreaker\Keys;
use App\ValueObjects\CircuitBreaker\Tracker;

/**
 * Class DashboardService
 * @package App\Services
 */
class DashboardService
{
    /** @var CampaignRepositoryInterface */
    private $campaignRepository;

    /**
     * DashboardService constructor.
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(CampaignRepositoryInterface $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @return array
     */
    public function getDashboardData(): array
    {
        return ['overview' => $this->getOverview(), 'providerStatus' => $this->getProviderStatus()];
    }

    /**
     * @return array
     */
    protected function getOverview(): array
    {
        return [
            'queued' => $this->campaignRepository->totalQueued(),
            'sent' => $this->campaignRepository->totalSent(),
            'failed' => $this->campaignRepository->totalFailed(),
        ];
    }

    /**
     * @return array
     */
    protected function getProviderStatus(): array
    {
        $sendGridTracker = new Tracker(new Keys(ProviderEnums::SEND_GRID));
        $mailJetTracker = new Tracker(new Keys(ProviderEnums::MAIL_JET));

        return [
            ['name' => ProviderEnums::SEND_GRID_ALIAS, 'status' => $sendGridTracker->getStatusAlias()],
            ['name' => ProviderEnums::MAIL_JET_ALIAS, 'status' => $mailJetTracker->getStatusAlias()],
        ];
    }
}
