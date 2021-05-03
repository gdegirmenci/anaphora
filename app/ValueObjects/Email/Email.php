<?php

namespace App\ValueObjects\Email;

use App\ValueObjects\Email\Components\Contact;
use App\ValueObjects\Email\Components\Template;
use Illuminate\Support\Arr;

/**
 * Class Email
 * @package App\ValueObjects\Email
 */
final class Email
{
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
     * Email constructor.
     * @param string $subject
     * @param array $from
     * @param array $reply
     * @param array $to
     * @param string $template
     * @param string $type
     */
    public function __construct(
        string $subject,
        array $from,
        array $reply,
        array $to,
        string $template,
        string $type
    ) {
        $this->subject = $subject;
        $this->from = $from;
        $this->reply = $reply;
        $this->to = $to;
        $this->template = $template;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return Contact
     */
    public function getFrom(): Contact
    {
        return new Contact(Arr::get($this->from, 'name'), Arr::get($this->from, 'email'));
    }

    /**
     * @return Contact
     */
    public function getReply(): Contact
    {
        return new Contact(Arr::get($this->reply, 'name'), Arr::get($this->reply, 'email'));
    }

    /**
     * @return array
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @return Template
     */
    public function getTemplate(): Template
    {
        return new Template($this->template, $this->type);
    }
}
