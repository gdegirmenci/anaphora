<?php

namespace App\ValueObjects\CircuitBreaker;

use App\Enums\CircuitBreakerEnums;
use Illuminate\Support\Facades\Redis;

/**
 * Class Tracker
 * @package App\ValueObjects\CircuitBreaker
 */
final class Tracker
{
    /** @var Keys */
    private $keys;
    /** @var int */
    private $campaignId;

    /**
     * Tracker constructor.
     * @param Keys $keys
     * @param int $campaignId
     */
    public function __construct(Keys $keys, int $campaignId)
    {
        $this->keys = $keys;
        $this->campaignId = $campaignId;
    }

    /**
     * @return bool
     */
    public function isOpened(): bool
    {
        return $this->getStatus() === CircuitBreakerEnums::OPENED;
    }

    /**
     * @return bool
     */
    public function isHalfOpened(): bool
    {
        return $this->getStatus() === CircuitBreakerEnums::HALF_OPENED || $this->wasOpened();
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return !Redis::exists($this->keys->getStatusKey()) && !$this->isFailedCountExceed();
    }

    /**
     * @return bool
     */
    public function isFailedCountExceed(): bool
    {
        return $this->getFailedCount() >= CircuitBreakerEnums::MAX_FAILED_COUNT;
    }

    /**
     * @return Keys
     */
    public function getKeys(): Keys
    {
        return $this->keys;
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
    protected function getFailedCount(): int
    {
        return (int)Redis::get($this->keys->getFailedCountKey());
    }

    /**
     * @return int
     */
    protected function getStatus(): int
    {
        return (int)Redis::get($this->keys->getStatusKey());
    }

    /**
     * @return bool
     */
    protected function wasOpened(): bool
    {
        return !Redis::exists($this->keys->getStatusKey()) && $this->isFailedCountExceed();
    }
}
