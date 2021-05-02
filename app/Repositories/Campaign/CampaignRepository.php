<?php

namespace App\Repositories\Campaign;

use App\Models\Campaign;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class CampaignRepository
 * @package App\Repositories\Campaign
 */
class CampaignRepository implements CampaignRepositoryInterface
{
    private $campaign;

    /**
     * CampaignRepository constructor.
     * @param Campaign $campaign
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->campaign->with('log')->paginate($perPage);
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
        return $this->campaign->queued()->count();
    }

    /**
     * @return int
     */
    public function totalSent(): int
    {
        return $this->campaign->sent()->count();
    }

    /**
     * @return int
     */
    public function totalFailed(): int
    {
        return $this->campaign->failed()->count();
    }
}
