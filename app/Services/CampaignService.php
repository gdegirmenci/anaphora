<?php

namespace App\Services;

use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\ValueObjects\Payloads\CampaignPayload;

/**
 * Class CampaignService
 * @package App\Services
 */
class CampaignService
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
     * @param CampaignPayload $campaignPayload
     * @return array
     */
    public function create(CampaignPayload $campaignPayload): array
    {
        $this->campaignRepository->create($campaignPayload->toSave());

        return ['success' => true];
    }
}
