<?php

namespace Tests\Unit\Http\Requests\API\Campaign;

use App\Http\Requests\API\Campaign\PaginateRequest;
use App\Http\Requests\Request;
use Tests\Suites\RequestTestSuite;

/**
 * Class PaginateRequestTest
 * @package Tests\Unit\Http\Requests\API\Campaign
 * @coversDefaultClass \App\Http\Requests\API\Campaign\PaginateRequest
 */
class PaginateRequestTest extends RequestTestSuite
{
    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return new PaginateRequest();
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
            ['page', 'int|min:0'],
            ['perPage', 'int|max:50'],
        ];
    }
}
