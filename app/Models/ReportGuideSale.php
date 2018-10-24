<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportGuideSale
 *
 * @property integer $id
 * @property string $guide_name 导游的姓名
 * @property integer $guide_id 导游的id
 * @property integer $order_num 导游的累计销量
 * @property float $total_sale 导游的累计销售额
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGuideSale whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGuideSale whereGuideName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGuideSale whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGuideSale whereOrderNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGuideSale whereTotalSale($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGuideSale whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportGuideSale whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReportGuideSale extends Model
{
    protected $table = 'report_guide_sale';

    public $timestamps = true;

    protected $fillable = [
        'guide_name',
        'guide_id',
        'order_num',
        'total_sale'
    ];

    protected $guarded = [];




}