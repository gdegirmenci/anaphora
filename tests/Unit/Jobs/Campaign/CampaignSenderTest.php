<?php

namespace Tests\Unit\Jobs\Campaign;

use App\Entities\CampaignEntity;
use App\Events\Campaign\CampaignStatusUpdated;
use App\Factories\ProviderServiceFactory;
use App\Jobs\Campaign\CampaignSender;
use App\Jobs\Campaign\CampaignSenderDispatcher;
use App\Services\CircuitBreakerService;
use App\Services\Providers\ProviderServiceInterface;
use App\Services\Providers\SendGridService;
use App\ValueObjects\CircuitBreaker\Keys;
use App\ValueObjects\CircuitBreaker\Tracker;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\RequestInterface;
use Tests\TestCase;

/**
 * Class CampaignSenderTest
 * @package Tests\Unit\Jobs\Campaign
 * @coversDefaultClass \App\Jobs\Campaign\CampaignSender
 */
class CampaignSenderTest extends TestCase
{
    use WithFaker;

    const QUEUED = 0;
    const SENT = 1;
    const FAILED = 2;
    const CLOSED = 0;
    const OPENED = 2;

    /**
     * @test
     * @covers ::__construct
     * @covers ::handle
     */
    function it_should_dispatch_another_campaign_sender_dispatcher_with_other_provider_when_circuit_is_opened()
    {
        Event::fake();
        Queue::fake();
        $campaignEntity = new CampaignEntity([]);
        $campaignId = random_int(1, 10);
        $campaignEntity->setCampaignId($campaignId);
        $provider = $this->faker->word;
        $availableProvider = $this->faker->word;
        $job = new CampaignSender($campaignEntity, $provider);
        /** @var ProviderServiceFactory|MockObject $providerServiceFactory */
        $providerServiceFactory = $this->createMock(ProviderServiceFactory::class);
        /** @var ProviderServiceInterface|MockObject $sendGridService */
        $sendGridService = $this->createMock(SendGridService::class);
        /** @var CircuitBreakerService|MockObject $circuitBreakerService */
        $circuitBreakerService = $this->createMock(CircuitBreakerService::class);
        $keys = new Keys($provider);

        $providerServiceFactory->expects($this->once())
            ->method('make')
            ->with($campaignEntity, $provider)
            ->willReturn($sendGridService);
        $sendGridService->expects($this->once())
            ->method('switchProvider')
            ->with($campaignId)
            ->willReturn($availableProvider);
        Redis::shouldReceive('get')->with($keys->getStatusKey())->andReturn(self::OPENED);
        $sendGridService->expects($this->once())->method('switchProvider')->willReturn($availableProvider);

        $job->handle($providerServiceFactory, $circuitBreakerService);

        Queue::assertPushed(
            CampaignSenderDispatcher::class,
            function (CampaignSenderDispatcher $campaignSenderDispatcher) use ($campaignEntity, $availableProvider) {
                $this->assertProperty($campaignSenderDispatcher, 'campaignEntity', $campaignEntity);
                $this->assertProperty($campaignSenderDispatcher, 'provider', $availableProvider);

                return true;
            }
        );
        Event::assertDispatched(
            CampaignStatusUpdated::class,
            function (CampaignStatusUpdated $event) use ($campaignId, $provider) {
                $this->assertProperty($event, 'campaignId', $campaignId);
                $this->assertProperty($event, 'provider', $provider);
                $this->assertProperty($event, 'status', self::FAILED);

                return true;
            }
        );
    }

