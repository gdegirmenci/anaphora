<?php

namespace App\ValueObjects\Payloads;

use Illuminate\Support\Arr;

/**
 * Class CampaignPayload
 * @package App\ValueObjects\Payloads
 */
final class CampaignPayload
{
    const DEFAULT_TYPE = 'text';

    private $payload;

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
    public function getType(): string
    {
        return Arr::get($this->payload, 'type') ?? self::DEFAULT_TYPE;
    }

    /**
     * @return array
     */
    public function getTo(): array
    {
        return Arr::get($this->payload, 'to');
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
