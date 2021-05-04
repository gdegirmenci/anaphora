<?php

namespace Tests\Unit\ValueObjects\CircuitBreaker;

use App\ValueObjects\CircuitBreaker\Keys;
use App\ValueObjects\CircuitBreaker\Tracker;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Class TrackerTest
 * @package Tests\Unit\ValueObjects\CircuitBreaker
 * @coversDefaultClass \App\ValueObjects\CircuitBreaker\Tracker
 */
class TrackerTest extends TestCase
{
    use WithFaker;

    const CLOSED = 0;
    const HALF_OPENED = 1;
    const OPENED = 2;
    const CLOSED_ALIAS = 'closed';
    const HALF_OPENED_ALIAS = 'half-opened';
    const OPENED_ALIAS = 'opened';
    const MAX_FAILED_COUNT = 3;
    const STATUS_KEY = ':circuit-breaker:status';
    const FAILED_COUNT_KEY = ':circuit-breaker:failed-count';

    /**
     * @return Tracker
     */
    public function getTracker(): Tracker
    {
        $provider = $this->faker->word;
        $campaignId = random_int(1, 10);
        $keys = new Keys($provider);

        return new Tracker($keys, $campaignId);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::isOpened
     */
    function it_should_return_true_when_status_is_opened()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('get')->once()->with($tracker->getKeys()->getStatusKey())->andReturn(self::OPENED);

        $this->assertTrue($tracker->isOpened());
    }

    /**
     * @test
     * @covers ::isOpened
     */
    function it_should_return_false_when_status_is_not_opened()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('get')->once()->with($tracker->getKeys()->getStatusKey())->andReturn(self::HALF_OPENED);

