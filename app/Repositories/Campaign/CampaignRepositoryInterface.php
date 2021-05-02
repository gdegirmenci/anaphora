<?php

namespace App\Repositories\Campaign;

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
     * @return void
     */
    public function create(array $fields): void;

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
}
