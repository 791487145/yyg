<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SupplierSm
 *
 * @property integer $id
 * @property boolean $type 2.忘记密码. 3修改密码
 * @property string $mobile
 * @property string $code
 * @property boolean $is_valid -1过期无效，0有效
 * @property string $sid 短信运营商的id
 * @property integer $ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereIsValid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereSid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierSm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SupplierSm extends Model
{
    const SEND_DELIVERY_SMS = 4;

    protected $table = 'supplier_sms';

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