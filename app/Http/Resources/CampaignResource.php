<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CampaignResource
 * @package App\Http\Resources
 * @property int id
 * @property string name
 * @property string template
 * @property string type
 * @property int status
 * @property string created_at
 */
class CampaignResource extends JsonResource
{
    /**
     * @param Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'template' => $this->template,
            'type' => $this->type,
            'status' => $this->status,
            'createdAt' => $this->created_at,
        ];
    }
}
