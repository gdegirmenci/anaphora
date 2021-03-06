<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Campaign
 * @package App\Models
 * @property int id
 * @property string name
 * @property string template
 * @property array to
 * @property string type
 */
class Campaign extends Model
{
    protected $table = 'campaigns';
    protected $fillable = [
        'name',
        'template',
        'to',
        'type',
    ];
    protected $casts = ['to' => 'array'];
}
