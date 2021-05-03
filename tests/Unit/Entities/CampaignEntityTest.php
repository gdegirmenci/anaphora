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

    const DEFAULT_TYPE = 'text';

    /**
     * @return array
     */
    protected function getPayload(): array
    {
        return [
            'name' => $this->faker->word,
            'template' => $this->faker->sentence,
            'type' => self::DEFAULT_TYPE,
            'from' => ['email' => $this->faker->word],
            'reply' => ['email' => $this->faker->word],
            'to' => [['email' => $this->faker->word]],
            'subject' => $this->faker->sentence,
        ];
    }

    /**
     * @test
     * @covers ::getName
     * @covers ::__construct
     */
    function it_should_return_name()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);

        $this->assertEquals(Arr::get($payload, 'name'), $campaignEntity->getName());
    }

    /**
     * @test
     * @covers ::getTemplate
     */
    function it_should_return_template()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);

        $this->assertEquals(Arr::get($payload, 'template'), $campaignEntity->getTemplate());
    }

    /**
     * @test
     * @covers ::getSubject
     */
    function it_should_return_subject()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);

        $this->assertEquals(Arr::get($payload, 'subject'), $campaignEntity->getSubject());
    }

    /**
     * @test
     * @covers ::getFrom
     */
    function it_should_return_from()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);

        $this->assertEquals(Arr::get($payload, 'from'), $campaignEntity->getFrom());
    }

    /**
     * @test
     * @covers ::getReply
     */
    function it_should_return_reply()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);

        $this->assertEquals(Arr::get($payload, 'reply'), $campaignEntity->getReply());
    }

    /**
     * @test
     * @covers ::getType
     */
    function it_should_return_type()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);

        $this->assertEquals(self::DEFAULT_TYPE, $campaignEntity->getType());
    }

    /**
     * @test
     * @covers ::getTo
     */
    function it_should_return_to()
    {
        $payload = $this->getPayload();
        $campaignEntity = new CampaignEntity($payload);

        $this->assertEquals(Arr::get($payload, 'to'), $campaignEntity->getTo());
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
            $campaignEntity->getSubject(),
            $campaignEntity->getFrom(),
            $campaignEntity->getReply(),
            $campaignEntity->getTo(),
            $campaignEntity->getTemplate(),
            $campaignEntity->getType()
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
            'type' => Arr::get($payload, 'type'),
            'to' => Arr::get($payload, 'to'),
        ];

        $this->assertEquals($toSave, $campaignEntity->toSave());
    }
}
