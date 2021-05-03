<?php

namespace App\Services;

use App\Entities\CampaignEntity;
use App\Jobs\Campaign\CampaignSenderDispatcher;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use Illuminate\Support\Facades\Queue;

/**
 * Class CampaignService
 * @package App\Services
 */
class CampaignService
{
    /** @var CampaignRepositoryInterface */
    private $campaignRepository;

    /**
     * CampaignService constructor.
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(CampaignRepositoryInterface $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @param CampaignEntity $campaignEntity
     * @return array
     */
    public function create(CampaignEntity $campaignEntity): array
    {
        $campaign = $this->campaignRepository->create($campaignEntity->toSave());
        $campaignEntity->setCampaignId($campaign->id);
        Queue::push(new CampaignSenderDispatcher($campaignEntity, config('mail.primary_provider')));

        return ['success' => true];
    }
}