        $this->assertFalse($tracker->isOpened());
    }

    /**
     * @test
     * @covers ::isHalfOpened
     */
    function it_should_return_true_when_status_is_half_opened()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('get')->once()->with($tracker->getKeys()->getStatusKey())->andReturn(self::HALF_OPENED);

        $this->assertTrue($tracker->isHalfOpened());
    }

    /**
     * @test
     * @covers ::isHalfOpened
     */
    function it_should_return_true_when_it_was_opened()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('exists')->with($tracker->getKeys()->getStatusKey())->andReturn(false);
        Redis::shouldReceive('get')->with($tracker->getKeys()->getStatusKey())->andReturn(0);
        Redis::shouldReceive('get')->with($tracker->getKeys()->getFailedCountKey())->andReturn(self::MAX_FAILED_COUNT);

        $this->assertTrue($tracker->isHalfOpened());
    }

    /**
     * @test
     * @covers ::isHalfOpened
     */
    function it_should_return_false_when_either_status_is_not_half_opened_or_it_was_not_opened()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('exists')->with($tracker->getKeys()->getStatusKey())->andReturn(false);
        Redis::shouldReceive('get')->with($tracker->getKeys()->getStatusKey())->andReturn(0);
        Redis::shouldReceive('get')->with($tracker->getKeys()->getFailedCountKey())->andReturn(random_int(0, 2));

        $this->assertFalse($tracker->isHalfOpened());
    }

    /**
     * @test
     * @covers ::isClosed
     */
    function it_should_return_true_when_status_not_exist_and_failed_count_is_not_exceed()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('exists')->with($tracker->getKeys()->getStatusKey())->andReturn(false);
        Redis::shouldReceive('get')->with($tracker->getKeys()->getFailedCountKey())->andReturn(random_int(0, 2));

        $this->assertTrue($tracker->isClosed());
    }

    /**
     * @test
     * @covers ::isClosed
     */
    function it_should_return_false_when_status_exists()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('exists')->with($tracker->getKeys()->getStatusKey())->andReturn(true);

        $this->assertFalse($tracker->isClosed());
    }

    /**
     * @test
     * @covers ::isClosed
     */
    function it_should_return_false_when_status_not_exists_but_failed_count_is_exceed()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('exists')->with($tracker->getKeys()->getStatusKey())->andReturn(true);
        Redis::shouldReceive('get')->with($tracker->getKeys()->getFailedCountKey())->andReturn(random_int(3, 9));

        $this->assertFalse($tracker->isClosed());
    }

    /**
     * @test
     * @covers ::isFailedCountExceed
     */
    function it_should_return_true_when_failed_count_is_exceed()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('get')->with($tracker->getKeys()->getFailedCountKey())->andReturn(random_int(3, 9));

        $this->assertTrue($tracker->isFailedCountExceed());
    }

    /**
     * @test
     * @covers ::isFailedCountExceed
     */
    function it_should_return_false_when_failed_count_is_not_exceed()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('get')->with($tracker->getKeys()->getFailedCountKey())->andReturn(random_int(0, 2));

        $this->assertFalse($tracker->isFailedCountExceed());
    }

    /**
     * @test
     * @covers ::getKeys
     */
    function it_should_return_keys()
    {
        $provider = $this->faker->word;
        $campaignId = random_int(1, 10);
        $keys = new Keys($provider);
        $tracker = new Tracker($keys, $campaignId);

        $this->assertEquals($keys, $tracker->getKeys());
    }

    /**
     * @test
     * @covers ::getCampaignId
     */
    function it_should_return_campaign_id()
    {
        $provider = $this->faker->word;
        $campaignId = random_int(1, 10);
        $keys = new Keys($provider);
        $tracker = new Tracker($keys, $campaignId);

        $this->assertEquals($campaignId, $tracker->getCampaignId());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getCampaignId
     */
    function it_should_return_null_when_campaign_id_is_not_given()
    {
        $provider = $this->faker->word;
        $keys = new Keys($provider);
        $tracker = new Tracker($keys);

        $this->assertNull($tracker->getCampaignId());
    }

    /**
     * @test
     * @covers ::getStatusAlias
     */
    function it_should_return_opened_alias_when_it_is_opened()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('get')->once()->with($tracker->getKeys()->getStatusKey())->andReturn(self::OPENED);

        $this->assertEquals(self::OPENED_ALIAS, $tracker->getStatusAlias());
    }

    /**
     * @test
     * @covers ::getStatusAlias
     */
    function it_should_return_half_opened_alias_when_it_is_half_opened()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('get')->with($tracker->getKeys()->getStatusKey())->andReturn(self::HALF_OPENED);

        $this->assertEquals(self::HALF_OPENED_ALIAS, $tracker->getStatusAlias());
    }

    /**
     * @test
     * @covers ::getStatusAlias
     */
    function it_should_return_closed_alias_when_it_is_not_opened_or_half_opened()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('get')->with($tracker->getKeys()->getStatusKey())->andReturn(self::CLOSED);
        Redis::shouldReceive('exists')->with($tracker->getKeys()->getStatusKey())->andReturn(true);

        $this->assertEquals(self::CLOSED_ALIAS, $tracker->getStatusAlias());
    }

    /**
     * @test
     * @covers ::getFailedCount
     */
    function it_should_return_failed_count()
    {
        $tracker = $this->getTracker();
        $failedCount = random_int(1, 10);

        Redis::shouldReceive('get')->with($tracker->getKeys()->getFailedCountKey())->andReturn($failedCount);

        $this->assertEquals($failedCount, $this->invokeMethod($tracker, 'getFailedCount'));
    }

    /**
     * @test
     * @covers ::getStatus
     */
    function it_should_return_status()
    {
        $tracker = $this->getTracker();
        $status = random_int(0, 2);

        Redis::shouldReceive('get')->with($tracker->getKeys()->getStatusKey())->andReturn($status);

        $this->assertEquals($status, $this->invokeMethod($tracker, 'getStatus'));
    }

    /**
     * @test
     * @covers ::wasOpened
     */
    function it_should_return_true_when_status_is_closed_and_failed_count_is_exceed()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('exists')->with($tracker->getKeys()->getStatusKey())->andReturn(false);
        Redis::shouldReceive('get')->with($tracker->getKeys()->getFailedCountKey())->andReturn(random_int(3, 9));

        $this->assertTrue($this->invokeMethod($tracker, 'wasOpened'));
    }

    /**
     * @test
     * @covers ::wasOpened
     */
    function it_should_return_false_when_status_is_not_closed()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('exists')->with($tracker->getKeys()->getStatusKey())->andReturn(true);

        $this->assertFalse($this->invokeMethod($tracker, 'wasOpened'));
    }

    /**
     * @test
     * @covers ::wasOpened
     */
    function it_should_return_false_when_status_is_closed_but_failed_count_is_exceed()
    {
        $tracker = $this->getTracker();

        Redis::shouldReceive('exists')->with($tracker->getKeys()->getStatusKey())->andReturn(false);
        Redis::shouldReceive('get')->with($tracker->getKeys()->getFailedCountKey())->andReturn(random_int(3, 9));

        $this->assertTrue($this->invokeMethod($tracker, 'wasOpened'));
    }
}
