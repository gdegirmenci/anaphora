<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Campaign
 * @package App\Models
 * @property int id
 * @property string name
 * @property string template
 * @property string type
 * @property int status
 * @property string created_at
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
}
