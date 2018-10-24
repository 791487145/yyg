<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CouponGood
 *
 * @property integer $id
 * @property integer $coupon_id 优惠券ID
 * @property integer $goods_id 商品ID
 * @property integer $supplier_id 供应商ID
 * @property integer $state 状态
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponGood whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponGood whereCouponId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponGood whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponGood whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponGood whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponGood whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CouponGood whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CouponGood extends Model
{
    protected $table = 'coupon_goods';

    const STATE_NORMAL = 1;
    const STATE_DELETE = -1;

    public $timestamps = true;

    protected $fillable = [
        'coupon_id',
        'goods_id',
        'supplier_id',
        'state',
    ];

    protected $guarded = [];
}