<?php

namespace App\Models;

use App\Enums\CampaignStatusEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Class CampaignLog
 * @package App\Models
 * @property int id
 * @property int campaign_id
 * @property string to
 * @property string provider
 * @property string status
 * @property Carbon created_at
 * @property Campaign campaign
 * @method CampaignLog|Builder queued()
 * @method CampaignLog|Builder sent()
 * @method CampaignLog|Builder failed()
 */
class CampaignLog extends Model
{
    protected $table = 'campaign_logs';
    protected $fillable = [
        'campaign_id',
        'to',
        'provider',
        'status',
    ];

    /**
     * @return HasOne
     */
    public function campaign(): HasOne
    {
        return $this->hasOne(Campaign::class, 'id', 'campaign_id');
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
