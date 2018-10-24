<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GoodsExt
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $important_tips 重要提示
 * @property string $send_out_address 发货地
 * @property string $send_out_desc 发货说明
 * @property string $product_area 产地
 * @property string $shelf_life 保质期
 * @property string $pack 包装
 * @property string $store 贮藏
 * @property string $express_desc 快递描述
 * @property string $sold_desc 售后说明
 * @property string $level 等级
 * @property string $product_license 产品许可证
 * @property string $company 公司
 * @property string $dealer 经销商
 * @property string $food_addiitive 食品添加剂
 * @property string $food_burden 食品配料表
 * @property string $address 地址
 * @property string $remark 备注
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereImportantTips($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereSendOutAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereSendOutDesc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereProductArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereShelfLife($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt wherePack($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereStore($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereExpressDesc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereSoldDesc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereLevel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereProductLicense($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereCompany($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereDealer($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereFoodAddiitive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereFoodBurden($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $description 详情
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GoodsExt whereDescription($value)
 */
class GoodsExt extends Model
{
    protected $table = 'goods_ext';

    public $timestamps = true;

    protected $fillable = [
        'goods_id',
        'important_tips',
        'send_out_address',
        'send_out_desc',
        'product_area',
        'shelf_life',
        'pack',
        'store',
        'express_desc',
        'sold_desc',
        'level',
        'product_license',
        'company',
        'dealer',
        'food_addiitive',
        'food_burden',
        'address',
        'description',
        'remark'
    ];

}