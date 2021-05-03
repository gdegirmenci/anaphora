<?php

namespace Tests\Unit\Factories;

use App\Entities\CampaignEntity;
use App\Factories\ProviderServiceFactory;
use App\Services\Providers\MailJetService;
use App\Services\Providers\SendGridService;
use Tests\Suites\FactoryTestSuite;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Class ProviderServiceFactoryTest
 * @package Tests\Unit\Factories
 * @coversDefaultClass \App\Factories\ProviderServiceFactory
 */
class ProviderServiceFactoryTest extends FactoryTestSuite
{
    use WithFaker;

    const SEND_GRID = 'sendgrid';
    const MAIL_JET = 'mailjet';

    /** @var ProviderServiceFactory */
    private $factory;

    /**
     * @return void
     */
    public function setFactory(): void
    {
        $this->factory = new ProviderServiceFactory();
    }

    /**
     * @return array
     */
    protected function getPayload(): array
    {
        return [
            'name' => $this->faker->word,
            'template' => $this->faker->sentence,
            'type' => $this->faker->word,
            'from' => ['email' => $this->faker->word],
            'reply' => ['email' => $this->faker->word],
            'to' => [['email' => $this->faker->word]],
            'subject' => $this->faker->sentence,
        ];
    }

    /**
     * @test
     * @covers ::make
     */
    function it_should_return_send_grid_service_when_provider_is_send_grid()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);
        $sendGridService = new SendGridService($campaignEntity->getEmail());

        $this->assertEquals($sendGridService, $this->factory->make($campaignEntity, self::SEND_GRID));
    }

    /**
     * @test
     * @covers ::make
     */
    function it_should_return_mail_jet_service_when_provider_is_mail_jet()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);
        $mailJetService = new MailJetService($campaignEntity->getEmail());

        $this->assertEquals($mailJetService, $this->factory->make($campaignEntity, self::MAIL_JET));
    }

    /**
     * @test
     * @covers ::make
     */
    function it_should_throw_an_exception_when_provider_is_not_supported()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);
        $provider = $this->faker->word;

        $this->expectExceptionMessage("Given provider <{$provider}> is not supported.");

        $this->factory->make($campaignEntity, $provider);
    }
}
