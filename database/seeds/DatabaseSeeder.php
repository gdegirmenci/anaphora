<?php

use App\Models\Campaign;
use App\Models\CampaignLog;
use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        factory(Campaign::class, 50)
            ->create()
            ->each(function (Campaign $campaign) {
                $logCount = random_int(1, 3);
                factory(CampaignLog::class, $logCount)->create(['campaign_id' => $campaign->id]);
            });
    }
}
