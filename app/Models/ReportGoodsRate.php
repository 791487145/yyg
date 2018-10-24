<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportGoodsRate
 *
 * @property integer $id
 * @property string $title 商品的名称
 * @property integer $goods_id 商品的id
 * @property integer $aftersale_num 商品的售后订单数
 * @property integer $order_num 商品的累计订单数 状态1 2 5
 * @property float $after_rate 商品的售后率
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsRate whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsRate whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsRate whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsRate whereAftersaleNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsRate whereOrderNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsRate whereAfterRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGoodsRate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReportGoodsRate extends Model
{
    protected $table = 'report_goods_rate';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'goods_id',
        'aftersale_num',
        'order_num',
        'after_rate'
    ];

    protected $guarded = [];




}