<?php

namespace App\Services;

use App\Repositories\Campaign\CampaignRepositoryInterface;

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
     * @return void[]
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
        return ['SendGrid' => 'Closed', 'Mailjet' => 'Open'];
    }
}
