<?php

namespace Tests\Unit\Http\Resources;

use Tests\TestCase;
use App\Models\Campaign;
use App\Http\Resources\CampaignResource;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Class CampaignResourceTest
 * @package Tests\Unit\Resources
 * @coversDefaultClass \App\Http\Resources\CampaignResource
 */
class CampaignResourceTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     * @covers ::toArray
     */
    function it_should_return_array_as_resource()
    {
        /** @var Campaign $campaign */
        $campaign = factory(Campaign::class)->make(['id' => random_int(1, 10)]);
        $campaignResource = new CampaignResource($campaign);

        $this->assertEquals(
            [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'template' => $campaign->template,
                'type' => $campaign->type,
                'status' => $campaign->status,
                'createdAt' => $campaign->created_at,
            ],
            $campaignResource->resolve()
        );
    }
}
