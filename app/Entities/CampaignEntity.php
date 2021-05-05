<?php

namespace App\Entities;

use App\Enums\EmailTypeEnums;
use App\ValueObjects\Email\Email;
use Illuminate\Support\Arr;

/**
 * Class CampaignEntity
 * @package App\ValueObjects\Payloads
 */
class CampaignEntity
{
    const TEXT = 'text';

    /** @var int */
    private $campaignId;
    /** @var string */
    private $name;
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
     * CampaignPayload constructor.
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->name = Arr::get($payload, 'name');
        $this->subject = Arr::get($payload, 'subject');
        $this->from = Arr::get($payload, 'from');
        $this->reply = Arr::get($payload, 'reply');
        $this->to = Arr::get($payload, 'to');
        $this->template = Arr::get($payload, 'template');
        $this->type = Arr::get($payload, 'type');
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type === self::TEXT ? EmailTypeEnums::TEXT_TYPE : EmailTypeEnums::HTML_TYPE;
    }

    /**
     * @param int $campaignId
     * @return void
     */
    public function setCampaignId(int $campaignId): void
    {
        $this->campaignId = $campaignId;
    }

    /**
     * @return int
     */
    public function getCampaignId(): int
    {
        return $this->campaignId;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return new Email(
            $this->subject,
            $this->from,
            $this->reply,
            $this->to,
            $this->template,
            $this->getType()
        );
    }

    /**
     * @return array
     */
    public function toSave(): array
    {
        return [
            'name' => $this->name,
            'template' => $this->template,
            'type' => $this->getType(),
            'to' => $this->to,
        ];
    }
}
