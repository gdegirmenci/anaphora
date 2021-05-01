<?php

namespace App\Http\Requests\API\Campaign;

use App\Http\Requests\Request;

/**
 * Class CreateRequest
 * @package App\Http\Requests\API\Campaign
 */
class CreateRequest extends Request
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'template' => 'required|string',
            'type' => 'required|string',
        ];
    }
}
