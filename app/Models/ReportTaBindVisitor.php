<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportTaBindVisitor
 *
 * @property integer $id
 * @property integer $ta_id 旅行社id
 * @property string $ta_name 旅行社的名称
 * @property integer $vistor_num 导游绑定的人数
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportTaBindVisitor whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportTaBindVisitor whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportTaBindVisitor whereTaName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportTaBindVisitor whereVistorNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportTaBindVisitor whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportTaBindVisitor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReportTaBindVisitor extends Model
{
    protected $table = 'report_ta_bind_visitor';

    public $timestamps = true;

    protected $fillable = [
        'ta_id',
        'ta_name',
        'vistor_num'
    ];

    protected $guarded = [];

  


}