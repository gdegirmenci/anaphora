<?php

namespace Tests\Unit\Services\Providers;

use App\Services\Providers\ProviderServiceInterface;
use App\Services\Providers\SendGridService;
use App\ValueObjects\Email\Email;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Testing\WithFaker;
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

    /** @var ProviderServiceInterface|MockObject */
    private $service;
    /** @var Email */
    private $email;

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
        $this->service = new SendGridService($this->email);
    }

    /**
     * @param array $methods
     * @return void
     */
    public function setServiceMock(array $methods): void
    {
        $this->service = $this->getMockBuilder(SendGridService::class)
            ->setConstructorArgs([$this->email])
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * @test
     * @covers ::getRequest
     * @covers ::__construct
     */
    function it_should_return_request()
    {
        $this->setServiceMock(['getMethod', 'getUrl', 'getHeaders', 'getBody']);
        $url = $this->faker->url;
        $headers = [$this->faker->word => $this->faker->word];
        $body = [$this->faker->word => $this->faker->word];

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
    function it_should_switch_provider()
    {
        $this->assertEquals(self::MAIL_JET, $this->service->switchProvider());
    }
}
