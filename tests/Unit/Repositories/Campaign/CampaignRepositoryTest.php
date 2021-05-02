<?php

namespace Tests\Unit\Repositories\Campaign;

use App\Models\CampaignLog;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
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
    /** @var CampaignLog|Mock */
    private $campaignLog;
    /** @var CampaignRepositoryInterface */
    private $campaignRepository;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->campaign = Mockery::mock(Campaign::class)->makePartial();
        $this->campaignLog = Mockery::mock(CampaignLog::class)->makePartial();
        $this->campaignRepository = new CampaignRepository($this->campaign, $this->campaignLog);
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

        $this->campaignLog->shouldReceive('with')->once()->with('campaign')->andReturnSelf();
        $this->campaignLog->shouldReceive('orderBy')->once()->with('created_at', 'desc')->andReturnSelf();
        $this->campaignLog->shouldReceive('paginate')->once()->with($perPage)->andReturn($paginatorMock);

        $this->assertEquals($paginatorMock, $this->campaignRepository->paginate($perPage));
    }

    /**
     * @test
     * @covers ::create
     */
    function it_should_create()
    {
        $fields = [$this->faker->name => $this->faker->name];

        $this->campaign->shouldReceive('create')->once()->with($fields)->andReturnSelf();

        $this->assertEquals($this->campaign, $this->campaignRepository->create($fields));
    }

    /**
     * @test
     * @covers ::updateCampaignStatus
     */
    function it_should_update_campaign_status()
    {
        $campaignId = random_int(1, 10);
        $provider = $this->faker->word;
        $status = random_int(0, 2);

        $this->campaignLog
            ->shouldReceive('updateOrCreate')
            ->once()
            ->with(['campaign_id' => $campaignId, 'provider' => $provider], compact('status'));

        $this->campaignRepository->updateCampaignStatus($campaignId, $provider, $status);
    }

    /**
     * @test
     * @covers ::totalQueued
     */
    function it_should_return_total_queued()
    {
        $builder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->addMethods(['count'])
            ->getMock();
        $totalQueued = random_int(1, 10);

        $this->campaignLog->shouldReceive('queued')->once()->andReturn($builder);
        $builder->expects($this->once())->method('count')->willReturn($totalQueued);

        $this->assertEquals($totalQueued, $this->campaignRepository->totalQueued());
    }

    /**
     * @test
     * @covers ::totalSent
     */
    function it_should_return_total_sent()
    {
        $builder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->addMethods(['count'])
            ->getMock();
        $totalSent = random_int(1, 10);

        $this->campaignLog->shouldReceive('sent')->once()->andReturn($builder);
        $builder->expects($this->once())->method('count')->willReturn($totalSent);

        $this->assertEquals($totalSent, $this->campaignRepository->totalSent());
    }

    /**
     * @test
     * @covers ::totalFailed
     */
    function it_should_return_total_failed()
    {
        $builder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->addMethods(['count'])
            ->getMock();
        $totalFailed = random_int(1, 10);

        $this->campaignLog->shouldReceive('failed')->once()->andReturn($builder);
        $builder->expects($this->once())->method('count')->willReturn($totalFailed);

        $this->assertEquals($totalFailed, $this->campaignRepository->totalFailed());
    }
}
