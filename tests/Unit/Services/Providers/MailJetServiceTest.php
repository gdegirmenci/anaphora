<?php

namespace Tests\Unit\Services\Providers;

use App\Repositories\Campaign\CampaignRepository;
use App\Repositories\Campaign\CampaignRepositoryInterface;
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
    const TEXT_TYPE = 'text/plain';
    const HTML_TYPE = 'text/html';

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
            self::TEXT_TYPE
        );
        $this->campaignRepository = $this->createMock(CampaignRepository::class);
        $this->service = new MailJetService($this->campaignRepository, $this->email);
    }

    /**
     * @param array $methods
     * @return void
     */
    public function setServiceMock(array $methods): void
    {
        $this->service = $this->getMockBuilder(MailJetService::class)
            ->setConstructorArgs([$this->campaignRepository, $this->email])
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getUrl
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
    function it_should_return_body_and_filled_text_part_when_type_is_text()
    {
        $this->setServiceMock(['getRecipients']);
        $recipients = [['Name' => $this->faker->name, 'Email' => $this->faker->email]];
        $body = collect([
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
                    'TextPart' => $this->email->getTemplate()->getContent(),
                    'HTMLPart' => '',
                ],
            ],
        ]);

        $this->service->expects($this->once())->method('getRecipients')->willReturn($recipients);

        $this->assertEquals($body, $this->service->getBody());
    }

    /**
     * @test
     * @covers ::getBody
     */
    function it_should_return_body_and_filled_html_part_when_type_is_html()
    {
        $this->setServiceMock(['getRecipients']);
        $email = new Email(
            $this->faker->sentence,
            ['name' => $this->faker->name, 'email' => $this->faker->email],
            ['name' => $this->faker->name, 'email' => $this->faker->email],
            [['name' => $this->faker->name, 'email' => $this->faker->email]],
            $this->faker->sentence,
            self::HTML_TYPE
        );
        $this->setPrivateProperty($this->service, 'email', $email);
        $recipients = [['Name' => $this->faker->name, 'Email' => $this->faker->email]];
        $body = collect([
            'Messages' => [
                [
                    'From' => [
                        'Email' => $email->getFrom()->getEmail(),
                        'Name' => $email->getFrom()->getName(),
                    ],
                    'ReplyTo' => [
                        'Email' => $email->getReply()->getEmail(),
                        'Name' => $email->getReply()->getName(),
                    ],
                    'To' => $recipients,
                    'Subject' => $email->getSubject(),
                    'TextPart' => '',
                    'HTMLPart' => $email->getTemplate()->getContent(),
                ],
            ],
        ]);

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
