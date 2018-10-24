<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GoodsBase
 *
 * @property integer $id
 * @property string $title
 * @property integer $supplier_id 供应商ID
 * @property integer $category_id 品类id
 * @property integer $pavilion 所属馆
 * @property integer $location
 * @property integer $location_order
 * @property string $cover 封面图
 * @property string $first_image 封面图
 * @property boolean $state -1删除 0未审核 1.上架 2下架 3驳回 4售罄
 * @property integer $num 库存数量
 * @property integer $num_sold 已销数量
 * @property integer $num_browse 浏览量
 * @property float $guide_amount 导游反复金额
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase wherePavilion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereCover($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereFirstImage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereNumSold($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereLocation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereLocationOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereNumBrowse($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereGuideAmount($value)
 * @property integer $num_favorite 收藏数量
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsBase whereNumFavorite($value)
 * @mixin \Eloquent
 */
class GoodsBase extends Model
{
    const state_online = 1;
    const state_check = 0;
    const state_delete = -1;
    const state_down = 2;
    const state_return = 3;
    const state_finish = 4;

    const location_index_recommend = 1;

    protected $table = 'goods_base';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'supplier_id',
        'category_id',
        'pavilion',
        'location',
        'location_order',
        'cover',
        'first_image',
        'state',
        'num',
        'num_sold',
        'state'
    ];

    protected $guarded = [];

    /**
     * 获取商品
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    static function getGoods($goods){
        if ($goods){
            //获取轮播图
            $images = GoodsImage::whereGoodsId($goods->id)->get();
            $goods->images = [];
            if(!$images->isEmpty()){
                $goods->images = GoodsImage::whereGoodsId($goods->id)->get();
            }
            //获取分类
            $goods->conf_categories = ConfCategory::all();
            //运营分类
            $goods_categories = GoodsCategory::whereGoodsId($goods->id)->get();
            foreach ($goods_categories as $goods_category) {
                $category[] = $goods_category->category_id;
            }
            $goods->goods_category = [];
            if(isset($category)){
                $goods->goods_category = $category;
            }
            //获取场馆
            $goods->pavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
            //获取属性、规格、赠品
            $goods->ext  = GoodsExt::whereGoodsId($goods->id)->first();
            $goods->spec = GoodsSpec::whereGoodsId($goods->id)->get();
            $gift = GoodsGift::whereGoodsId($goods->id)->get();
            $goods->gift = [];
            if(!$gift->isEmpty()){
                $goods->gift = GoodsGift::whereGoodsId($goods->id)->get();
            }
        }
        return $goods;

    }



        
}