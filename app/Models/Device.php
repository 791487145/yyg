<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Device
 *
 * @property integer $id
 * @property integer $uid
 * @property string $app_name
 * @property string $app_version
 * @property string $device_token
 * @property boolean $push_badge 1接收，-1禁止
 * @property boolean $push_alert 1接收，-1禁止
 * @property boolean $push_sound 1接收，-1禁止
 * @property boolean $status 1接收，-1禁止
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereAppName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereAppVersion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereDeviceToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device wherePushBadge($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device wherePushAlert($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device wherePushSound($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Device extends Model
{
    protected $table = 'devices';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'app_name',
        'app_version',
        'device_token',
        'push_badge',
        'push_alert',
        'push_sound',
        'status'
    ];

    protected $guarded = [];

        
}