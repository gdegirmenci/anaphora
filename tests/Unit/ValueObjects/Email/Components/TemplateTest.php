<?php

namespace Tests\Unit\ValueObjects\Email\Components;

use App\ValueObjects\Email\Components\Template;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Class TemplateTest
 * @package Tests\Unit\ValueObjects\Email\Components
 * @coversDefaultClass \App\ValueObjects\Email\Components\Template
 */
class TemplateTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     * @covers ::getType
     * @covers ::__construct
     */
    function it_should_return_name()
    {
        $type = $this->faker->word;
        $template = new Template($this->faker->sentence, $type);

        $this->assertEquals($type, $template->getType());
    }

    /**
     * @test
     * @covers ::getContent
     */
    function it_should_return_email()
    {
        $content = $this->faker->sentence;
        $template = new Template($content, $this->faker->word);

        $this->assertEquals($content, $template->getContent());
    }

    /**
     * @test
     * @covers ::toArray
     */
    function it_should_return_to_array()
    {
        $value = $this->faker->sentence;
        $type = $this->faker->word;
        $template = new Template($value, $type);

        $this->assertEquals(compact('value', 'type'), $template->toArray());
    }
}
