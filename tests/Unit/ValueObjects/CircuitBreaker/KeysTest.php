<?php

namespace Tests\Unit\ValueObjects\CircuitBreaker;

use App\ValueObjects\CircuitBreaker\Keys;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Class KeysTest
 * @package Tests\Unit\ValueObjects\CircuitBreaker
 * @coversDefaultClass \App\ValueObjects\CircuitBreaker\Keys
 */
class KeysTest extends TestCase
{
    use WithFaker;

    const STATUS_KEY = ':circuit-breaker:status';
    const FAILED_COUNT_KEY = ':circuit-breaker:failed-count';

    /**
     * @test
     * @covers ::__construct
     * @covers ::getStatusKey
     */
    function it_should_return_status_key()
    {
        $provider = $this->faker->word;
        $keys = new Keys($provider);
        $statusKey = $provider . self::STATUS_KEY;

        $this->assertEquals($statusKey, $keys->getStatusKey());
    }

    /**
     * @test
     * @covers ::getFailedCountKey
     */
    function it_should_return_failed_count_key()
    {
        $provider = $this->faker->word;
        $keys = new Keys($provider);
        $failedCountKey = $provider . self::FAILED_COUNT_KEY;

        $this->assertEquals($failedCountKey, $keys->getFailedCountKey());
    }

    /**
     * @test
     * @covers ::getProvider
     */
    function it_should_return_provider()
    {
        $provider = $this->faker->word;
        $keys = new Keys($provider);

        $this->assertEquals($provider, $keys->getProvider());
    }
}
