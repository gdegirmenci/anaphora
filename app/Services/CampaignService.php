<?php

namespace App\Services;

use App\Enums\CampaignStatusEnums;
use App\Enums\ProviderEnums;
use App\Events\Campaign\CampaignStatusUpdated;
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
        $campaign = $this->campaignRepository->create($campaignPayload->toSave());
        event(new CampaignStatusUpdated($campaign->id, ProviderEnums::SENDGRID, CampaignStatusEnums::QUEUED));

        return ['success' => true];
    }
}
