<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportPlatformUser
 *
 * @property integer $id
 * @property integer $ta_num 平台当天新增的旅行社数量
 * @property integer $guide_num 平台当天新增的导游数量
 * @property integer $visitor_num 平台当天新增的访问游客数量
 * @property integer $attention_num 平台当天新增关注公众号数量
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPlatformUser whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPlatformUser whereTaNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPlatformUser whereGuideNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPlatformUser whereVisitorNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPlatformUser whereAttentionNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPlatformUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ReportPlatformUser whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReportPlatformUser extends Model
{
    protected $table = 'report_platform_users';

    public $timestamps = true;

    protected $fillable = [
        'ta_num',
        'guide_num',
        'visitor_num',
        'attention_num'
    ];

    protected $guarded = [];




}