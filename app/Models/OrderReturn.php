<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\OrderReturn
 *
 * @property integer $id
 * @property integer $uid 用户id
 * @property integer $supplier_id 供应商ID
 * @property string $receiver_name
 * @property string $receiver_mobile
 * @property string $order_no 订单编号
 * @property float $amount
 * @property string $return_no 退单编号
 * @property string $return_content 退款说明
 * @property boolean $state 0.待审核 1.审核通过待退款 4.审核驳回 3成功退款
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereReceiverName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereReceiverMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereReturnNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereReturnContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturn whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderReturn extends Model
{
    protected $table = 'order_return';

    const STATE_PAIED = 51;
    const STATE_NO_CHECK = 0;
    const STATE_NO_REFUND = 1;
    const STATE_SUCCESS = 3;
    const STATE_REFUSE = 4;

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'goods_id',
        'supplier_id',
        'spec_id',
        'amount',
        'order_no',
        'return_no',
        'return_content',
        'state'
    ];

    //0.待审核 1.审核通过待退款 4.审核驳回 3成功退款
    static public function getStateCN($state){
        $stateArray = array(
            OrderReturn::STATE_NO_CHECK=> '待审核',
            OrderReturn::STATE_NO_REFUND=> '待退款',
            OrderReturn::STATE_SUCCESS=> '成功退款',
            OrderReturn::STATE_REFUSE=> '审核驳回',
        );
        return  isset($stateArray[$state]) ? $stateArray[$state] : '';
    }


    protected $guarded = [];

        
}