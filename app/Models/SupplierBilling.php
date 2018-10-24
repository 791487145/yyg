<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SupplierBilling
 *
 * @property integer $id
 * @property integer $supplier_id
 * @property string $order_no
 * @property string $trade_no
 * @property boolean $in_out 1入账，2出账
 * @property float $amount 金额
 * @property float $coupon_amount 优惠金额
 * @property float $express_amount 快递金额
 * @property float $balance 余额
 * @property string $content
 * @property boolean $state -1删除，0待入账，1成功，2失败
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereTradeNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereInOut($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereReturnAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereCouponAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float $return_amount 退款金额
 * @property integer $withdraw_id 提现ID
 * @property string $auditor 审核人名
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereWithdrawId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SupplierBilling whereAuditor($value)
 */
class SupplierBilling extends Model
{
    protected $table = 'supplier_billing';
    //入账出账
    const income = 1;
    const outcome = 2;
    const state_withdraw_enter_account    = 0;
    const state_withdraw_wait_buyer_audit = 11;
    const state_withdraw_wait_finance_audit = 15;
    const state_withdraw_wait_money = 12;
    const state_withdraw_success = 13;
    const state_withdraw_fail = 14;

    public $timestamps = true;

    protected $fillable = [
        'withdraw_id',
        'supplier_id',
        'order_no',
        'trade_no',
        'in_out',
        'amount',
        'return_amount',
        'coupon_amount',
        'express_amount',
        'balance',
        'content',
        'state',
        'remark',
        'auditor'
    ];

    protected $guarded = [];
    static function getStateName($state){
        $stateNames = [
            self::state_withdraw_wait_buyer_audit => '提现审核中',
            self::state_withdraw_wait_money => '提现待打款',
            self::state_withdraw_success => '提现已打款',
            self::state_withdraw_fail => '提现已驳回',
            self::state_withdraw_wait_finance_audit => '提现财务待审核',
            self::state_withdraw_enter_account => '待入账',
        ];
        if (isset($stateNames[$state])){
            return $stateNames[$state];
        }

    }
}