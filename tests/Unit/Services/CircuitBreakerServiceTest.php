<?php

namespace Tests\Unit\Services;

use App\Events\CircuitBreaker\CircuitBreakerStatusUpdated;
use App\Services\CircuitBreakerService;
use App\ValueObjects\CircuitBreaker\Keys;
use App\ValueObjects\CircuitBreaker\Tracker;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\RequestInterface;
use Tests\Suites\ServiceTestSuite;

/**
 * Class CircuitBreakerServiceTest
 * @package Tests\Unit\Services
 * @coversDefaultClass \App\Services\CircuitBreakerService
 */
class CircuitBreakerServiceTest extends ServiceTestSuite
{
    use WithFaker;

    const CLOSED = 0;
    const HALF_OPENED = 1;
    const OPENED = 2;
    const STATUS_KEY = ':circuit-breaker:status';
    const FAILED_COUNT_KEY = ':circuit-breaker:failed-count';
    const MAX_FAILED_COUNT = 3;
    const STATUS_TIMEOUT = Carbon::SECONDS_PER_MINUTE * 5;
    const FAILED_COUNT_TIMEOUT = Carbon::SECONDS_PER_MINUTE * 5 * 2;

    /** @var ClientInterface|MockObject */
    private $client;
    /** @var CircuitBreakerService|MockObject */
    private $service;

    /**
     * @return void
     */
    public function setService(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->service = new CircuitBreakerService($this->client);
    }

    /**
     * @param array $methods
     * @return void
     */
    public function setServiceMock(array $methods = []): void
    {
        $this->service = $this->getMockBuilder(CircuitBreakerService::class)
            ->setConstructorArgs([$this->client])
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * @test
     * @covers ::makeRequest
     * @covers ::__construct
     */
    function it_should_return_true_and_make_request()
    {
        /** @var RequestInterface|MockObject $request */
        $request = $this->createMock(RequestInterface::class);
        $provider = $this->faker->word;
        $campaignId = random_int(1, 10);
        $tracker = new Tracker(new Keys($provider), $campaignId);

        $this->client->expects($this->once())->method('send')->with($request);

        $this->assertTrue($this->service->makeRequest($request, $tracker));
    }

    /**
     * @test
     * @covers ::makeRequest
     * @covers ::__construct
     */
    function it_should_return_false_and_dispatch_circuit_breaker_status_updated_event_when_thrown_an_exception()
    {
        Event::fake();
        /** @var RequestInterface|MockObject $request */
        $request = $this->createMock(RequestInterface::class);
        $provider = $this->faker->word;
        $campaignId = random_int(1, 10);
        $tracker = new Tracker(new Keys($provider), $campaignId);
        $exception = new Exception();

        $this->client->expects($this->once())->method('send')->with($request)->willThrowException($exception);

        $this->assertFalse($this->service->makeRequest($request, $tracker));
        Event::assertDispatched(
            CircuitBreakerStatusUpdated::class,
            function (CircuitBreakerStatusUpdated $event) use ($tracker) {
                $this->assertProperty($event, 'tracker', $tracker);

                return true;
            }
        );
    }

    /**
     * @test
     * @covers ::updateCircuitBreakerStatus
     */
    function it_should_update_circuit_breaker_status_as_opened_when_it_is_half_opened_and_exceed_max_failed_count()
    {
        $this->setServiceMock(['updateAs']);
        $provider = $this->faker->word;
        $campaignId = random_int(1, 10);
        $tracker = new Tracker(new Keys($provider), $campaignId);
        $statusKey = $provider . self::STATUS_KEY;
        $failedCountKey = $provider . self::FAILED_COUNT_KEY;

        Redis::shouldReceive('get')->with($statusKey)->andReturn(self::HALF_OPENED);
        Redis::shouldReceive('get')->with($failedCountKey)->andReturn(self::MAX_FAILED_COUNT);
        $this->service->expects($this->once())->method('updateAs')->with($tracker->getKeys(), self::OPENED);

        $this->service->updateCircuitBreakerStatus($tracker);
    }

    /**
     * @test
     * @covers ::updateCircuitBreakerStatus
     */
    function it_should_update_circuit_breaker_status_as_half_opened_when_it_is_not_half_opened()
    {
        $this->setServiceMock(['updateAs', 'increaseFailedCount']);
        $provider = $this->faker->word;
        $campaignId = random_int(1, 10);
        $tracker = new Tracker(new Keys($provider), $campaignId);
        $statusKey = $provider . self::STATUS_KEY;
        $failedCountKey = $provider . self::FAILED_COUNT_KEY;

        Redis::shouldReceive('get')->with($statusKey)->andReturn(self::CLOSED);
        Redis::shouldReceive('exists')->with($statusKey)->andReturn(false);
        Redis::shouldReceive('get')->with($failedCountKey)->andReturn(random_int(0, 2));
        $this->service->expects($this->once())->method('updateAs')->with($tracker->getKeys(), self::HALF_OPENED);
        $this->service
            ->expects($this->once())
            ->method('increaseFailedCount')
            ->with($tracker->getKeys()->getFailedCountKey());

        $this->service->updateCircuitBreakerStatus($tracker);
    }

    /**
     * @test
     * @covers ::updateCircuitBreakerStatus
     */
    function it_should_update_circuit_breaker_status_as_half_opened_when_it_is_not_exceed_max_failed_count()
    {
        $this->setServiceMock(['updateAs', 'increaseFailedCount']);
        $provider = $this->faker->word;
        $campaignId = random_int(1, 10);
        $tracker = new Tracker(new Keys($provider), $campaignId);
        $statusKey = $provider . self::STATUS_KEY;
        $failedCountKey = $provider . self::FAILED_COUNT_KEY;

        Redis::shouldReceive('get')->with($statusKey)->andReturn(self::HALF_OPENED);
        Redis::shouldReceive('get')->with($failedCountKey)->andReturn(random_int(0, 2));
        $this->service->expects($this->once())->method('updateAs')->with($tracker->getKeys(), self::HALF_OPENED);
        $this->service
            ->expects($this->once())
            ->method('increaseFailedCount')
            ->with($tracker->getKeys()->getFailedCountKey());

        $this->service->updateCircuitBreakerStatus($tracker);
    }

    /**
     * @test
     * @covers ::updateAs
     */
    function it_should_update_as_with_given_keys_and_status()
    {
        $provider = $this->faker->word;
        $keys = new Keys($provider);
        $status = random_int(0, 2);

        Redis::shouldReceive('set')->once()->with($keys->getStatusKey(), $status);
        Redis::shouldReceive('expire')->once()->with($keys->getStatusKey(), self::STATUS_TIMEOUT);

        $this->invokeMethod($this->service, 'updateAs', [$keys, $status]);
    }

    /**
     * @test
     * @covers ::increaseFailedCount
     */
    function it_should_increase_failed_count()
    {
        $key = $this->faker->word;

        Redis::shouldReceive('incr')->once()->with($key);
        Redis::shouldReceive('expire')->once()->with($key, self::FAILED_COUNT_TIMEOUT);

        $this->invokeMethod($this->service, 'increaseFailedCount', [$key]);
    }
}
