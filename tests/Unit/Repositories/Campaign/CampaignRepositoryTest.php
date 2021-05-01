<?php

namespace Tests\Unit\Repositories\Campaign;

use App\Repositories\Campaign\CampaignRepositoryInterface;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;
use App\Models\Campaign;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Campaign\CampaignRepository;

/**
 * Class CampaignRepositoryTest
 * @package Tests\Unit\Repositories\CampaignResource
 * @coversDefaultClass \App\Repositories\Campaign\CampaignRepository
 */
class CampaignRepositoryTest extends TestCase
{
    use WithFaker;

    /** @var Campaign|Mock */
    private $campaign;
    /** @var CampaignRepositoryInterface */
    private $campaignRepository;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->campaign = Mockery::mock(Campaign::class)->makePartial();
        $this->campaignRepository = new CampaignRepository($this->campaign);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::paginate
     */
    function it_should_paginate()
    {
        $perPage = random_int(1, 10);
        $paginatorMock = $this->createMock(LengthAwarePaginator::class);

        $this->campaign->shouldReceive('with')->once()->with('log')->andReturnSelf();
        $this->campaign->shouldReceive('paginate')->once()->with($perPage)->andReturn($paginatorMock);

        $this->assertEquals($paginatorMock, $this->campaignRepository->paginate($perPage));
    }

    /**
     * @test
     * @covers ::create
     */
    function it_should_create()
    {
        $fields = [$this->faker->name => $this->faker->name];

        $this->campaign->shouldReceive('create')->once()->with($fields);

        $this->campaignRepository->create($fields);
    }
}
