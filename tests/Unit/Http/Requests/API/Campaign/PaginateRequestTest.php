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
    const DEFAULT_PER_PAGE = 10;

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
     * @test
     * @covers ::perPage
     */
    function it_should_return_per_page()
    {
        $perPage = random_int(1, 10);
        $request = new PaginateRequest(compact('perPage'));

        $this->assertEquals($perPage, $request->perPage());
    }

    /**
     * @test
     * @covers ::perPage
     */
    function it_should_return_per_page_as_default_per_page_when_request_does_not_have_per_page()
    {
        $this->assertEquals(self::DEFAULT_PER_PAGE, $this->getRequest()->perPage());
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
