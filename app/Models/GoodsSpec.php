<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GoodsSpec
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $name
 * @property string $pack_num 包装数
 * @property integer $num 数量
 * @property integer $num_sold 已售数量
 * @property integer $num_limit 限购数量
 * @property float $weight 重量
 * @property float $weight_net 净重
 * @property string $long 长
 * @property string $wide 宽
 * @property string $height 高
 * @property float $price 市场价
 * @property float $price_buying 进价
 * @property float $platform_fee 平台服务费
 * @property float $guide_rate 导游分成
 * @property float $travel_agency_rate 旅行社分成
 * @property boolean $express_fee_mode 1包邮 2设置邮费
 * @property boolean $is_pick_up 1支持自提 2不支持自提
 * @property boolean $state
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePackNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereNumSold($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereNumLimit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereWeightNet($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereLong($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereWide($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereHeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePriceBuying($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePlatformFee($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereGuideRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereTravelAgencyRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereExpressFeeMode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereIsPickUp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float $price_market 市场价
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsSpec wherePriceMarket($value)
 */
class GoodsSpec extends Model
{
    protected $table = 'goods_spec';

    public $timestamps = true;

    protected $fillable = [
        'goods_id',
        'name',
        'pack_num',
        'num',
        'num_sold',
        'num_limit',
        'weight',
        'weight_net',
        'long',
        'wide',
        'height',
        'price',
        'price_buying',
        'price_market',
        'platform_fee',
        'guide_rate',
        'travel_agency_rate',
        'express_fee_mode',
        'is_pick_up',
        'state'
    ];

    protected $guarded = [];

    static function goodsSpecPriceCartNum($GoodBases,$open_id)
    {
        foreach($GoodBases as $GoodBase){
            $GoodBase->cover_image = $GoodBase->first_image;
            $UserCarts = UserCart::whereOpenId($open_id)->whereGoodsId($GoodBase->id)->get();
            $GoodBase->cartState = 'btnIconChecked';
            if($UserCarts->isEmpty()){
                $GoodBase->cartState = '';
            }
            $GoodBaseSpec = GoodsSpec::whereGoodsId($GoodBase->id)->first();
            $GoodBase->price = $GoodBaseSpec->price;
            $GoodBase->spec_num = $GoodBaseSpec->num;
            $GoodBase->price_market = empty($GoodBaseSpec->price_market) ? 0 : $GoodBaseSpec->price_market;
        }
        return $GoodBases;
    }

        
}