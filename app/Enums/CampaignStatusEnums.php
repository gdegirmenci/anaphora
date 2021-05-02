<?php

namespace App\Enums;

/**
 * Class CampaignStatusEnums
 * @package App\Enums
 */
final class CampaignStatusEnums
{
    const QUEUED = 0;
    const SENT = 1;
    const FAILED = 2;
    const STATUS_ALIASES = [
        self::QUEUED => 'Queued',
        self::SENT => 'Sent',
        self::FAILED => 'Failed',
    ];
}
