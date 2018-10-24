<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaLog
 *
 * @property integer $id
 * @property integer $type
 * @property integer $uid
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TaLog extends Model
{
    protected $table = 'ta_log';

    public $timestamps = true;

    protected $fillable = [
        'type',
        'uid',
        'content'
    ];

    protected $guarded = [];

        
}