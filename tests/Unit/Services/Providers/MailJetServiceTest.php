<?php

namespace Tests\Unit\Services\Providers;

use App\Services\Providers\MailJetService;
use App\Services\Providers\ProviderServiceInterface;
use App\ValueObjects\Email\Email;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Suites\ServiceTestSuite;

/**
 * Class MailJetServiceTest
 * @package Tests\Unit\Services\Providers
 * @coversDefaultClass \App\Services\Providers\MailJetService
 */
class MailJetServiceTest extends ServiceTestSuite
{
    use WithFaker;

    const PROVIDER = 'mailjet';

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
        $this->service = new MailJetService($this->email);
    }

    /**
     * @param array $methods
     * @return void
     */
    public function setServiceMock(array $methods): void
    {
        $this->service = $this->getMockBuilder(MailJetService::class)
            ->setConstructorArgs([$this->email])
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * @test
     * @covers ::getUrl
     * @covers ::__construct
     */
    function it_should_return_url()
    {
        $this->assertEquals(config('services.mailjet.endpoint'), $this->service->getUrl());
    }

    /**
     * @test
     * @covers ::getHeaders
     */
    function it_should_return_headers()
    {
        $headers = [
            'Authorization' => sprintf('Basic %s', config('services.mailjet.secret')),
            'Content-Type' => 'application/json',
        ];

        $this->assertEquals($headers, $this->service->getHeaders());
    }

    /**
     * @test
     * @covers ::getBody
     */
    function it_should_return_body()
    {
        $this->setServiceMock(['getRecipients']);
        $recipients = [['Name' => $this->faker->name, 'Email' => $this->faker->email]];
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->email->getFrom()->getEmail(),
                        'Name' => $this->email->getFrom()->getName(),
                    ],
                    'ReplyTo' => [
                        'Email' => $this->email->getReply()->getEmail(),
                        'Name' => $this->email->getReply()->getName(),
                    ],
                    'To' => $recipients,
                    'Subject' => $this->email->getSubject(),
                    'TextPart' => $this->email->getTemplate(),
                ],
            ],
        ];

        $this->service->expects($this->once())->method('getRecipients')->willReturn($recipients);

        $this->assertEquals($body, $this->service->getBody());
    }

    /**
     * @test
     * @covers ::getProvider
     */
    function it_should_return_provider()
    {
        $this->assertEquals(self::PROVIDER, $this->service->getProvider());
    }

    /**
     * @test
     * @covers ::getRecipients
     */
    function it_should_return_recipients()
    {
        $recipients = collect($this->email->getTo())
            ->map(function (array $to) {
                return ['Email' => Arr::get($to, 'email'), 'Name' => Arr::get($to, 'name')];
            })
            ->toArray();

        $this->assertEquals($recipients, $this->service->getRecipients());
    }
}
