<?php

namespace Tests\Feature\Http\Controllers\API\Campaign;

use App\Jobs\Campaign\CampaignSenderDispatcher;
use App\Models\CampaignLog;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class CampaignControllerTest
 * @package Tests\Feature\Http\Controllers\API\Campaign
 * @coversDefaultClass \App\Http\Controllers\API\Campaign\CampaignController
 */
class CampaignControllerTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    const DEFAULT_PER_PAGE = 10;
    const STATUS_ALIASES = [
        0 => 'Queued',
        1 => 'Sent',
        2 => 'Failed',
    ];

    /**
     * @test
     * @covers ::index
     */
    function it_should_return_campaigns()
    {
        $campaignLogs = factory(CampaignLog::class, 2)
            ->create()
            ->transform(function (CampaignLog $campaignLog) {
                return [
                    'id' => $campaignLog->campaign->id,
                    'name' => $campaignLog->campaign->name,
                    'status' => self::STATUS_ALIASES[$campaignLog->status],
                    'to' => $campaignLog->campaign->to,
                    'provider' => Str::ucfirst($campaignLog->provider),
                    'date' => $campaignLog->created_at->toRfc850String(),
                ];
            });

        $response = $this->get(route('get-campaigns', ['perPage' => self::DEFAULT_PER_PAGE]));

        $response->assertOk()->assertJsonFragment(['data' => $campaignLogs->toArray()]);
    }

    /**
     * @test
     * @covers ::create
     */
    function it_should_create_campaign()
    {
        Queue::fake();
        $requestData = [
            'name' => $this->faker->word,
            'subject' => $this->faker->sentence,
            'from' => ['name' => $this->faker->name, 'email' => $this->faker->email],
            'reply' => ['name' => $this->faker->name, 'email' => $this->faker->email],
            'to' => [['name' => $this->faker->name, 'email' => $this->faker->email]],
            'template' => $this->faker->text,
            'type' => 'text',
        ];
        $response = $this->post(route('create-campaign', $requestData));

        $response->assertOk();
        $this->assertDatabaseHas('campaigns', ['name' => $requestData['name'], 'template' => $requestData['template']]);
        Queue::assertPushed(
            CampaignSenderDispatcher::class,
            function (CampaignSenderDispatcher $campaignSenderDispatcher) {
                $this->assertProperty($campaignSenderDispatcher, 'provider', config('mail.primary_provider'));

                return true;
            }
        );
    }
}
