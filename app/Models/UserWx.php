<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserWx
 *
 * @property integer $id
 * @property integer $uid 用户id
 * @property string $open_id wx open id
 * @property string $union_id wx union id
 * @property string $code
 * @property string $avatar
 * @property boolean $sex 1.男，2女
 * @property string $userdata
 * @property integer $ta_id
 * @property integer $guide_id
 * @property integer $remark_name
 * @property integer $ref
 * @property integer $subscribe 1:关注；0：未关注
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereOpenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereUnionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereSex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereUserdata($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereRemarkName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereRef($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereSubscribe($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserWx whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserWx extends Model
{
    protected $table = 'user_wx';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'open_id',
        'union_id',
        'code',
        'avatar',
        'sex',
        'userdata',
        'ta_id',
        'guide_id',
        'remark_name',
        'ref',
        'subscribe'
    ];

    protected $guarded = [];
}