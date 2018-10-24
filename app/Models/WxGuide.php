<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class WxGuide
 *
 * @property integer $id
 * @property string $order_no
 * @property string $ref
 * @property string $open_id wx open id
 * @property integer $guide_id
 * @property boolean $state
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxGuide whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxGuide whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxGuide whereRef($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxGuide whereOpenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxGuide whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxGuide whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxGuide whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WxGuide whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WxGuide extends Model
{
    const STATE_YES = 1;
    const STATE_DEL = -1;

    protected $table = 'wx_guide';

    public $timestamps = true;

    protected $fillable = [
        'order_no',
        'open_id',
        'guide_id',
        'state',
        'ref'
    ];
    protected $guarded = [];
}