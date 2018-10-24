<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportPavilionsSale
 *
 * @property integer $id
 * @property string $name 地方馆的名称
 * @property integer $pavilion_id 地方馆的id
 * @property integer $goods_num 当前地方馆上架的商品数
 * @property integer $order_num 商品的累计订单数 状态1 2 5
 * @property float $total_sale 地方馆的累计销售额
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPavilionsSale whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPavilionsSale whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPavilionsSale wherePavilionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPavilionsSale whereGoodsNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPavilionsSale whereOrderNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPavilionsSale whereTotalSale($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPavilionsSale whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPavilionsSale whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReportPavilionsSale extends Model
{
    protected $table = 'report_pavilions_sale';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'pavilion_id',
        'goods_num',
        'order_num',
        'total_sale'
    ];

    protected $guarded = [];




}