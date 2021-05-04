<?php

namespace Tests\Unit\Commands;

use App\Entities\CampaignEntity;
use App\Http\Requests\API\Campaign\CreateRequest;
use App\Services\CampaignService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator as Validation;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;
use App\Console\Commands\SendCampaign;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Class SendCampaignTest
 * @package Tests\Unit\Commands
 * @coversDefaultClass \App\Console\Commands\SendCampaign
 */
class SendCampaignTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     * @covers ::handle
     */
    function it_should_return_warning_when_check_payload_is_failed()
    {
        /** @var CampaignService|MockObject $campaignService */
        $campaignService = $this->createMock(CampaignService::class);
        $warningMessage = $this->faker->sentence;
        $checkPayload = ['failed' => true, 'message' => $warningMessage];
        $sendCampaign = $this->getMockBuilder(SendCampaign::class)->onlyMethods(['checkPayload', 'warn'])->getMock();

        $sendCampaign->expects($this->exactly(2))->method('checkPayload')->willReturn($checkPayload);
        $sendCampaign->expects($this->once())->method('warn')->with($warningMessage);

        $sendCampaign->handle($campaignService);
    }

    /**
     * @test
     * @covers ::handle
     */
    function it_should_create_campaign_with_creating_campaign_entity()
    {
        /** @var CampaignService|MockObject $campaignService */
        $campaignService = $this->createMock(CampaignService::class);
        $checkPayload = ['failed' => false];
        /** @var CampaignEntity|MockObject $campaignEntity */
        $campaignEntity = $this->createMock(CampaignEntity::class);
        $sendCampaign = $this->getMockBuilder(SendCampaign::class)
            ->onlyMethods(['checkPayload', 'campaignEntity', 'info'])
            ->getMock();

        $sendCampaign->expects($this->once())->method('checkPayload')->willReturn($checkPayload);
        $sendCampaign->expects($this->once())->method('campaignEntity')->willReturn($campaignEntity);
        $campaignService->expects($this->once())->method('create')->with($campaignEntity);
        $sendCampaign->expects($this->once())->method('info')->with('Campaign is sent.');

        $sendCampaign->handle($campaignService);
    }

    /**
     * @test
     * @covers ::rules
     */
    function it_should_return_rules()
    {
        $request = new CreateRequest();
        $sendCampaign = new SendCampaign();

        $this->assertEquals($request->rules(), $this->invokeMethod($sendCampaign, 'rules'));
    }

    /**
     * @test
     * @covers ::checkPayload
     */
    function it_should_return_failed_when_payload_could_not_be_parsed()
    {
        /** @var SendCampaign|MockObject $sendCampaign */
        $sendCampaign = $this->getMockBuilder(SendCampaign::class)->onlyMethods(['payload'])->getMock();
        $result = ['failed' => true, 'message' => 'Given payload has syntax error.'];

        $sendCampaign->expects($this->once())->method('payload')->willReturn(null);

        $this->assertEquals($result, $this->invokeMethod($sendCampaign, 'checkPayload'));
    }

    /**
     * @test
     * @covers ::checkPayload
     */
    function it_should_return_validation_result_when_payload_could_be_parsed()
    {
        /** @var SendCampaign|MockObject $sendCampaign */
        $sendCampaign = $this->getMockBuilder(SendCampaign::class)->onlyMethods(['payload', 'rules'])->getMock();
        $payload = [$this->faker->word => $this->faker->word];
        $message = $this->faker->sentence;
        $result = ['failed' => true, 'message' => $message];
        $rules = [$this->faker->word => $this->faker->word];
        /** @var Validation|MockObject $validation */
        $validation = $this->createMock(Validation::class);
        /** @var MessageBag|MockObject $messageBag */
        $messageBag = $this->createMock(MessageBag::class);

        $sendCampaign->expects($this->once())->method('payload')->willReturn($payload);
        $sendCampaign->expects($this->once())->method('rules')->willReturn($rules);
        Validator::shouldReceive('make')->once()->with($payload, $rules)->andReturn($validation);
        $validation->expects($this->once())->method('fails')->willReturn(true);
        $validation->expects($this->once())->method('errors')->willReturn($messageBag);
        $messageBag->expects($this->once())->method('first')->willReturn($message);

        $this->assertEquals($result, $this->invokeMethod($sendCampaign, 'checkPayload'));
    }

    /**
     * @test
     * @covers ::payload
     */
    function it_should_return_payload()
    {
        $key = $this->faker->word;
        $value = $this->faker->word;
        $argument = sprintf('{"%s":"%s"}', $key, $value);
        $payload = [$key => $value];
        /** @var SendCampaign|MockObject $sendCampaign */
        $sendCampaign = $this->getMockBuilder(SendCampaign::class)->onlyMethods(['argument'])->getMock();

        $sendCampaign->expects($this->once())->method('argument')->willReturn($argument);

        $this->assertEquals($payload, $this->invokeMethod($sendCampaign, 'payload'));
    }

    /**
     * @test
     * @covers ::campaignEntity
     */
    function it_should_return_campaign_entity()
    {
        $payload = [$this->faker->word => $this->faker->word];
        $campaignEntity = new CampaignEntity($payload);
        /** @var SendCampaign|MockObject $sendCampaign */
        $sendCampaign = $this->getMockBuilder(SendCampaign::class)->onlyMethods(['payload'])->getMock();

        $sendCampaign->expects($this->once())->method('payload')->willReturn($payload);

        $this->assertEquals($campaignEntity, $this->invokeMethod($sendCampaign, 'campaignEntity'));
    }
}
