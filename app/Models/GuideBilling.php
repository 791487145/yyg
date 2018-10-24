<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GuideBilling
 *
 * @property integer $id
 * @property integer $group_id
 * @property integer $guide_id
 * @property integer $uid
 * @property string $order_no
 * @property string $trade_no
 * @property boolean $in_out 1入账，2出账
 * @property float $amount 金额
 * @property float $return_amount 实际金额
 * @property float $balance 余额
 * @property string $content
 * @property boolean $state -1删除，0待入账，1成功，2失败 11提现审核中   12提现待打款    13提现已打款   14提现已驳回
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereTradeNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereInOut($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereReturnAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property integer $withdraw_id 提现ID
 * @property string $auditor 审核人名
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereWithdrawId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideBilling whereAuditor($value)
 */
class GuideBilling extends Model
{
    protected $table = 'guide_billing';

    const in_income = 1;
    const in_out = 2;

    const state_del = -1;
    const state_nofund = 0;
    const state_fund = 1;
    const state_defeat = 2;
    const no_apply_withdraw_id = 0;
    const state_withdraw_wait_audit = 11;
    const state_withdraw_wait_money = 12;
    const state_withdraw_success = 13;
    const state_withdraw_fail = 14;




    public $timestamps = true;

    protected $fillable = [
        'guide_id',
        'group_id',
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

    //0待入账，1成功，2失败 11审核中
    static public function getStateCN($state){
        $stateArray = array(
            GuideBilling::state_nofund=> '待入账',
            GuideBilling::state_fund=> '成功',
            GuideBilling::state_defeat=> '失败',
            GuideBilling::state_withdraw_wait_audit=> '审核中',
            GuideBilling::state_withdraw_wait_money=> '待打款',
            GuideBilling::state_withdraw_success=> '已打款',
            GuideBilling::state_withdraw_fail=> '已驳回',
        );
        return  isset($stateArray[$state]) ? $stateArray[$state] : '';
    }

    protected $guarded = [];

}