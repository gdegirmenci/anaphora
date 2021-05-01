<?php

namespace Tests\Feature\Http\Controllers\API\Campaign;

use Tests\TestCase;
use App\Models\Campaign;
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

    /**
     * @test
     * @covers ::index
     */
    function it_should_return_campaigns()
    {
        $campaigns = factory(Campaign::class, 2)
            ->create()
            ->transform(function (Campaign $campaign) {
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'template' => $campaign->template,
                    'type' => $campaign->type,
                    'status' => $campaign->status,
                ];
            });

        $response = $this->get(route('get-campaigns', ['perPage' => self::DEFAULT_PER_PAGE]));

        $response->assertOk()->assertJson(['data' => $campaigns->toArray()]);
    }
}
