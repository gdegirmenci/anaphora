<?php

namespace App\Jobs\Campaign;

use App\Entities\CampaignEntity;
use App\Enums\CampaignStatusEnums;
use App\Events\Campaign\CampaignStatusUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Queue;

/**
 * Class CampaignSenderDispatcher
 * @package App\Jobs\Campaign
 */
class CampaignSenderDispatcher implements ShouldQueue
{
    use Queueable;

    /** @var CampaignEntity */
    private $campaignEntity;
    /** @var string */
    private $provider;

    /**
     * CampaignSenderDispatcher constructor.
     * @param CampaignEntity $campaignEntity
     * @param string $provider
     */
    public function __construct(CampaignEntity $campaignEntity, string $provider)
    {
        $this->campaignEntity = $campaignEntity;
        $this->provider = $provider;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        Queue::push(new CampaignSender($this->campaignEntity, $this->provider));
        event(
            new CampaignStatusUpdated(
                $this->campaignEntity->getCampaignId(),
                $this->provider,
                CampaignStatusEnums::QUEUED
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
