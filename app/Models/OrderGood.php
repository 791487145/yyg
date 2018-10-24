<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderGood
 *
 * @property integer $id
 * @property string $order_no
 * @property integer $goods_id
 * @property string $goods_title
 * @property integer $spec_id
 * @property string $spec_name
 * @property boolean $is_gift
 * @property integer $price 价格
 * @property integer $price_market
 * @property float $price_buying 进价
 * @property float $platform_fee 平台服务费
 * @property float $guide_rate 导游分成
 * @property float $travel_agency_rate 旅行社分成
 * @property integer $num 数量
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereGoodsTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereSpecId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereSpecName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereIsGift($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePriceMarket($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePriceBuying($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePlatformFee($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereGuideRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereTravelAgencyRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereExpressFeeMode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $remark json  商品信息
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderGood whereRemark($value)
 */
class OrderGood extends Model
{
    const is_gift_no = 0;
    const is_gift_yes = 1;

    protected $table = 'order_goods';

    public $timestamps = true;

    protected $fillable = [
        'order_no',
        'goods_id',
        'goods_title',
        'spec_name',
        'spec_id',
        'is_gift',
        'price',
        'price_buying',
        'price_market',
        'platform_fee',
        'guide_rate',
        'travel_agency_rate',
        'num'
    ];

    protected $guarded = [];

        
}