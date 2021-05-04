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
            'subject' => 'required|string',
            'from' => 'required|array',
            'reply' => 'required|array',
            'to' => 'required|array',
            'template' => 'required|string',
            'type' => 'required|string',
        ];
    }
}
