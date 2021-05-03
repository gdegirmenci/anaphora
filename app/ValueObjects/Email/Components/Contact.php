<?php

namespace App\ValueObjects\Email\Components;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Contact
 * @package App\ValueObjects\Email\Components
 */
final class Contact implements Arrayable
{
    /** @var string */
    private $name;
    /** @var string */
    private $email;

    /**
     * Contact constructor.
     * @param string $name
     * @param string $email
     */
    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return ['name' => $this->getName(), 'email' => $this->getEmail()];
    }
}
