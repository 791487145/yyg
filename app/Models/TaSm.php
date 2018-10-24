<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaSm
 *
 * @property integer $id
 * @property boolean $type 1.注册，2.忘记密码. 3修改密码
 * @property string $mobile
 * @property string $code
 * @property boolean $is_valid -1过期无效，0有效
 * @property string $sid 短信运营商的id
 * @property integer $ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereIsValid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereSid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaSm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TaSm extends Model
{
    protected $table = 'ta_sms';

    public $timestamps = true;

    protected $fillable = [
        'type',
        'mobile',
        'code',
        'is_valid',
        'sid',
        'ip'
    ];

    protected $guarded = [];

        
}