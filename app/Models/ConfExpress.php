<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfExpress
 *
 * @property integer $id
 * @property string $tel
 * @property string $name
 * @property integer $order_sort
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfExpress whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfExpress whereTel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfExpress whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ConfExpress whereOrderSort($value)
 * @mixin \Eloquent
 */
class ConfExpress extends Model
{
    protected $table = 'conf_express';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'order_sort'
    ];

    protected $guarded = [];
}