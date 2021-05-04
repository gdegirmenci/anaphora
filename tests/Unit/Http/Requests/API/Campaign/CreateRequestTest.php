<?php

namespace Tests\Unit\Http\Requests\API\Campaign;

use App\Http\Requests\API\Campaign\CreateRequest;
use App\Http\Requests\Request;
use Tests\Suites\RequestTestSuite;

/**
 * Class CreateRequestTest
 * @package Tests\Unit\Http\Requests\API\Campaign
 * @coversDefaultClass \App\Http\Requests\API\Campaign\CreateRequest
 */
class CreateRequestTest extends RequestTestSuite
{
    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return new CreateRequest();
    }

    /**
     * @test
     * @covers ::rules
     * @dataProvider rulesProvider
     * @param string $field
     * @param string $rule
     */
    function it_should_validate_rules(string $field, string $rule)
    {
        $this->assertSame($rule, $this->getRules()[$field]);
    }

    /**
     * @test
     * @covers ::rules
     */
    function it_should_assert_count_validation_rules()
    {
        $this->assertCount(count($this->rulesProvider()), $this->getRules());
    }

    /**
     * @return array
     */
    public function rulesProvider(): array
    {
        return [
            ['name', 'required|string'],
            ['subject', 'required|string'],
            ['from', 'required|array'],
            ['reply', 'required|array'],
            ['to', 'required|array'],
            ['template', 'required|string'],
            ['type', 'required|string'],
        ];
    }
}
