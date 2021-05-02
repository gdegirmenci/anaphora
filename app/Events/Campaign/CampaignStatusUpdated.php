<?php

namespace App\Events\Campaign;

use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class CampaignStatusUpdated
 * @package App\Events\Campaign
 */
class CampaignStatusUpdated implements ShouldQueue
{
    private $campaignId;
    private $status;
    private $provider;

    /**
     * CampaignStatusUpdated constructor.
     * @param int $campaignId
     * @param string $provider
     * @param int $status
     */
    public function __construct(int $campaignId, string $provider, int $status)
    {
        $this->campaignId = $campaignId;
        $this->status = $status;
        $this->provider = $provider;
    }

    /**
     * @return int
     */
    public function getCampaignId(): int
    {
        return $this->campaignId;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }
}
