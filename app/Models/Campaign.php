<?php

namespace App\Models;

use App\Enums\CampaignStatusEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Class Campaign
 * @package App\Models
 * @property int id
 * @property string name
 * @property string template
 * @property string type
 * @property int status
 * @property Carbon created_at
 * @method Campaign|Builder queued()
 * @method Campaign|Builder sent()
 * @method Campaign|Builder failed()
 */
class Campaign extends Model
{
    protected $table = 'campaigns';
    protected $fillable = [
        'name',
        'template',
        'type',
        'status',
    ];

    /**
     * @return HasOne
     */
    public function log(): HasOne
    {
        return $this->hasOne(CampaignLog::class, 'campaign_id', 'id');
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeQueued(Builder $builder): Builder
    {
        return $builder->where('status', CampaignStatusEnums::QUEUED);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeSent(Builder $builder): Builder
    {
        return $builder->where('status', CampaignStatusEnums::SENT);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeFailed(Builder $builder): Builder
    {
        return $builder->where('status', CampaignStatusEnums::FAILED);
    }
}
