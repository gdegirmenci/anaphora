<?php

namespace App\Events\CircuitBreaker;

use App\ValueObjects\CircuitBreaker\Tracker;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class CircuitBreakerStatusUpdated
 * @package App\Events\CircuitBreaker
 */
class CircuitBreakerStatusUpdated implements ShouldQueue
{
    /** @var Tracker */
    private $tracker;

    /**
     * CircuitBreakerStatusUpdated constructor.
     * @param Tracker $tracker
     */
    public function __construct(Tracker $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * @return Tracker
     */
    public function getTracker(): Tracker
    {
        return $this->tracker;
    }
}
