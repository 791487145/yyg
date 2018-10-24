<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderExpress
 *
 * @property integer $id
 * @property string $order_no
 * @property string $express_name
 * @property string $express_no
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderExpress whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderExpress whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderExpress whereExpressName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderExpress whereOrderExpress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderExpress whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderExpress whereUpdatedAt($value)
 */


class OrderExpress extends Model{
    protected $table = 'order_express';
    
    public $timestamps = true;
    
    protected $fillable = [
        'id',
        'order_no',
        'express_name',
        'express_no'
    ];
    
    protected $guarded = [];

}