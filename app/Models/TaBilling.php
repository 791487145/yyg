<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaBilling
 *
 * @property integer $id
 * @property integer $ta_id
 * @property string $order_no
 * @property string $trade_no
 * @property boolean $in_out 1入账，2出账
 * @property float $amount
 * @property float $balance
 * @property string $content
 * @property boolean $state -1删除，0待入账，1成功，2失败
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereTradeNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereInOut($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float $return_amount 退款金额
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereReturnAmount($value)
 * @property integer $withdraw_id 提现ID
 * @property string $auditor 审核人名
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereWithdrawId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaBilling whereAuditor($value)
 */
class TaBilling extends Model
{
    protected $table = 'ta_billing';

    const in_income = 1;
    const in_out = 2;

    const state_del = -1;
    const state_nofund = 0;
    const state_fund = 1;
    const state_defeat = 2;
    const state_withdraw_wait_audit = 11;
    const state_withdraw_wait_money = 12;
    const state_withdraw_success = 13;
    const state_withdraw_fail = 14;
    
    public $timestamps = true;

    protected $fillable = [
        'ta_id',
        'order_no',
        'trade_no',
        'in_out',
        'amount',
        'balance',
        'content',
        'state',
        'remark'
    ];

    protected $guarded = [];
    static function getStateName($state){
        $stateNames = [
            /* self::state_withdraw_wait_audit => '提现审核中',
            self::state_withdraw_wait_money => '提现待打款',
            self::state_withdraw_success => '提现已打款',
            self::state_withdraw_fail => '提现已驳回', */
            self::state_withdraw_wait_audit => '待审核',
            self::state_withdraw_wait_money => '待打款',
            self::state_withdraw_success => '已打款',
            self::state_withdraw_fail => '已驳回',
        ];
        if (isset($stateNames[$state])){
            return $stateNames[$state];
        }

    }
}