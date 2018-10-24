<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportBindVisitor
 *
 * @property integer $id
 * @property integer $guide_id 导游的id
 * @property string $guide_name 导游的名字
 * @property integer $vistor_num 导游绑定的人数
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindVisitor whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindVisitor whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindVisitor whereGuideName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindVisitor whereVistorNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindVisitor whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindVisitor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReportBindVisitor extends Model
{
    protected $table = 'report_bind_visitor';

    public $timestamps = true;

    protected $fillable = [
        'guide_id',
        'guide_name',
        'vistor_num'
    ];

    protected $guarded = [];




}