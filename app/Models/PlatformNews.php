<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PlatformNews
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property string $cover
 * @property string $content
 * @property boolean $state 0未发，1已发
 * @property boolean $umeng_response 0未发，1已发
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereUmengResponse($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $send_time
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformNews whereSendTime($value)
 */
class PlatformNews extends Model
{
    protected $table = 'platform_news';

    public $timestamps = true;

    const state_online = 1;

    protected $fillable = [
        'title',
        'url',
        'cover',
        'content',
        'state',
        'umeng_response'
    ];

    protected $guarded = [];

        
}