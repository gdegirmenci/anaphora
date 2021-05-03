<?php

namespace Tests\Unit\ValueObjects\Email;

use App\ValueObjects\Email\Components\Contact;
use App\ValueObjects\Email\Components\Template;
use App\ValueObjects\Email\Email;
use Illuminate\Support\Arr;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Class EmailTest
 * @package Tests\Unit\ValueObjects\Email
 * @coversDefaultClass \App\ValueObjects\Email\Email
 */
class EmailTest extends TestCase
{
    use WithFaker;

    /** @var Email */
    private $email;
    /** @var string */
    private $subject;
    /** @var array */
    private $from;
    /** @var array */
    private $reply;
    /** @var array */
    private $to;
    /** @var string */
    private $template;
    /** @var string */
    private $type;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->faker->sentence;
        $this->from = ['name' => $this->faker->name, 'email' => $this->faker->email];
        $this->reply = ['name' => $this->faker->name, 'email' => $this->faker->email];
        $this->to = [['name' => $this->faker->name, 'email' => $this->faker->email]];
        $this->template = $this->faker->sentence;
        $this->type = $this->faker->word;
        $this->email = new Email(
            $this->subject,
            $this->from,
            $this->reply,
            $this->to,
            $this->template,
            $this->type
        );
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getSubject
     */
    function it_should_return_subject()
    {
        $this->assertEquals($this->subject, $this->email->getSubject());
    }

    /**
     * @test
     * @covers ::getFrom
     */
    function it_should_return_from()
    {
        $from = new Contact(Arr::get($this->from, 'name'), Arr::get($this->from, 'email'));

        $this->assertEquals($from, $this->email->getFrom());
    }

    /**
     * @test
     * @covers ::getReply
     */
    function it_should_return_reply()
    {
        $reply = new Contact(Arr::get($this->reply, 'name'), Arr::get($this->reply, 'email'));

        $this->assertEquals($reply, $this->email->getReply());
    }

    /**
     * @test
     * @covers ::getTo
     */
    function it_should_return_to()
    {
        $this->assertEquals($this->to, $this->email->getTo());
    }

    /**
     * @test
     * @covers ::getTemplate
     */
    function it_should_return_template()
    {
        $this->assertEquals(new Template($this->template, $this->type), $this->email->getTemplate());
    }
}
