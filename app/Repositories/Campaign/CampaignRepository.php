<?php

namespace App\Repositories\Campaign;

use App\Enums\CampaignStatusEnums;
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
        return $this->campaignLog->with('campaign')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * @param array $fields
     * @return Campaign
     */
    public function create(array $fields): Campaign
    {
        return $this->campaign->create($fields);
    }

    /**
     * @param int $campaignId
     * @param string $provider
     * @param int $status
     * @return void
     */
    public function updateCampaignStatus(int $campaignId, string $provider, int $status): void
    {
        $this->campaignLog->updateOrCreate(['campaign_id' => $campaignId, 'provider' => $provider], compact('status'));
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

    /**
     * @param int $campaignId
     * @param string $provider
     * @return CampaignLog|null
     */
    public function getFailedLogByProvider(int $campaignId, string $provider): ?CampaignLog
    {
        return $this->campaignLog
            ->where(['campaign_id' => $campaignId, 'provider' => $provider, 'status' => CampaignStatusEnums::FAILED])
            ->first();
    }
}
