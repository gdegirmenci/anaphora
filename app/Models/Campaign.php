<?php

namespace App\Models;

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
}
