<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfBanner
 *
 * @property integer $id
 * @property integer $pavilion_id
 * @property string $name
 * @property string $cover
 * @property integer $display_order
 * @property string $url
 * @property string $start_time
 * @property string $end_time
 * @property string $state
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereLocation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner wherePavilionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereEndTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property boolean $url_type 0.URL, 1商品ID
 * @property boolean $location 0.微商城, 1app导游
 * @property string $url_content
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereUrlType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfBanner whereUrlContent($value)
 */
class ConfBanner extends Model
{
    protected $table = 'conf_banner';

    const state_online = 1;
    const WAIT_ONLINE = 0;
    const STATE_OFFLINE = 2;
    const state_del = -1;
    const location_wx = 0;
    const location_app = 1;
    //表示地方馆的banner
    const pavilion_id_9999 = 9999;

    public $timestamps = true;

    protected $fillable = [
        'pavilion_id',
        'name',
        'cover',
        'display_order',
        'url',
        'start_time',
        'end_time',
        'state',
        'url_type',
        'url_content',
        'location'
    ];

    protected $guarded = [];

        
}