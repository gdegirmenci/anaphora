<?php

namespace App\ValueObjects\Email\Components;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Template
 * @package App\ValueObjects\Email\Components
 */
final class Template implements Arrayable
{
    /** @var string */
    private $content;
    /** @var string */
    private $type;

    /**
     * Template constructor.
     * @param string $content
     * @param string $type
     */
    public function __construct(string $content, string $type)
    {
        $this->content = $content;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return ['type' => $this->getType(), 'value' => $this->getContent()];
    }
}
