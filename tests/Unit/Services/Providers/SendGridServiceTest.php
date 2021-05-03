<?php

namespace Tests\Unit\Services\Providers;

use App\Services\Providers\ProviderServiceInterface;
use App\Services\Providers\SendGridService;
use App\ValueObjects\Email\Email;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Suites\ServiceTestSuite;

/**
 * Class SendGridServiceTest
 * @package Tests\Unit\Services\Providers
 * @coversDefaultClass \App\Services\Providers\SendGridService
 */
class SendGridServiceTest extends ServiceTestSuite
{
    use WithFaker;

    const PROVIDER = 'sendgrid';

    /** @var ProviderServiceInterface */
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
     * @test
     * @covers ::getUrl
     * @covers ::__construct
     */
    function it_should_return_url()
    {
        $this->assertEquals(config('services.sendgrid.endpoint'), $this->service->getUrl());
    }

    /**
     * @test
     * @covers ::getHeaders
     */
    function it_should_return_headers()
    {
        $headers = [
            'Authorization' => sprintf('Bearer %s', config('services.sendgrid.secret')),
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
        $body = [
            'personalizations' => [['to' => $this->email->getTo(), 'subject' => $this->email->getSubject()]],
            'from' => $this->email->getFrom()->toArray(),
            'reply_to' => $this->email->getReply()->toArray(),
            'content' => $this->email->getReply()->toArray(),
        ];

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
}
