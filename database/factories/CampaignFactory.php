<?php

use App\Models\Campaign;
use App\Models\CampaignLog;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * @var Factory $factory
 */
$factory->define(Campaign::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'template' => $faker->text,
        'type' => 'text',
        'status' => random_int(0, 2),
        'created_at' => Carbon::create(),
    ];
});

$factory->define(CampaignLog::class, function (Faker $faker) {
    return [
        'campaign_id' => (factory(Campaign::class)->create())->id,
        'to' => $faker->email,
        'provider' => Arr::random(['sendgrid', 'mailjet']),
    ];
});
