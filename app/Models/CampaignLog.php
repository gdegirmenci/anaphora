<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CampaignLog
 * @package App\Models
 * @property int id
 * @property int campaign_id
 * @property string to
 * @property string provider
 */
class CampaignLog extends Model
{
    protected $table = 'campaign_logs';
    protected $fillable = [
        'campaign_id',
        'to',
        'provider',
    ];
}
