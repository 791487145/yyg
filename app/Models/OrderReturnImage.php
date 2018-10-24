<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderReturnImage
 *
 * @property integer $id
 * @property integer $return_id
 * @property string $name 图片名
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturnImage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturnImage whereReturnId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturnImage whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturnImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderReturnImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderReturnImage extends Model
{
    protected $table = 'order_return_image';

    public $timestamps = true;

    protected $fillable = [
        'return_id',
        'name'
    ];

    protected $guarded = [];

}