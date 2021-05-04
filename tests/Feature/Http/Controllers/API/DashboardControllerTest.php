<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Enums\ProviderEnums;
use App\Models\CampaignLog;
use App\ValueObjects\CircuitBreaker\Keys;
use App\ValueObjects\CircuitBreaker\Tracker;
use Tests\TestCase;
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
        factory(CampaignLog::class, $queuedCount)->state('queued')->create();
        // Sent campaigns
        factory(CampaignLog::class, $sentCount)->state('sent')->create();
        // Failed campaigns
        factory(CampaignLog::class, $failedCount)->state('failed')->create();
        $sendGridTracker = new Tracker(new Keys(ProviderEnums::SEND_GRID));
        $mailJetTracker = new Tracker(new Keys(ProviderEnums::MAIL_JET));
        $providerStatus = [
            ['name' => ProviderEnums::SEND_GRID_ALIAS, 'status' => $sendGridTracker->getStatusAlias()],
            ['name' => ProviderEnums::MAIL_JET_ALIAS, 'status' => $mailJetTracker->getStatusAlias()],
        ];
        $dashboardData = [
            'overview' => ['queued' => $queuedCount, 'sent' => $sentCount, 'failed' => $failedCount],
            'providerStatus' => $providerStatus,
        ];

        $response = $this->get(route('get-dashboard'));

        $response->assertOk()->assertExactJson(['data' => $dashboardData]);
    }
}
