<?php

namespace App\ValueObjects\CircuitBreaker;

/**
 * Class Keys
 * @package App\ValueObjects\CircuitBreaker
 */
final class Keys
{
    const STATUS_KEY = ':circuit-breaker:status';
    const FAILED_COUNT_KEY = ':circuit-breaker:failed-count';

    /** @var string */
    private $provider;

    /**
     * Keys constructor.
     * @param string $provider
     */
    public function __construct(string $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return string
     */
    public function getStatusKey(): string
    {
        return $this->provider . self::STATUS_KEY;
    }

    /**
     * @return string
     */
    public function getFailedCountKey(): string
    {
        return $this->provider . self::FAILED_COUNT_KEY;
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }
}
