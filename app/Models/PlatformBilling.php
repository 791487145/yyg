<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PlatformBilling
 *
 * @property integer $id
 * @property integer $guide_id
 * @property integer $uid
 * @property string $order_no
 * @property string $trade_no
 * @property boolean $in_out 1入账，2出账
 * @property float $amount 金额
 * @property float $return_amount 退款金额
 * @property float $balance 余额
 * @property string $content
 * @property boolean $state -1删除，0待入账，1成功，2失败
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereTradeNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereInOut($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereReturnAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property integer $withdraw_id 提现ID
 * @property string $auditor 审核人名
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereWithdrawId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PlatformBilling whereAuditor($value)
 */
class PlatformBilling extends Model
{
    protected $table = 'platform_billing';

    public $timestamps = true;

    protected $fillable = [
        'guide_id',
        'uid',
        'order_no',
        'trade_no',
        'in_out',
        'amount',
        'return_amount',
        'balance',
        'content',
        'state',
        'remark'
    ];

    protected $guarded = [];

        
}