<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PlatformSm
 *
 * @property integer $id
 * @property boolean $type 1.商品审核 2.旅行社审核,3.导游审核，4.供应商补交保证金，41.供应商添加，5.售后订单审核,51.售后打款,6.提现审核，61.提现打款, 7.导游分利提醒
 * @property string $mobile
 * @property string $code
 * @property boolean $is_valid -1过期无效，0有效
 * @property string $sid 短信运营商的id
 * @property string $ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereIsValid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereSid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformSm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PlatformSm extends Model
{
    protected $table = 'platform_sms';

    const travelCheck = 2;
    const supplierMoney = 4;
    const supplierAdd = 41;
    const travelAdd = 21;
    const guideAmount = 7;
    //短信催单
    const ORDER_PRESS_SMS = 8;
    const ORDER_NOPAY_SMS = 9;
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