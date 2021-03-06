<?php

namespace App\Services;

use App\Enums\CircuitBreakerEnums;
use App\Events\CircuitBreaker\CircuitBreakerStatusUpdated;
use App\ValueObjects\CircuitBreaker\Keys;
use App\ValueObjects\CircuitBreaker\Tracker;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Redis;
use Psr\Http\Message\RequestInterface;

/**
 * Class CircuitBreakerService
 * @package App\Services
 */
class CircuitBreakerService
{
    /** @var ClientInterface */
    private $client;

    /**
     * CircuitBreakerService constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param RequestInterface $request
     * @param Tracker $tracker
     * @throws GuzzleException
     * @return bool
     */
    public function makeRequest(RequestInterface $request, Tracker $tracker): bool
    {
        try {
            $this->client->send($request);

            return true;
        } catch (Exception $exception) {
            event(new CircuitBreakerStatusUpdated($tracker));

            return false;
        }
    }

    /**
     * @param Tracker $tracker
     */
    public function updateCircuitBreakerStatus(Tracker $tracker): void
    {
        if ($tracker->isHalfOpened() && $tracker->isFailedCountExceed()) {
            $this->updateAs($tracker->getKeys(), CircuitBreakerEnums::OPENED);

            return;
        }

        $this->updateAs($tracker->getKeys(), CircuitBreakerEnums::HALF_OPENED);
        $this->increaseFailedCount($tracker->getKeys()->getFailedCountKey());
    }

    /**
     * @param Keys $keys
     * @param int $status
     * @return void
     */
    protected function updateAs(Keys $keys, int $status): void
    {
        Redis::set($keys->getStatusKey(), $status);
        Redis::expire($keys->getStatusKey(), CircuitBreakerEnums::STATUS_TIMEOUT);
    }

    /**
     * @param string $key
     * @return void
     */
    protected function increaseFailedCount(string $key): void
    {
        Redis::incr($key);
        Redis::expire($key, CircuitBreakerEnums::FAILED_COUNT_TIMEOUT);
    }
}
