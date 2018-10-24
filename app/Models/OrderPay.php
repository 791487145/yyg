<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderPay
 *
 * @property integer $id
 * @property string $order_no
 * @property string $pay_info
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderPay whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderPay whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderPay wherePayInfo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderPay whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderPay whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderPay extends Model
{
    protected $table = 'order_pay';

    public $timestamps = true;

    protected $fillable = [
        'order_no',
        'pay_info'
    ];

    protected $guarded = [];

}