<?php

namespace Tests\Unit\ValueObjects\Payloads;

use App\ValueObjects\Payloads\CampaignPayload;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class CampaignPayloadTest
 * @package Tests\Unit\ValueObjects\Payloads
 * @coversDefaultClass \App\ValueObjects\Payloads\CampaignPayload
 */
class CampaignPayloadTest extends TestCase
{
    use WithFaker;

    /**
     * @return array
     */
    protected function getPayload(): array
    {
        return [
            'name' => $this->faker->word,
            'template' => $this->faker->sentence,
            'type' => $this->faker->word,
            'to' => ['email' => $this->faker->word],
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
        $campaignPayload = new CampaignPayload($payload);

        $this->assertEquals(Arr::get($payload, 'name'), $campaignPayload->getName());
    }

    /**
     * @test
     * @covers ::getTemplate
     */
    function it_should_return_template()
    {
        $payload = $this->getPayload();
        $campaignPayload = new CampaignPayload($payload);

        $this->assertEquals(Arr::get($payload, 'template'), $campaignPayload->getTemplate());
    }

    /**
     * @test
     * @covers ::getType
     */
    function it_should_return_type()
    {
        $payload = $this->getPayload();
        $campaignPayload = new CampaignPayload($payload);

        $this->assertEquals(Arr::get($payload, 'type'), $campaignPayload->getType());
    }

    /**
     * @test
     * @covers ::getTo
     */
    function it_should_return_to()
    {
        $payload = $this->getPayload();
        $campaignPayload = new CampaignPayload($payload);

        $this->assertEquals(Arr::get($payload, 'to'), $campaignPayload->getTo());
    }

    /**
     * @test
     * @covers ::toSave
     */
    function it_should_return_to_save()
    {
        $payload = $this->getPayload();
        $campaignPayload = new CampaignPayload($payload);
        $toSave = [
            'name' => Arr::get($payload, 'name'),
            'template' => Arr::get($payload, 'template'),
            'type' => Arr::get($payload, 'type'),
            'to' => Arr::get($payload, 'to'),
        ];

        $this->assertEquals($toSave, $campaignPayload->toSave());
    }
}
