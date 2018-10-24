<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderWx
 *
 * @property integer $id
 * @property integer $uid
 * @property string $order_sn
 * @property string $order_no
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereOrderSn($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderWx whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderWx extends Model
{
    protected $table = 'order_wx';

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'order_sn',
        'order_no'
    ];

    protected $guarded = [];

        
}