<?php

namespace App\ValueObjects\Email\Components;

use App\Enums\EmailTypeEnums;
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
     * @return bool
     */
    public function isText(): bool
    {
        return $this->getType() === EmailTypeEnums::TEXT_TYPE;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return ['type' => $this->getType(), 'value' => $this->getContent()];
    }
}