    /**
     * @test
     * @covers ::handle
     */
    function it_should_should_not_dispatch_another_dispatcher_when_circuit_is_opened_and_available_provider_is_false()
    {
        Event::fake();
        $campaignEntity = new CampaignEntity([]);
        $campaignId = random_int(1, 10);
        $campaignEntity->setCampaignId($campaignId);
        $provider = $this->faker->word;
        $availableProvider = $this->faker->word;
        $job = new CampaignSender($campaignEntity, $provider);
        /** @var ProviderServiceFactory|MockObject $providerServiceFactory */
        $providerServiceFactory = $this->createMock(ProviderServiceFactory::class);
        /** @var ProviderServiceInterface|MockObject $sendGridService */
        $sendGridService = $this->createMock(SendGridService::class);
        /** @var CircuitBreakerService|MockObject $circuitBreakerService */
        $circuitBreakerService = $this->createMock(CircuitBreakerService::class);
        $keys = new Keys($provider);

        $providerServiceFactory->expects($this->once())
            ->method('make')
            ->with($campaignEntity, $provider)
            ->willReturn($sendGridService);
        $sendGridService->expects($this->once())->method('switchProvider')->with($campaignId)->willReturn(null);
        Redis::shouldReceive('get')->with($keys->getStatusKey())->andReturn(self::OPENED);
        $sendGridService->expects($this->once())->method('switchProvider')->willReturn($availableProvider);

        $job->handle($providerServiceFactory, $circuitBreakerService);

        Event::assertDispatched(
            CampaignStatusUpdated::class,
            function (CampaignStatusUpdated $event) use ($campaignId, $provider) {
                $this->assertProperty($event, 'campaignId', $campaignId);
                $this->assertProperty($event, 'provider', $provider);
                $this->assertProperty($event, 'status', self::FAILED);

                return true;
            }
        );
    }

    /**
     * @test
     * @covers ::handle
     */
    function it_should_dispatch_another_dispatcher_when_status_is_false()
    {
        Event::fake();
        $campaignEntity = new CampaignEntity([]);
        $campaignId = random_int(1, 10);
        $campaignEntity->setCampaignId($campaignId);
        $provider = $this->faker->word;
        $job = new CampaignSender($campaignEntity, $provider);
        /** @var ProviderServiceFactory|MockObject $providerServiceFactory */
        $providerServiceFactory = $this->createMock(ProviderServiceFactory::class);
        /** @var ProviderServiceInterface|MockObject $sendGridService */
        $sendGridService = $this->createMock(SendGridService::class);
        /** @var CircuitBreakerService|MockObject $circuitBreakerService */
        $circuitBreakerService = $this->createMock(CircuitBreakerService::class);
        $keys = new Keys($provider);
        $tracker = new Tracker($keys, $campaignId);
        /** @var RequestInterface|MockObject $request */
        $request = $this->createMock(RequestInterface::class);

        $providerServiceFactory->expects($this->once())
            ->method('make')
            ->with($campaignEntity, $provider)
            ->willReturn($sendGridService);
        $sendGridService->expects($this->once())->method('switchProvider')->willReturn(null);
        Redis::shouldReceive('get')->with($keys->getStatusKey())->andReturn(self::CLOSED);
        $sendGridService->expects($this->once())->method('getRequest')->willReturn($request);
        $circuitBreakerService->expects($this->once())
            ->method('makeRequest')
            ->with($request, $tracker)
            ->willReturn(false);

        $job->handle($providerServiceFactory, $circuitBreakerService);

        Event::assertDispatched(
            CampaignStatusUpdated::class,
            function (CampaignStatusUpdated $event) use ($campaignId, $provider) {
                $this->assertProperty($event, 'campaignId', $campaignId);
                $this->assertProperty($event, 'provider', $provider);
                $this->assertProperty($event, 'status', self::FAILED);

                return true;
            }
        );
    }

