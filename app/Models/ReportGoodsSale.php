<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportGoodsSale
 *
 * @property integer $id
 * @property string $title 商品的名称
 * @property integer $goods_id 商品的id
 * @property integer $order_num 商品的累计订单数 状态1 2 5
 * @property float $goods_sale 商品的累计销售额
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsSale whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsSale whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsSale whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsSale whereOrderNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsSale whereGoodsSale($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsSale whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsSale whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReportGoodsSale extends Model
{
    protected $table = 'report_goods_sale';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'goods_id',
        'order_num',
        'goods_sale'
    ];

    protected $guarded = [];




}