<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GuideTum
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $guide_id
 * @property integer $ta_id
 * @property string $name
 * @property string $mobile
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereId($value)
 * * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GuideTum whereUid($value)
 */
class GuideTum extends Model
{
    protected $table = 'guide_ta';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'guide_id',
        'ta_id',
        'name',
        'mobile',
        'vistors_num',
        'total_sales',
    ];

    protected $guarded = [];



}