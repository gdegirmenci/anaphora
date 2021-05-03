<?php

namespace Tests\Unit\Services\Providers;

use App\Models\CampaignLog;
use App\Repositories\Campaign\CampaignRepository;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\Services\Providers\ProviderServiceInterface;
use App\Services\Providers\SendGridService;
use App\ValueObjects\CircuitBreaker\Keys;
use App\ValueObjects\CircuitBreaker\Tracker;
use App\ValueObjects\Email\Email;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Suites\ServiceTestSuite;

/**
 * Class BaseProviderServiceTest
 * @package Tests\Unit\Services\Providers
 * @coversDefaultClass \App\Services\Providers\BaseProviderService
 */
class BaseProviderServiceTest extends ServiceTestSuite
{
    use WithFaker;

    const METHOD = 'POST';
    const MAIL_JET = 'mailjet';
    const SEND_GRID = 'sendgrid';
    const STATUS_KEY = ':circuit-breaker:status';
    const CLOSED = 0;
    const OPENED = 2;

    /** @var ProviderServiceInterface|MockObject */
    private $service;
    /** @var Email */
    private $email;
    /** @var CampaignRepositoryInterface|MockObject */
    private $campaignRepository;

    /**
     * @return void
     */
    public function setService(): void
    {
        $this->email = new Email(
            $this->faker->sentence,
            ['name' => $this->faker->name, 'email' => $this->faker->email],
            ['name' => $this->faker->name, 'email' => $this->faker->email],
            [['name' => $this->faker->name, 'email' => $this->faker->email]],
            $this->faker->sentence,
            'text'
        );
        $this->campaignRepository = $this->createMock(CampaignRepository::class);
        $this->service = new SendGridService($this->campaignRepository, $this->email);
    }

    /**
     * @param array $methods
     * @return void
     */
    public function setServiceMock(array $methods): void
    {
        $this->service = $this->getMockBuilder(SendGridService::class)
            ->setConstructorArgs([$this->campaignRepository, $this->email])
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getRequest
     */
    function it_should_return_request()
    {
        $this->setServiceMock(['getMethod', 'getUrl', 'getHeaders', 'getBody']);
        $url = $this->faker->url;
        $headers = [$this->faker->word => $this->faker->word];
        $body = collect([$this->faker->word => $this->faker->word]);

        $this->service->expects($this->once())->method('getMethod')->willReturn(self::METHOD);
        $this->service->expects($this->once())->method('getUrl')->willReturn($url);
        $this->service->expects($this->once())->method('getHeaders')->willReturn($headers);
        $this->service->expects($this->once())->method('getBody')->willReturn($body);

        $this->assertInstanceOf(Request::class, $this->service->getRequest());
    }

    /**
     * @test
     * @covers ::getMethod
     */
    function it_should_return_method()
    {
        $this->assertEquals(self::METHOD, $this->service->getMethod());
    }

    /**
     * @test
     * @covers ::getRecipients
     */
    function it_should_return_recipients()
    {
        $this->assertEquals($this->email->getTo(), $this->service->getRecipients());
    }

    /**
     * @test
     * @covers ::switchProvider
     */
    function it_should_return_switched_provider_when_it_is_not_opened_and_not_failed()
    {
        $statusKey = self::MAIL_JET . self::STATUS_KEY;
        $campaignId = random_int(1, 10);

        Redis::shouldReceive('get')->with($statusKey)->andReturn(self::CLOSED);
        $this->campaignRepository
            ->expects($this->once())
            ->method('getFailedLogByProvider')
            ->with($campaignId, self::MAIL_JET)
            ->willReturn(null);

        $this->assertEquals(self::MAIL_JET, $this->service->switchProvider($campaignId));
    }

    /**
     * @test
     * @covers ::switchProvider
     */
    function it_should_return_null_when_it_is_opened()
    {
        $statusKey = self::MAIL_JET . self::STATUS_KEY;
        $campaignId = random_int(1, 10);

        Redis::shouldReceive('get')->with($statusKey)->andReturn(self::OPENED);

        $this->assertNull($this->service->switchProvider($campaignId));
    }

    /**
     * @test
     * @covers ::switchProvider
     */
    function it_should_return_null_when_it_is_not_opened_but_failed()
    {
        $statusKey = self::MAIL_JET . self::STATUS_KEY;
        $campaignId = random_int(1, 10);
        $campaignLog = new CampaignLog();

        Redis::shouldReceive('get')->with($statusKey)->andReturn(self::CLOSED);
        $this->campaignRepository
            ->expects($this->once())
            ->method('getFailedLogByProvider')
            ->with($campaignId, self::MAIL_JET)
            ->willReturn($campaignLog);

        $this->assertNull($this->service->switchProvider($campaignId));
    }

    /**
     * @test
     * @covers ::getTracker
     */
    function it_should_return_tracker()
    {
        $provider = $this->faker->word;
        $campaignId = random_int(1, 10);
        $tracker = new Tracker(new Keys($provider), $campaignId);

        $this->assertEquals($tracker, $this->invokeMethod($this->service, 'getTracker', [$provider, $campaignId]));
    }
}
