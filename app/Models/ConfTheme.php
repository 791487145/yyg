<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfTheme
 *
 * @property integer $id
 * @property integer $pavilion_id
 * @property integer $location
 * @property integer $display_order
 * @property string $name
 * @property string $content
 * @property string $url
 * @property string $cover
 * @property \Carbon\Carbon $created_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereLocation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme wherePavilionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereCreatedAt($value)
 * @mixin \Eloquent
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereUpdatedAt($value)
 * @property boolean $state
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereState($value)
 * @property boolean $url_type
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfTheme whereUrlType($value)
 */
class ConfTheme extends Model
{
    protected $table = 'conf_theme';

    public $timestamps = true;

    const state_online = 1;
    const state_del = -1;
    const location_wx = 0;//微商城
    const location_app = 1;

    protected $fillable = [
        'pavilion_id',
        'display_order',
        'name',
        'content',
        'url',
        'cover',
        'location'
    ];

    protected $guarded = [];

        
}