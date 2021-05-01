<?php

namespace Tests\Unit\Repositories\Campaign;

use App\Repositories\Campaign\CampaignRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
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

    /** @var Campaign|MockObject */
    private $campaign;
    /** @var CampaignRepositoryInterface */
    private $campaignRepository;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->campaign = $this->getMockBuilder(Campaign::class)->addMethods(['paginate', 'create'])->getMock();
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

        $this->campaign->expects($this->once())->method('paginate')->with($perPage)->willReturn($paginatorMock);

        $this->assertEquals($paginatorMock, $this->campaignRepository->paginate($perPage));
    }

    /**
     * @test
     * @covers ::create
     */
    function it_should_create()
    {
        $fields = [$this->faker->name => $this->faker->name];

        $this->campaign->expects($this->once())->method('create')->with($fields);

        $this->campaignRepository->create($fields);
    }
}