    /**
     * @test
     * @covers ::handle
     */
    function it_should_not_dispatcher_another_dispatcher_when_status_is_false_and_available_provider_is_false()
    {
        Event::fake();
        Queue::fake();
        $campaignEntity = new CampaignEntity([]);
        $campaignId = random_int(1, 10);
        $campaignEntity->setCampaignId($campaignId);
        $provider = $this->faker->word;
        $fallBackProvider = $this->faker->word;
        $job = new CampaignSender($campaignEntity, $provider);
        /** @var ProviderServiceFactory|MockObject $providerServiceFactory */
        $providerServiceFactory = $this->createMock(ProviderServiceFactory::class);
        /** @var ProviderServiceInterface|MockObject $sendGridService */
        $sendGridService = $this->createMock(SendGridService::class);
        /** @var CircuitBreakerService|MockObject $circuitBreakerService */
        $circuitBreakerService = $this->createMock(CircuitBreakerService::class);
        $keys = new Keys($provider);
        $tracker = new Tracker($keys, $campaignId);
        /** @var RequestInterface|MockObject $request */
        $request = $this->createMock(RequestInterface::class);

        $providerServiceFactory->expects($this->once())
            ->method('make')
            ->with($campaignEntity, $provider)
            ->willReturn($sendGridService);
        Redis::shouldReceive('get')->with($keys->getStatusKey())->andReturn(self::CLOSED);
        $sendGridService->expects($this->once())->method('getRequest')->willReturn($request);
        $circuitBreakerService->expects($this->once())
            ->method('makeRequest')
            ->with($request, $tracker)
            ->willReturn(false);
        $sendGridService->expects($this->once())->method('switchProvider')->willReturn($fallBackProvider);

        $job->handle($providerServiceFactory, $circuitBreakerService);

        Queue::assertPushed(
            CampaignSenderDispatcher::class,
            function (CampaignSenderDispatcher $campaignSenderDispatcher) use ($campaignEntity, $fallBackProvider) {
                $this->assertProperty($campaignSenderDispatcher, 'campaignEntity', $campaignEntity);
                $this->assertProperty($campaignSenderDispatcher, 'provider', $fallBackProvider);

                return true;
            }
        );
        Event::assertDispatched(
            CampaignStatusUpdated::class,
            function (CampaignStatusUpdated $event) use ($campaignId, $provider) {
                $this->assertProperty($event, 'campaignId', $campaignId);
                $this->assertProperty($event, 'provider', $provider);
                $this->assertProperty($event, 'status', self::FAILED);

                return true;
            }
        );
    }

    /**
     * @test
     * @covers ::handle
     */
    function it_should_dispatch_campaign_status_updated_event_when_request_is_not_failed()
    {
        Event::fake();
        $campaignEntity = new CampaignEntity([]);
        $campaignId = random_int(1, 10);
        $campaignEntity->setCampaignId($campaignId);
        $provider = $this->faker->word;
        $job = new CampaignSender($campaignEntity, $provider);
        /** @var ProviderServiceFactory|MockObject $providerServiceFactory */
        $providerServiceFactory = $this->createMock(ProviderServiceFactory::class);
        /** @var ProviderServiceInterface|MockObject $sendGridService */
        $sendGridService = $this->createMock(SendGridService::class);
        /** @var CircuitBreakerService|MockObject $circuitBreakerService */
        $circuitBreakerService = $this->createMock(CircuitBreakerService::class);
        $keys = new Keys($provider);
        $tracker = new Tracker($keys, $campaignId);
        /** @var RequestInterface|MockObject $request */
        $request = $this->createMock(RequestInterface::class);

        $providerServiceFactory->expects($this->once())
            ->method('make')
            ->with($campaignEntity, $provider)
            ->willReturn($sendGridService);
        Redis::shouldReceive('get')->with($keys->getStatusKey())->andReturn(self::CLOSED);
        $sendGridService->expects($this->once())->method('getRequest')->willReturn($request);
        $circuitBreakerService->expects($this->once())
            ->method('makeRequest')
            ->with($request, $tracker)
            ->willReturn(true);

        $job->handle($providerServiceFactory, $circuitBreakerService);

        Event::assertDispatched(
            CampaignStatusUpdated::class,
            function (CampaignStatusUpdated $event) use ($campaignId, $provider) {
                $this->assertProperty($event, 'campaignId', $campaignId);
                $this->assertProperty($event, 'provider', $provider);
                $this->assertProperty($event, 'status', self::SENT);

                return true;
            }
        );
    }

    /**
     * @test
     * @covers ::failed
     */
    function it_should_update_campaign_status_when_failed()
    {
        Event::fake();
        $campaignEntity = new CampaignEntity([]);
        $campaignId = random_int(1, 10);
        $campaignEntity->setCampaignId($campaignId);
        $provider = $this->faker->word;
        $job = new CampaignSender($campaignEntity, $provider);

        $job->failed();

        Event::assertDispatched(
            CampaignStatusUpdated::class,
            function (CampaignStatusUpdated $event) use ($campaignId, $provider) {
                $this->assertProperty($event, 'campaignId', $campaignId);
                $this->assertProperty($event, 'provider', $provider);
                $this->assertProperty($event, 'status', self::FAILED);

                return true;
            }
        );
    }
}
