<?php

namespace Tests\Unit\Models;

use App\Models\Campaign;
use App\Models\CampaignLog;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Suites\ModelTestSuite;

/**
 * Class CampaignTest
 * @package Tests\Unit\Models
 * @coversDefaultClass \App\Models\Campaign
 */
class CampaignTest extends ModelTestSuite
{
    /** @var Campaign|MockObject */
    private $model;

    /**
     * @return void
     */
    public function setModel(): void
    {
        $this->model = $this->getMockBuilder(Campaign::class)->onlyMethods(['hasOne'])->getMock();
    }

    /**
     * @test
     * @covers ::log
     */
    function it_should_have_has_one_relation()
    {
        $hasOne = $this->createMock(HasOne::class);

        $this->model
            ->expects($this->once())
            ->method('hasOne')
            ->with(CampaignLog::class, 'campaign_id', 'id')
            ->willReturn($hasOne);

        $this->assertEquals($hasOne, $this->model->log());
    }
}
