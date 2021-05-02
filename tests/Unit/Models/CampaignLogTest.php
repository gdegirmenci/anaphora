<?php

namespace Tests\Unit\Models;

use App\Models\Campaign;
use App\Models\CampaignLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Suites\ModelTestSuite;

/**
 * Class CampaignTest
 * @package Tests\Unit\Models
 * @coversDefaultClass \App\Models\Campaign
 */
class CampaignLogTest extends ModelTestSuite
{
    const QUEUED = 0;
    const SENT = 1;
    const FAILED = 2;

    /** @var CampaignLog|MockObject */
    private $model;
    /** @var Builder|MockObject */
    private $builder;

    /**
     * @return void
     */
    public function setModel(): void
    {
        $this->model = $this->getMockBuilder(CampaignLog::class)->onlyMethods(['hasOne'])->getMock();
        $this->builder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['where'])
            ->getMock();
    }

    /**
     * @test
     * @covers ::campaign
     */
    function it_should_have_has_one_relation()
    {
        $hasOne = $this->createMock(HasOne::class);

        $this->model
            ->expects($this->once())
            ->method('hasOne')
            ->with(Campaign::class, 'id', 'campaign_id')
            ->willReturn($hasOne);

        $this->assertEquals($hasOne, $this->model->campaign());
    }

    /**
     * @test
     * @covers ::scopeQueued
     */
    function it_should_scope_queued()
    {
        $this->builder->expects($this->once())->method('where')->with('status', self::QUEUED)->willReturnSelf();

        $this->assertEquals($this->builder, $this->model->scopeQueued($this->builder));
    }

    /**
     * @test
     * @covers ::scopeSent
     */
    function it_should_scope_sent()
    {
        $this->builder->expects($this->once())->method('where')->with('status', self::SENT)->willReturnSelf();

        $this->assertEquals($this->builder, $this->model->scopeSent($this->builder));
    }

    /**
     * @test
     * @covers ::scopeFailed
     */
    function it_should_scope_failed()
    {
        $this->builder->expects($this->once())->method('where')->with('status', self::FAILED)->willReturnSelf();

        $this->assertEquals($this->builder, $this->model->scopeFailed($this->builder));
    }
}
