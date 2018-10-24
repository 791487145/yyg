<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CouponUser
 *
 * @property integer $id
 * @property integer $uid
 * @property string $open_id
 * @property string $send_source 发送源头
 * @property integer $supplier_id
 * @property integer $coupon_id 优惠券ID
 * @property integer $title 优惠券TITLE
 * @property float $amount_order 订单需超过金额
 * @property float $amount_coupon 订单优惠金额
 * @property string $start_time 优惠券生效时间
 * @property string $end_time 优惠券失效时间
 * @property string $used_time 优惠券使用时间
 * @property boolean $state -1删除 0未使用 1已使用 2过期
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereOpenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereSendSource($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereCouponId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereAmountOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereAmountCoupon($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereEndTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereUsedTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponUser whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CouponUser extends Model
{
    protected $table = 'coupon_user';

    public $timestamps = true;

    const state_delete = -1;
    const state_used = 1;
    const state_unused = 0;
    const state_expired = 2;

    protected $fillable = [
        'uid',
        'send_source',
        'supplier_id',
        'coupon_id',
        'title',
        'amount_order',
        'amount_coupon',
        'start_time',
        'end_time',
        'used_time',
        'state',
        'open_id'
    ];

    protected $guarded = [];

}