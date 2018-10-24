<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SmsVerificationCode
 *
 * @property integer $id
 * @property boolean $type 1.注册，2.忘记密码
 * @property string $mobile
 * @property string $code
 * @property boolean $is_valid -1过期无效，0有效
 * @property string $sid 短信运营商的id
 * @property integer $ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereIsValid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereSid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SmsVerificationCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SmsVerificationCode extends Model
{
    const IS_VALID_YES = 0;
    const IS_VALID_NO = -1;
    const IS_VALID_PASS = 1;

    const TYPE_REGISTER = 1;
    const TYPE_FORGET_PASSWORD = 2;


    protected $table = 'sms_verification_code';

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