<?php

namespace App\Http\Resources;

use App\Enums\CampaignStatusEnums;
use App\Models\CampaignLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class CampaignResource
 * @package App\Http\Resources
 * @property int id
 * @property string name
 * @property string template
 * @property string type
 * @property int status
 * @property Carbon created_at
 * @property CampaignLog log
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
            'status' => CampaignStatusEnums::STATUS_ALIASES[$this->status],
            'to' => $this->log->to,
            'provider' => Str::ucfirst($this->log->provider),
            'date' => $this->created_at->toRfc850String(),
        ];
    }
}
