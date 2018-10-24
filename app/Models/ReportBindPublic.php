<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportBindPublic
 *
 * @property integer $id
 * @property integer $guide_id 导游的id
 * @property string $guide_name 导游的名字
 * @property integer $attention_num 绑定的公众号人数
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindPublic whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindPublic whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindPublic whereGuideName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindPublic whereAttentionNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindPublic whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportBindPublic whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReportBindPublic extends Model
{
    protected $table = 'report_bind_public';

    public $timestamps = true;

    protected $fillable = [
        'guide_id',
        'guide_name',
        'attention_num'
    ];

    protected $guarded = [];




}