<?php

namespace App\Http\Resources;

use App\Enums\CampaignStatusEnums;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class CampaignResource
 * @package App\Http\Resources
 * @property int id
 * @property string to
 * @property string provider
 * @property int status
 * @property Carbon created_at
 * @property Campaign campaign
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
            'id' => $this->campaign->id,
            'name' => $this->campaign->name,
            'status' => CampaignStatusEnums::STATUS_ALIASES[$this->status],
            'to' => $this->to,
            'provider' => Str::ucfirst($this->provider),
            'date' => $this->created_at->toRfc850String(),
        ];
    }
}
