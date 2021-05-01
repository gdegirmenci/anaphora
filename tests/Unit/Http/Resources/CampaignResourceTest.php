<?php

namespace Tests\Unit\Http\Resources;

use App\Models\CampaignLog;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
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
    use DatabaseMigrations, WithFaker;

    const STATUS_ALIASES = [
        0 => 'Queue',
        1 => 'Sent',
        2 => 'Failed',
     ];

    /**
     * @test
     * @covers ::toArray
     */
    function it_should_return_array_as_resource()
    {
        /** @var Campaign $campaign */
        $campaign = factory(Campaign::class)->create(['id' => random_int(1, 10), 'created_at' => Carbon::create()]);
        /** @var CampaignLog $campaignLog */
        $campaignLog = factory(CampaignLog::class)->create(['campaign_id' => $campaign->id]);
        $campaignResource = new CampaignResource($campaign);

        $this->assertEquals(
            [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'template' => $campaign->template,
                'status' => self::STATUS_ALIASES[$campaign->status],
                'to' => $campaignLog->to,
                'provider' => Str::ucfirst($campaignLog->provider),
                'date' => $campaign->created_at->toRfc850String(),
            ],
            $campaignResource->resolve()
        );
    }
}
