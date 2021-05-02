<?php

namespace App\Repositories\Campaign;

use App\Models\Campaign;
use App\Models\CampaignLog;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class CampaignRepository
 * @package App\Repositories\Campaign
 */
class CampaignRepository implements CampaignRepositoryInterface
{
    private $campaign;
    private $campaignLog;

    /**
     * CampaignRepository constructor.
     * @param Campaign $campaign
     * @param CampaignLog $campaignLog
     */
    public function __construct(Campaign $campaign, CampaignLog $campaignLog)
    {
        $this->campaign = $campaign;
        $this->campaignLog = $campaignLog;
    }

    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->campaignLog->with('campaign')->paginate($perPage);
    }

    /**
     * @param array $fields
     * @return void
     */
    public function create(array $fields): void
    {
        $this->campaign->create($fields);
    }

    /**
     * @return int
     */
    public function totalQueued(): int
    {
        return $this->campaignLog->queued()->count();
    }

    /**
     * @return int
     */
    public function totalSent(): int
    {
        return $this->campaignLog->sent()->count();
    }

    /**
     * @return int
     */
    public function totalFailed(): int
    {
        return $this->campaignLog->failed()->count();
    }
}
