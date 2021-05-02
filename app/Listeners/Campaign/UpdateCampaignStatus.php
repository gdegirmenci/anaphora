<?php

namespace App\Listeners\Campaign;

use App\Events\Campaign\CampaignStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\Campaign\CampaignRepositoryInterface;

/**
 * Class UpdateCampaignStatus
 * @package App\Listeners\Campaign
 */
class UpdateCampaignStatus implements ShouldQueue
{
    /** @var CampaignRepositoryInterface */
    private $campaignRepository;

    /**
     * UpdateCampaignStatus constructor.
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(CampaignRepositoryInterface $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @param CampaignStatusUpdated $campaignStatusUpdated
     * @return void
     */
    public function handle(CampaignStatusUpdated $campaignStatusUpdated): void
    {
        $this->campaignRepository
            ->updateCampaignStatus(
                $campaignStatusUpdated->getCampaignId(),
                $campaignStatusUpdated->getProvider(),
                $campaignStatusUpdated->getStatus()
            );
    }
}
