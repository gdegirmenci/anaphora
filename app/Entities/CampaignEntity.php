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

    /** @var array */
    private $payload;
    /** @var int */
    private $campaignId;

    /**
     * CampaignPayload constructor.
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return Arr::get($this->payload, 'name');
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return Arr::get($this->payload, 'template');
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return Arr::get($this->payload, 'subject');
    }

    /**
     * @return array
     */
    public function getFrom(): array
    {
        return Arr::get($this->payload, 'from');
    }

    /**
     * @return array
     */
    public function getReply(): array
    {
        return Arr::get($this->payload, 'reply');
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        if (Arr::get($this->payload, 'type') === self::TEXT) {
            return EmailTypeEnums::TEXT_TYPE;
        }

        return EmailTypeEnums::HTML_TYPE;
    }

    /**
     * @return array
     */
    public function getTo(): array
    {
        return Arr::get($this->payload, 'to');
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
            $this->getSubject(),
            $this->getFrom(),
            $this->getReply(),
            $this->getTo(),
            $this->getTemplate(),
            $this->getType()
        );
    }

    /**
     * @return array
     */
    public function toSave(): array
    {
        return [
            'name' => $this->getName(),
            'template' => $this->getTemplate(),
            'type' => $this->getType(),
            'to' => $this->getTo(),
        ];
    }
}
