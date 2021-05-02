<?php

use App\Models\Campaign;
use App\Models\CampaignLog;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Arr;

/**
 * @var Factory $factory
 */
$factory->define(Campaign::class, function (Faker $faker) {
    return [
        'name' => $faker->catchPhrase,
        'template' => $faker->text,
        'to' => ['email' => $faker->email, 'name' => $this->faker->name],
        'type' => 'text',
    ];
});

$factory->define(CampaignLog::class, function () {
    return [
        'campaign_id' => (factory(Campaign::class)->create())->id,
        'provider' => Arr::random(['sendgrid', 'mailjet']),
        'status' => random_int(0, 2),
    ];
});

$factory->state(CampaignLog::class, 'queued', function () {
    return [
        'status' => 0,
    ];
});

$factory->state(CampaignLog::class, 'sent', function () {
    return [
        'status' => 1,
    ];
});

$factory->state(CampaignLog::class, 'failed', function () {
    return [
        'status' => 2,
    ];
});
