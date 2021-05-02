<?php

namespace Tests\Feature\Http\Controllers\API;

use Tests\TestCase;
use App\Models\Campaign;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class DashboardControllerTest
 * @package Tests\Feature\Http\Controllers\API
 * @coversDefaultClass \App\Http\Controllers\API\DashboardController
 */
class DashboardControllerTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    const DEFAULT_PER_PAGE = 10;
    const STATUS_ALIASES = [
        0 => 'Queue',
        1 => 'Sent',
        2 => 'Failed',
    ];

    /**
     * @test
     * @covers ::index
     */
    function it_should_return_dashboard_data_with_overview_and_provider_status()
    {
        $queuedCount = random_int(1, 10);
        $sentCount = random_int(1, 10);
        $failedCount = random_int(1, 10);
        // Queued campaigns
        factory(Campaign::class, $queuedCount)->state('queued')->create();
        // Sent campaigns
        factory(Campaign::class, $sentCount)->state('sent')->create();
        // Failed campaigns
        factory(Campaign::class, $failedCount)->state('failed')->create();
        $dashboardData = [
            'overview' => ['queued' => $queuedCount, 'sent' => $sentCount, 'failed' => $failedCount],
            'providerStatus' => ['SendGrid' => 'Closed', 'Mailjet' => 'Open'],
        ];

        $response = $this->get(route('get-dashboard'));

        $response->assertOk()->assertExactJson($dashboardData);
    }
}
