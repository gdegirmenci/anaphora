<?php

namespace App\Repositories\Campaign;

use App\Models\Campaign;
use App\Models\CampaignLog;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface CampaignRepositoryInterface
 * @package App\Repositories\Campaign
 */
interface CampaignRepositoryInterface
{
    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage): LengthAwarePaginator;

    /**
     * @param array $fields
     * @return Campaign
     */
    public function create(array $fields): Campaign;

    /**
     * @param int $campaignId
     * @param string $provider
     * @param int $status
     * @return void
     */
    public function updateCampaignStatus(int $campaignId, string $provider, int $status): void;

    /**
     * @return int
     */
    public function totalQueued(): int;

    /**
     * @return int
     */
    public function totalSent(): int;

    /**
     * @return int
     */
    public function totalFailed(): int;

    /**
     * @param int $campaignId
     * @param string $provider
     * @return CampaignLog|null
     */
    public function getFailedLogByProvider(int $campaignId, string $provider): ?CampaignLog;
}
