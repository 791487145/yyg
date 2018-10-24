<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderLog
 *
 * @property integer $id
 * @property string $order_no 订单编号
 * @property integer $uid 操作者id
 * @property string $action 操作描述
 * @property string $content json
 * @property \Carbon\Carbon $created_at 建立时间
 * @property \Carbon\Carbon $updated_at 修改时间
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereAction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderLog extends Model
{
    protected $table = 'order_log';

    public $timestamps = true;

    protected $fillable = [
        'order_no',
        'uid',
        'action',
        'content'
    ];

    protected $guarded = [];

        
}