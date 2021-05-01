<?php

namespace App\Http\Requests\API\Campaign;

use App\Http\Requests\Request;

/**
 * Class PaginateRequest
 * @package App\Http\Requests\API\Campaign
 */
class PaginateRequest extends Request
{
    const DEFAULT_PER_PAGE = 10;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'page' => 'int|min:0',
            'perPage' => 'int|max:50',
        ];
    }

    /**
     * @return int
     */
    public function perPage(): int
    {
        return $this->get('perPage', self::DEFAULT_PER_PAGE);
    }
}
