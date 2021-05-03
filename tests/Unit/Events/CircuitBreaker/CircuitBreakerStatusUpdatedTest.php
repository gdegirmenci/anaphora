<?php

namespace Tests\Unit\Events\CircuitBreaker;

use App\Events\CircuitBreaker\CircuitBreakerStatusUpdated;
use App\ValueObjects\CircuitBreaker\Keys;
use App\ValueObjects\CircuitBreaker\Tracker;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class CircuitBreakerStatusUpdatedTest
 * @package Tests\Unit\Events\CircuitBreaker
 * @coversDefaultClass \App\Events\CircuitBreaker\CircuitBreakerStatusUpdated
 */
class CircuitBreakerStatusUpdatedTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     * @covers ::__construct
     * @covers ::getTracker
     */
    function it_should_return_tracker()
    {
        $provider = $this->faker->word;
        $campaignId = random_int(1, 10);
        $tracker = new Tracker(new Keys($provider), $campaignId);
        $circuitBreakerStatusUpdated = new CircuitBreakerStatusUpdated($tracker);

        $this->assertEquals($tracker, $circuitBreakerStatusUpdated->getTracker());
    }
}
