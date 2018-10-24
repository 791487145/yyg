<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CouponBase
 *
 * @property integer $id
 * @property string $title 优惠券名称
 * @property boolean $send_type 1.购买指定商品后发放 2.扫描二维码领取
 * @property integer $supplier_id
 * @property float $amount_order 订单需要满足的金额
 * @property float $amount_coupon 订单优惠金额
 * @property string $start_time 优惠券生效时间
 * @property string $end_time 优惠券失效时间
 * @property boolean $state 1.正常状态 2.暂停使用
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponBase whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponBase whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponBase whereSendType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponBase whereAmountOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponBase whereAmountCoupon($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponBase whereStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponBase whereEndTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponBase whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponBase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CouponBase extends Model
{
    protected $table = 'coupon_base';

    public $timestamps = true;

    const state_normal = 1;
    const state_delete = -1;
    const state_pause = 2;
    protected $fillable = [
        'title',
        'supplier_id',
        'send_type',
        'amount_order',
        'amount_coupon',
        'start_time',
        'end_time',
        'state'
    ];

    protected $guarded = [];


}