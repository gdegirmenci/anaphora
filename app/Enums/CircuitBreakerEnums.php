<?php

namespace App\Enums;

use Illuminate\Support\Carbon;

/**
 * Class CircuitBreakerEnums
 * @package App\Enums
 */
final class CircuitBreakerEnums
{
    const HALF_OPENED = 1;
    const OPENED = 2;
    const INTERVAL = 5;
    const STATUS_TIMEOUT = Carbon::SECONDS_PER_MINUTE * self::INTERVAL;
    const FAILED_COUNT_TIMEOUT = Carbon::SECONDS_PER_MINUTE * self::INTERVAL * 2;
    const CLOSED_ALIAS = 'closed';
    const HALF_OPENED_ALIAS = 'half-opened';
    const OPENED_ALIAS = 'opened';
}
