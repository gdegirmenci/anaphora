<?php

use App\Models\CampaignLog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        factory(CampaignLog::class, 50)->create();
    }
}
