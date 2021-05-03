<?php

namespace Tests\Unit\ValueObjects\Email\Components;

use App\ValueObjects\Email\Components\Contact;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Class ContactTest
 * @package Tests\Unit\ValueObjects\Email\Components
 * @coversDefaultClass \App\ValueObjects\Email\Components\Contact
 */
class ContactTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     * @covers ::getName
     * @covers ::__construct
     */
    function it_should_return_name()
    {
        $name = $this->faker->name;
        $contact = new Contact($name, $this->faker->email);

        $this->assertEquals($name, $contact->getName());
    }

    /**
     * @test
     * @covers ::getEmail
     */
    function it_should_return_email()
    {
        $email = $this->faker->email;
        $contact = new Contact($this->faker->name, $email);

        $this->assertEquals($email, $contact->getEmail());
    }

    /**
     * @test
     * @covers ::toArray
     */
    function it_should_return_to_array()
    {
        $name = $this->faker->name;
        $email = $this->faker->email;
        $contact = new Contact($name, $email);

        $this->assertEquals(compact('name', 'email'), $contact->toArray());
    }
}
