<?php

namespace Tests\Unit\Entities;

use App\Entities\CampaignEntity;
use App\ValueObjects\Email\Email;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class CampaignEntityTest
 * @package Tests\Unit\Entities
 * @coversDefaultClass \App\Entities\CampaignEntity
 */
class CampaignEntityTest extends TestCase
{
    use WithFaker;

    const TEXT = 'text';
    const TEXT_TYPE = 'text/plain';
    const HTML_TYPE = 'text/html';

    /**
     * @return array
     */
    protected function getPayload(): array
    {
        return [
            'name' => $this->faker->word,
            'template' => $this->faker->sentence,
            'type' => self::TEXT,
            'from' => ['email' => $this->faker->word],
            'reply' => ['email' => $this->faker->word],
            'to' => [['email' => $this->faker->word]],
            'subject' => $this->faker->sentence,
        ];
    }

    /**
     * @test
     * @covers ::__construct
     */
    function it_should_initialize_properties()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);
        $name = $this->getPrivateProperty($campaignEntity, 'name');
        $subject = $this->getPrivateProperty($campaignEntity, 'subject');
        $from = $this->getPrivateProperty($campaignEntity, 'from');
        $reply = $this->getPrivateProperty($campaignEntity, 'reply');
        $to = $this->getPrivateProperty($campaignEntity, 'to');
        $template = $this->getPrivateProperty($campaignEntity, 'template');
        $type = $this->getPrivateProperty($campaignEntity, 'type');

        $this->assertEquals($name, $payload['name']);
        $this->assertEquals($subject, $payload['subject']);
        $this->assertEquals($from, $payload['from']);
        $this->assertEquals($reply, $payload['reply']);
        $this->assertEquals($to, $payload['to']);
        $this->assertEquals($template, $payload['template']);
        $this->assertEquals($type, $payload['type']);
    }

    /**
     * @test
     * @covers ::getType
     */
    function it_should_return_text_when_type_is_text()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);

        $this->assertEquals(self::TEXT_TYPE, $campaignEntity->getType());
    }

    /**
     * @test
     * @covers ::getType
     */
    function it_should_return_html_when_type_is_not_text()
    {
        $payload = $this->getPayload();
        $payload['type'] = $this->faker->word;
        $campaignEntity = new CampaignEntity($payload);

        $this->assertEquals(self::HTML_TYPE, $campaignEntity->getType());
    }

    /**
     * @test
     * @covers ::setCampaignId
     */
    function it_should_set_campaign_id()
    {
        $campaignId = random_int(1, 10);
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);

        $campaignEntity->setCampaignId($campaignId);

        $this->assertEquals($campaignId, $this->getPrivateProperty($campaignEntity, 'campaignId'));
    }

    /**
     * @test
     * @covers ::getCampaignId
     */
    function it_should_return_campaign_id()
    {
        $campaignId = random_int(1, 10);
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);
        $this->setPrivateProperty($campaignEntity, 'campaignId', $campaignId);

        $this->assertEquals($campaignId, $campaignEntity->getCampaignId());
    }

    /**
     * @test
     * @covers ::getEmail
     */
    function it_should_return_email()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);
        $email = new Email(
            $payload['subject'],
            $payload['from'],
            $payload['reply'],
            $payload['to'],
            $payload['template'],
            'text/plain'
        );

        $this->assertEquals($email, $campaignEntity->getEmail());
    }

    /**
     * @test
     * @covers ::toSave
     */
    function it_should_return_to_save()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);
        $toSave = [
            'name' => Arr::get($payload, 'name'),
            'template' => Arr::get($payload, 'template'),
            'type' => self::TEXT_TYPE,
            'to' => Arr::get($payload, 'to'),
        ];

        $this->assertEquals($toSave, $campaignEntity->toSave());
    }
}
